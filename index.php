<?php
include "db.php";
// 1. Query for Popular Categories
// Counts courses in each category and shows the top 5.
$sql_categories = "
    SELECT category, COUNT(*) as course_count 
    FROM courses 
    GROUP BY category 
    ORDER BY course_count DESC 
    LIMIT 5;
";
$result_categories = $conn->query($sql_categories);
$popular_categories = $result_categories->fetch_all(MYSQLI_ASSOC);


// 2. Query for Top-Rated Courses
// This query now serves as the only source for the "Top Courses" section.
$sql_top_courses = "
    SELECT title, platform, level, description, rating, duration, url
    FROM courses
    ORDER BY rating DESC
    LIMIT 3;
";
$result_top_courses = $conn->query($sql_top_courses);
$top_courses = $result_top_courses->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseCompass - Discover Your Perfect Course</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="logo">🧭</div>
                <span class="brand-text">CourseCompass</span>
            </div>
            <div class="nav-links">
                <a href="about.php" class="btn">About</a>
                <a href="login.php" class="btn">Courses</a>
                <button type="submit" class="btn btn-primary" style="margin-left: 10px;" onclick="location.href='login.php'">Sign In</button>
                <a href="login.php" class="profile-logo" title="Profile">
                   <img src="profileicon.svg" alt="profile icon" style="width: 24px; height: 24px;">
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Discover Your Perfect Course</h1>
                <p class="hero-description">
                    Explore thousands of certification courses from top platforms. Find courses tailored to your goals,
                    skill level, and schedule.
                </p>
                <div class="search-container">
                    <input type="text" class="search-box" placeholder="Search for courses, skills, or topics...">
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            
            <!-- DYNAMIC Course Categories Section -->
            <section class="categories" id="categories">
                <h2 class="section-title">Popular Categories</h2>
                <p class="section-subtitle">Choose from our most popular skill categories and start your learning journey today</p>
                <div class="category-grid">
                    <?php if (!empty($popular_categories)): ?>
                        <?php 
                        $category_icons = ["💻", "📊", "🎨", "📱", "🔒", "☁️"];
                        $icon_index = 0;
                        ?>
                        <?php foreach ($popular_categories as $category): ?>
                            <div class="category-card fade-in">
                                <span class="category-icon"><?php echo $category_icons[$icon_index % count($category_icons)]; ?></span>
                                <h3><?php echo htmlspecialchars($category['category']); ?></h3>
                                <p><?php echo htmlspecialchars($category['course_count']); ?>+ courses</p>
                            </div>
                            <?php $icon_index++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No categories found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- DYNAMIC Top Courses Section -->
            <section class="top-courses" id="courses">
                <h2 class="section-title">Trending Courses</h2>
                <p class="section-subtitle">Handpicked courses that are highly rated by our community.</p>
                <div class="course-grid">
                    <?php if (!empty($top_courses)): ?>
                        <?php foreach ($top_courses as $course): ?>
                            <div class="course-card fade-in">
                                <div class="course-image">🎓</div>
                                <div class="course-content">
                                    <div class="course-meta">
                                        <span class="platform-badge"><?php echo htmlspecialchars($course['platform']); ?></span>
                                        <span class="difficulty-badge"><?php echo htmlspecialchars($course['level']); ?></span>
                                    </div>
                                    <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                                    <p class="course-description"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                                    <div class="course-stats">
                                        <div class="rating">
                                            <span class="stars">★★★★★</span>
                                            <span><?php echo htmlspecialchars($course['rating']); ?></span>
                                        </div>
                                        <div class="duration"><?php echo htmlspecialchars($course['duration']); ?></div>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($course['url']); ?>" class="course-btn" target="_blank">Start Learning</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No top courses to display at the moment.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Learning Paths -->
            <section class="learning-paths" id="paths">
                <h2 class="section-title">Guided Learning Paths</h2>
                <p class="section-subtitle">Structured learning journeys designed to take you from beginner to expert</p>
                <div class="path-grid">
                    <div class="path-card fade-in">
                        <div class="path-icon">🚀</div>
                        <h3 class="path-title">Full-Stack Developer</h3>
                        <p class="path-description">Complete journey from frontend to backend development with modern technologies and frameworks.</p>
                        <p class="path-courses">8 courses • 240 hours • Beginner to Advanced</p>
                    </div>
                    <div class="path-card fade-in">
                        <div class="path-icon">🧠</div>
                        <h3 class="path-title">Data Scientist</h3>
                        <p class="path-description">Master data analysis, machine learning, and AI to become a skilled data professional.</p>
                        <p class="path-courses">6 courses • 180 hours • Intermediate to Advanced</p>
                    </div>
                    <div class="path-card fade-in">
                        <div class="path-icon">🎯</div>
                        <h3 class="path-title">Product Manager</h3>
                        <p class="path-description">Learn product strategy, user research, and agile methodologies to lead successful products.</p>
                        <p class="path-courses">5 courses • 120 hours • Beginner to Intermediate</p>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="cta-section">
                <h2>Ready to Start Your Journey?</h2>
                <p>Join thousands of learners who have transformed their careers with CourseCompass</p>
                <div class="cta-buttons">
                    <a href="login.php" class="btn btn-secondary">Take the Quiz</a>
                    <a href="login.php" class="btn btn-secondary">Browse All Courses</a>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <div class="logo">🧭</div>
                        <span class="brand-text">CourseCompass</span>
                    </div>
                    <p class="footer-description">
                        Guiding your learning journey with personalized course recommendations.
                    </p>
                </div>
                <div class="footer-column">
                    <h3 class="footer-title">Platform</h3>
                    <ul class="footer-links">
                        <li><a href="#">How it Works</a></li>
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-title">Support</h3>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-title">Company</h3>
                    <ul class="footer-links">
                        <li><a href="#">About</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 CourseCompass. All rights reserved.</p>
            </div>
        </div>
    </footer>

   <script>


        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase();
            if (query.length > 0) {
                // Add search suggestions or filtering logic here
                console.log('Searching for:', query);
            }
        });

        // Course card interactions
        document.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Category card click handlers
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function () {
                const category = this.querySelector('h3').textContent;
                console.log('Category selected:', category);
                // Add navigation to category page logic here
            });
        });

        // Course button interactions
        document.querySelectorAll('.course-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const courseTitle = this.closest('.course-card').querySelector('.course-title').textContent;
                console.log('Course selected:', courseTitle);
                // Add course enrollment logic here
            });
        });

        // Add pulse animation to CTA buttons
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('mouseenter', function () {
                this.classList.add('pulse');
            });

            btn.addEventListener('mouseleave', function () {
                this.classList.remove('pulse');
            });
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // Add any initialization code here
            console.log('CourseCompass Landing Page loaded');
        });
    </script>
</body>

</html>
