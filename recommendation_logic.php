<?php
include 'db.php';
function calculateAndStoreRecommendations($conn, $userId)
{

    // --- Step 1: Calculate User's Tag Scores ---
    $sql_user_scores = "
        SELECT 
            otw.tag_id, 
            SUM(otw.weight) AS total_weight
        FROM responses r
        JOIN option_tag_weights otw ON r.option_id = otw.option_id
        WHERE r.user_id = ?
        GROUP BY otw.tag_id
    ";

    $stmt = $conn->prepare($sql_user_scores);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $userTagScores = [];
    while ($row = $result->fetch_assoc()) {
        $userTagScores[$row['tag_id']] = $row['total_weight'];
    }

    if (empty($userTagScores)) {
        return;
    }

    // --- Step 2: Find Potentially Relevant Courses ---
    $tagIds = implode(',', array_keys($userTagScores));
    $sql_course_tags = "
        SELECT 
        ct.course_id, 
        ct.tag_id
        FROM course_tags ct
        WHERE ct.tag_id IN ($tagIds)
    ";

    $result_courses = $conn->query($sql_course_tags);

    // --- Step 3: Calculate Final Score for Each Course ---
    $recommendationScores = [];
    while ($row = $result_courses->fetch_assoc()) {
        $courseId = $row['course_id'];
        $tagId = $row['tag_id'];

        $scoreForThisTag = $userTagScores[$tagId];

        if (!isset($recommendationScores[$courseId])) {
            $recommendationScores[$courseId] = 0;
        }
        $recommendationScores[$courseId] += $scoreForThisTag;
    }

    arsort($recommendationScores);

    // --- Step 4: Clear Old and Insert New Recommendations ---
    $stmt_delete = $conn->prepare("DELETE FROM recommendations WHERE user_id = ?");
    $stmt_delete->bind_param("i", $userId);
    $stmt_delete->execute();

    $sql_insert = "INSERT INTO recommendations (user_id, course_id, score) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    foreach ($recommendationScores as $courseId => $score) {
        $stmt_insert->bind_param("iii", $userId, $courseId, $score);
        $stmt_insert->execute();
    }
}
?>