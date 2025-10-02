<?php
session_start();
include 'db.php';

// --- User Authentication ---
// Check if the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['user_id'];

// --- Fetch Recommendations ---
// This SQL query retrieves the top 10 recommendations for the logged-in user,
// ordered by score in descending order. It joins with the 'courses' table
// to get all the details needed for the display cards.
$sql = "
    SELECT 
        c.course_id, c.title, c.platform, c.duration, c.level, c.url, 
        c.description, c.rating, c.price, c.category
    FROM recommendations r
    JOIN courses c ON r.course_id = c.course_id
    WHERE r.user_id = ?
    ORDER BY r.score DESC
    LIMIT 10;
";

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); 
$stmt->execute();
$result = $stmt->get_result();
$recommendations = $result->fetch_all(MYSQLI_ASSOC);

// Close the database connection
$stmt->close();
$conn->close();

// Include the navigation bar
include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recommendations - CourseCompass</title>
    
    <!-- Link to Google Fonts for consistent typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Internal CSS for styling the page and course cards -->
    <style>
        /* --- Base and Layout Styles --- */
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f8ff 0%, #ffffff 50%, #f8f0ff 100%);
            color: #333;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        main {
            padding-top: 80px; 
        }

        .page-header {
            text-align: center;
            padding: 3rem 0;
            color: #1a1a1a;
        }
        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .page-header p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        /* --- Grid for Course Cards --- */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        /* --- Individual Course Card Styling --- */
        .course-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(59, 130, 246, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: box-shadow 0.3s, transform 0.3s;
            position: relative;
        }
        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }
        .course-card:hover::before {
            transform: scaleX(1);
        }
        .course-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            flex-shrink: 0;
        }
        .course-content {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .course-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .platform-badge,
        .difficulty-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .difficulty-badge {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        .course-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1f2937;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .course-description {
            color: #6b7280;
            line-height: 1.6;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .course-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        .rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .stars {
            color: #fbbf24;
            font-size: 1rem;
        }
        .rating-text,
        .duration {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .duration {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
    
        .course-btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: auto;
            box-sizing: border-box;
        }
        .course-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
        }

        /* --- "No Recommendations" Block --- */
        .no-recommendations {
            text-align: center;
            padding: 4rem 2rem;
            border-radius: 16px;
            margin: 2rem auto;
            max-width: 700px;
        }
        .no-recommendations h2 {
            font-size: 2rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        .no-recommendations p {
            color: #6b7280;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .no-recommendations .quiz-btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .no-recommendations .quiz-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* --- Responsive Styles --- */
        @media (max-width: 1200px) {
            .container {
                max-width: 98vw;
                padding: 10px;
            }
            .courses-grid {
                gap: 1.2rem;
                padding: 1.2rem 0;
            }
        }

        @media (max-width: 992px) {
            .page-header h1 {
                font-size: 2.2rem;
            }
            .courses-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .course-image {
                height: 140px;
                font-size: 2rem;
            }
            .course-content {
                padding: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            main {
                padding-top: 60px;
            }
            .container {
                padding: 6px;
            }
            .page-header {
                padding: 2rem 0;
            }
            .page-header h1 {
                font-size: 1.5rem;
            }
            .courses-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 1rem 0;
            }
            .course-card {
                min-width: 0;
            }
            .course-content {
                padding: 0.8rem;
            }
            .course-title {
                font-size: 1.1rem;
            }
            .course-btn {
                font-size: 0.95rem;
                padding: 10px 12px;
            }
            .no-recommendations {
                padding: 2rem 0.5rem;
                max-width: 98vw;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 2px;
            }
            .page-header {
                padding: 1rem 0;
            }
            .page-header h1 {
                font-size: 1.1rem;
            }
            .courses-grid {
                gap: 0.7rem;
                padding: 0.5rem 0;
            }
            .course-image {
                height: 80px;
                font-size: 1.2rem;
            }
            .course-content {
                padding: 10px;
            }
            .course-description{
                font-size: 13px;
            }
            .course-title {
                font-size: 0.95rem;
            }
            .course-btn {
                font-size: 0.85rem;
                padding: 8px 8px;
            }
            .no-recommendations {
                padding: 1rem 0.2rem;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <?php if (!empty($recommendations)): ?>
                <div class="page-header">
                    <h1>My Recommendations</h1>
                    <p>Here are your personalized course recommendations based on your quiz answers.</p>
                </div>
                <div class="courses-grid">
                    <?php foreach ($recommendations as $course): ?>
                        <div class="course-card fade-in" data-category="<?= htmlspecialchars($course['category']) ?>">
                            <div class="course-image">📚</div>
                            <div class="course-content">
                                <div class="course-meta">
                                    <span class="platform-badge"><?= htmlspecialchars($course['platform']) ?></span>
                                    <span class="difficulty-badge"><?= htmlspecialchars($course['level']) ?></span>
                                </div>
                                <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                                <p class="course-description"><?= htmlspecialchars($course['description']) ?></p>
                                <div class="course-stats">
                                    <div class="rating">
                                        <span class="stars">
                                            <?= str_repeat('★', floor($course['rating'] ?? 0)) ?>
                                            <?= str_repeat('☆', 5 - floor($course['rating'] ?? 0)) ?>
                                        </span>
                                        <span class="rating-text"><?= htmlspecialchars($course['rating'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="duration">⏱️ <?= htmlspecialchars($course['duration']) ?></div>
                                    <div class="price">₹<?= htmlspecialchars($course['price'] ?? 'Free') ?></div>
                                </div>
                                <a href="<?= htmlspecialchars($course['url']) ?>" class="course-btn" target="_blank">
                                    Enroll Now
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- If no recommendations are found, display this message -->
                <div class="no-recommendations">
                    <h2>No Recommendations Found</h2>
                    <p>You don't have any recommendations yet. Take our quick quiz to discover courses tailored just for you!</p>
                    <a href="quiz.php" class="quiz-btn">Take the Quiz Now</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
