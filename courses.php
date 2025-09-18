<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
require_once "db.php";

// --- Get the active category from the URL, default to 'all' ---
$active_category = isset($_GET['category']) ? $_GET['category'] : 'all';

// --- Router for AJAX 'Load More' requests ---
if (isset($_GET['action']) && $_GET['action'] === 'load_more') {

    // The number of courses already loaded on the page.
    $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
    $limit = 15; // Number of courses to fetch each time.

    // Base SQL query
    $query = "SELECT * FROM courses";
    $params = [];
    $types = "";

    // Add a WHERE clause if a category is active for the AJAX call
    if ($active_category !== 'all') {
        $query .= " WHERE category = ?";
        $params[] = $active_category;
        $types .= "s";
    }

    $query .= " ORDER BY course_id ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    try {
        $stmt = $conn->prepare($query);
        // Dynamically bind parameters
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $courses = [];
        if ($result) {
            $courses = $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    
        // Get the total count for the current filter
        $count_sql = "SELECT COUNT(*) as total FROM courses";
        $count_params = [];
        $count_types = "";
        if ($active_category !== 'all') {
            $count_sql .= " WHERE category = ?";
            $count_params[] = $active_category;
            $count_types .= "s";
        }
        $count_stmt = $conn->prepare($count_sql);
        if (!empty($count_params)) {
            $count_stmt->bind_param($count_types, ...$count_params);
        }
        $count_stmt->execute();
        $total_courses = $count_stmt->get_result()->fetch_assoc()['total'];
        $count_stmt->close();
        $conn->close();

        header('Content-Type: application/json');
        echo json_encode(['courses' => $courses, 'total_courses' => $total_courses]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit();
}

// --- Initial Page Load Block ---
include "navbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// List of categories for the filter buttons
$course_categories = [
    "AI", "AI / Programming", "Business", "Cloud", "Computer Science", "Data Analytics", 
    "Data Science", "Finance", "Frontend", "Game Development", "IT & Python", "Learning Skills", 
    "Machine Learning", "Marketing", "Networking", "Personal Development", "Programming", 
    "UX Design", "Web Development"
];
sort($course_categories);

// Initial query now respects the active category filter
$initial_limit = 15;
$sql = "SELECT * FROM courses";
$count_sql = "SELECT COUNT(*) as total FROM courses";
$params = [];
$types = "";

if ($active_category !== 'all') {
    $sql .= " WHERE category = ?";
    $count_sql .= " WHERE category = ?";
    $params[] = $active_category;
    $types .= "s";
}

// Get total count for the active category for the "Load More" button
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_courses = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fetch the initial set of courses for the page
$sql .= " ORDER BY course_id ASC LIMIT ?";
$params[] = $initial_limit;
$types .= "i";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - CourseCompass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="courses.css">

    <!-- CSS for the Course Details Dialog Box (Modal) -->
    <style>
        .modal-overlay {
            display: none; 
            position: fixed; 
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: slide-down 0.4s ease-out;
        }
        @keyframes slide-down {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-close {
            color: #aaa;
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            transition: color 0.3s;
            cursor: pointer;
        }
        .modal-close:hover,
        .modal-close:focus {
            color: #000;
            text-decoration: none;
        }
        .modal-header h2 {
            margin-top: 0;
            color: #1f2937;
            font-size: 1.8em;
        }
        .modal-body {
            margin-top: 20px;
            line-height: 1.7;
            color: #6b7280;
        }
        .modal-footer {
            margin-top: 30px;
            text-align: right;
        }
        .modal-footer .course-btn {
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <main class="main-content">
        <div class="page-header"></div>
        <div class="search-filter-section">
            <div class="container">
                <div class="search-container">
                    <input type="text" class="search-box" placeholder="Search courses..." id="searchInput">
                </div>
                <div class="filter-section">
                    <a href="courses.php" class="filter-btn <?= $active_category === 'all' ? 'active' : '' ?>">All Courses</a>
                    <?php foreach ($course_categories as $category) {
                        $is_active = ($active_category === $category);
                        $link = "courses.php?category=" . urlencode($category);
                        echo '<a href="' . $link . '" class="filter-btn ' . ($is_active ? 'active' : '') . '">' . htmlspecialchars($category) . '</a>';
                    } ?>
                </div>
            </div>
        </div>

        <div class="courses-section">
            <div class="container">
                <h2 class="section-title">
                    <?= $active_category === 'all' ? 'All Courses' : htmlspecialchars($active_category) . ' Courses' ?>
                </h2>
                <div class="courses-grid" id="coursesGrid">
                    <!-- Display initial courses fetched by PHP -->
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card fade-in"
                             data-title="<?= htmlspecialchars($course['title']) ?>"
                             data-description="<?= htmlspecialchars($course['description']) ?>"
                             data-url="<?= htmlspecialchars($course['url']) ?>"
                             data-platform="<?= htmlspecialchars($course['platform']) ?>"
                             data-level="<?= htmlspecialchars($course['level']) ?>"
                             data-duration="<?= htmlspecialchars($course['duration']) ?>">
                            <div class="course-image">📚</div>
                            <div class="course-content">
                                <div class="course-meta">
                                    <span class="platform-badge"><?= htmlspecialchars($course['platform']) ?></span>
                                    <span class="difficulty-badge"><?= htmlspecialchars($course['level']) ?></span>
                                </div>
                                <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                                <p class="course-description"><?= htmlspecialchars(($course['description'])) ?></p>
                                <div class="course-stats">
                                    <div class="rating">
                                        <span class="stars">
                                            <?= str_repeat('★', floor($course['rating'] ?? 0)) ?>
                                            <?= str_repeat('☆', 5 - floor($course['rating'] ?? 0)) ?>
                                        </span>
                                        <span class="rating-text"><?= htmlspecialchars($course['rating'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="duration">⏱️ <?= htmlspecialchars($course['duration']) ?></div>
                                    <div class="price">₹<?= htmlspecialchars($course['price']) ?></div>
                                </div>
                               <a href="<?= htmlspecialchars($course['url']) ?>" class="course-btn" target="_blank">Enroll Now</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Show "Load More" button only if there are more courses to load -->
                <?php if ($total_courses > count($courses)): ?>
                    <div class="load-more-section">
                        <button id="loadMoreBtn" class="load-more-btn">Load More</button>
                    </div>
                <?php endif; ?>

                <div id="no-results-message" style="text-align: center; padding: 2rem; <?= empty($courses) ? 'display: block;' : 'display: none;' ?>">
                    <p>No courses found matching your criteria.</p>
                </div>
            </div>
        </div>
    </main>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const coursesGrid = document.getElementById('coursesGrid');
    const noResults = document.getElementById('no-results-message');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    let courseCards = () => document.querySelectorAll('.course-card');

    // Search functionality
    searchInput.addEventListener('input', function (e) {
        const searchValue = e.target.value.toLowerCase();
        let visibleCount = 0;
        courseCards().forEach(course => {
            const title = course.querySelector('.course-title').textContent.toLowerCase();
            const desc = course.querySelector('.course-description').textContent.toLowerCase();
            const shouldShow = title.includes(searchValue) || desc.includes(searchValue);
            course.style.display = shouldShow ? 'flex' : 'none';
            if (shouldShow) visibleCount++;
        });
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    });

    // Intersection Observer for fade-in animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    }, { threshold: 0.1 });
    courseCards().forEach(card => observer.observe(card));

    // Load More functionality
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            this.disabled = true;
            this.innerHTML = '<span class="loading"></span> Loading...';

            const offset = courseCards().length;
            const currentCategory = new URLSearchParams(window.location.search).get('category') || 'all';
            const fetchURL = `courses.php?action=load_more&category=${encodeURIComponent(currentCategory)}&offset=${offset}`;

            fetch(fetchURL)
                .then(response => response.json())
                .then(data => {
                    if (data.courses && data.courses.length > 0) {
                        data.courses.forEach(course => {
                            const courseCard = document.createElement('div');
                            courseCard.className = 'course-card fade-in';
                            courseCard.setAttribute('data-title', course.title);
                            courseCard.setAttribute('data-description', course.description);
                            courseCard.setAttribute('data-url', course.url);
                            courseCard.setAttribute('data-platform', course.platform);
                            courseCard.setAttribute('data-level', course.level);
                            courseCard.setAttribute('data-duration', course.duration);

                            courseCard.innerHTML = `
                                <div class="course-image">📚</div>
                                <div class="course-content">
                                    <div class="course-meta">
                                        <span class="platform-badge">${course.platform}</span>
                                        <span class="difficulty-badge">${course.level}</span>
                                    </div>
                                    <h3 class="course-title">${course.title}</h3>
                                    <p class="course-description">${course.description}</p>
                                    <div class="course-stats">
                                        <div class="rating">
                                            <span class="stars">
                                                ${'★'.repeat(Math.floor(course.rating || 0))}
                                                ${'☆'.repeat(5 - Math.floor(course.rating || 0))}
                                            </span>
                                            <span class="rating-text">${course.rating || ''}</span>
                                        </div>
                                        <div class="duration">⏱️ ${course.duration}</div>
                                        <div class="price">₹${course.price}</div>
                                    </div>
                                  <a href="${course.url}" class="course-btn" target="_blank">Enroll Now</a>
                                </div>
                            `;
                            coursesGrid.appendChild(courseCard);
                            observer.observe(courseCard);
                        });
                        this.disabled = false;
                        this.innerHTML = 'Load More';
                    }
                    const totalDisplayed = document.querySelectorAll('.course-card').length;
                    if (typeof data.total_courses !== 'undefined' && totalDisplayed >= data.total_courses) {
                        this.closest('.load-more-section').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading more courses:', error);
                    this.disabled = false;
                    this.innerHTML = 'Load More (Error)';
                });
        });
    }

});
</script>
</body>
</html>
