<?php
include 'db.php';
session_start();

// Redirect to login page if the user is not authenticated
$userId = $_SESSION['user_id'] ?? 0;
if ($userId === 0) {
    header("Location: login.php");
    exit();
}

// --- Fetch Recommended Courses ---
$sql = "
    SELECT 
    c.course_id, c.title, c.platform, c.duration, c.level, c.url, c.description, c.rating, c.price, c.category
    FROM recommendations r
    JOIN courses c ON r.course_id = c.course_id
    WHERE r.user_id = ?
    ORDER BY r.score DESC
    LIMIT 10     ;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$recommendations = $result->fetch_all(MYSQLI_ASSOC);

// Close the database resources
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
    <title>Your Course Recommendations</title>

    <style>
        body {
            font-family:, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 2.8em;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header p {
            font-size: 1.2em;
            color: #7f8c8d;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Original "No Results" Message Styling */
        .no-results {
            text-align: center;
            padding: 60px 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .no-results h2 {
            color: #e74c3c;
            margin-bottom: 15px;
        }

        .no-results p {
            color: #555;
            margin-bottom: 25px;
        }

        .no-results a {
            display: inline-block;
            padding: 12px 25px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .no-results a:hover {
            background-color: #2980b9;
        }

        /* --- Copied Styles for Course Cards from courses.css --- */

        /* Grid Layout */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            font-family: 'Inter', sans-serif;
            /* Use the font from the cards */
        }

        /* Course Card Container */
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

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }

        /* Top highlight bar on hover */
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

        .course-card:hover::before {
            transform: scaleX(1);
        }

        /* Card Image Area */
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

        /* Card Content Area */
        .course-content {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        /* Meta Badges (Platform, Level) */
        .course-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
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
        }

        .difficulty-badge {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        /* Title and Description */
        .course-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-top: 4px;
            margin-bottom: 4px;
            color: #1f2937;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-description {
            color: #6b7280;
            margin-bottom: 10px;
            line-height: 1.6;
            flex-grow: 1;
            /* Allows description to fill space */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Stats (Rating, Duration, Price) */
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

        .course-btn {
            display: block;
            /* Ensures the 'a' tag behaves like a block element */
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
            margin-top: auto;
            /* Pushes button to the bottom */
            box-sizing: border-box;
        }

        .course-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Your Personalized Recommendations</h1>
            <p>Based on your quiz answers, here are the top courses we think you'll love.</p>
        </div>

        <!-- Check if there are any recommendations to display -->
        <?php if (!empty($recommendations)): ?>
            <!-- The 'courses-grid' class will arrange the cards correctly -->
            <div class="courses-grid">
                <!-- Loop through each course and create a card -->
                <?php foreach ($recommendations as $course): ?>
                    <div class="course-card fade-in" data-category="<?= htmlspecialchars($course['category'] ?? 'General') ?>">
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
                                <div class="price">
                                    ₹<?= htmlspecialchars($course['price'] ?? 'Free') ?>
                                </div>
                            </div>
                            <a href="<?= htmlspecialchars($course['url']) ?>" class="course-btn" target="_blank">
                                Enroll Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Display the original "no results" message if no courses are found -->
            <div class="no-results">
                <h2>No Recommendations Found</h2>
                <p>We couldn't generate recommendations based on your answers. Please try taking the quiz again.</p>
                <a href="quiz.php">Retake the Quiz</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript for the fade-in animation on the cards -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.course-card').forEach(card => {
                observer.observe(card);
            });
        });
    </script>
</body>

</html>