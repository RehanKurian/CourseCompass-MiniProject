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
            <button class="navbar-toggle" id="navbarToggle">&#9776;</button>
            <div class="nav-links" id="navLinks">
                <a href="about.php" class="btn">About</a>
                <a href="contactus.php" class="btn">Contact Us</a>
                <a href="courses.php" class="btn">Courses</a>
                <button type="submit" class="btn btn-primary" style="margin-left: 10px;"
                    onclick="location.href='login.php'">Sign In</button>
                <a href="login.php" class="profile-logo" title="Profile">
                    <img src="profileicon.svg" alt="profile icon" style="width: 24px; height: 24px;">
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-background"> </div>

        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        Navigate Your Way to
                        <span class="gradient-text typing-animation" id="typingText">Success</span>
                    </h1>
                    <p class="hero-description">
                        Join over <strong>50,000+ learners</strong> who have transformed their careers with our
                        intelligent course recommendation system. Find your perfect learning path in seconds.
                    </p>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number" data-target="15000">0</div>
                            <div class="stat-label">Courses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-target="500">0</div>
                            <div class="stat-label">Partners</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-target="98">0</div>
                            <div class="stat-label">% Success Rate</div>
                        </div>
                    </div>

                    <div class="hero-actions">
                        <a class="cta-primary" id="findCoursesBtn" href="login.php">
                            <span>Find My Perfect Course</span>
                            <div class="btn-glow"></div>
                        </a>
                    </div>

                </div>

                <div class="hero-visual">
                    <div class="compass-container">
                        <div class="compass-outer">
                            <div class="compass-inner">
                                <div class="compass-needle" id="compassNeedle"></div>
                                <div class="compass-center"></div>
                                <div class="compass-directions">
                                    <span class="direction north">💻</span>
                                    <span class="direction east">📊</span>
                                    <span class="direction south">🎨</span>
                                    <span class="direction west">🚀</span>
                                </div>
                            </div>
                        </div>
                        <div class="compass-glow"></div>
                    </div>

                    <div class="course-cards-preview">
                        <div class="preview-card card-1">
                            <div class="card-icon">💻</div>
                            <div class="card-title">Web Dev</div>
                            <div class="card-rating">⭐ 4.9</div>
                        </div>
                        <div class="preview-card card-2">
                            <div class="card-icon">📊</div>
                            <div class="card-title">Data Science</div>
                            <div class="card-rating">⭐ 4.8</div>
                        </div>
                        <div class="preview-card card-3">
                            <div class="card-icon">🎨</div>
                            <div class="card-title">UI/UX</div>
                            <div class="card-rating">⭐ 4.7</div>
                        </div>
                    </div>
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
                <p class="section-subtitle">Choose from our most popular skill categories and start your learning
                    journey today</p>
                <div class="category-grid">
                    <?php if (!empty($popular_categories)): ?>
                        <?php
                        $category_icons = ["💻"];
                        $icon_index = 0;
                        ?>
                        <?php foreach ($popular_categories as $category): ?>
                            <div class="category-card fade-in">
                                <span
                                    class="category-icon"><?php echo $category_icons[$icon_index % count($category_icons)]; ?></span>
                                <h3 style="font-weight: 600; font-size: 17px"><?php echo htmlspecialchars($category['category']); ?></h3>
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
                                    <p class="course-description">
                                        <?php echo htmlspecialchars($course['description']); ?></p>
                                    <div class="course-stats">
                                        <div class="rating">
                                            <span class="stars">★★★★★</span>
                                            <span><?php echo htmlspecialchars($course['rating']); ?></span>
                                        </div>
                                        <div class="duration"><?php echo htmlspecialchars($course['duration']); ?></div>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($course['url']); ?>" class="course-btn"
                                        target="_blank">Start Learning</a>
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
                <p class="section-subtitle">Structured learning journeys designed to take you from beginner to expert
                </p>
                <div class="path-grid">
                    <div class="path-card fade-in">
                        <div class="path-icon">🚀</div>
                        <h3 class="path-title">Full-Stack Developer</h3>
                        <p class="path-description">Complete journey from frontend to backend development with modern
                            technologies and frameworks.</p>
                        <p class="path-courses">8 courses • 240 hours • Beginner to Advanced</p>
                    </div>
                    <div class="path-card fade-in">
                        <div class="path-icon">🧠</div>
                        <h3 class="path-title">Data Scientist</h3>
                        <p class="path-description">Master data analysis, machine learning, and AI to become a skilled
                            data professional.</p>
                        <p class="path-courses">6 courses • 180 hours • Intermediate to Advanced</p>
                    </div>
                    <div class="path-card fade-in">
                        <div class="path-icon">🎯</div>
                        <h3 class="path-title">Product Manager</h3>
                        <p class="path-description">Learn product strategy, user research, and agile methodologies to
                            lead successful products.</p>
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

    <?php include 'footer.php'; ?>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Typing animation for the hero title
    const typingTexts = ['Success', 'Excellence', 'Growth', 'Innovation', 'Mastery'];
    let textIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    const typingElement = document.getElementById('typingText');

    function typeText() {
        const currentText = typingTexts[textIndex];

        if (isDeleting) {
            typingElement.textContent = currentText.substring(0, charIndex - 1);
            charIndex--;
        } else {
            typingElement.textContent = currentText.substring(0, charIndex + 1);
            charIndex++;
        }

        if (!isDeleting && charIndex === currentText.length) {
            setTimeout(() => isDeleting = true, 2000);
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % typingTexts.length;
        }

        const typingSpeed = isDeleting ? 100 : 150;
        setTimeout(typeText, typingSpeed);
    }

    setTimeout(typeText, 1000);

    // Counter animation for statistics
    function animateCounter(element, target) {
        const increment = target / 100;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            element.textContent = Math.floor(current);

            if (current >= target) {
                element.textContent = target + (target === 98 ? '%' : '+');
                clearInterval(timer);
            }
        }, 30);
    }

    setTimeout(() => {
        document.querySelectorAll('.stat-number').forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            animateCounter(stat, target);
        });
    }, 2000);

    // Compass needle interaction
    const compassNeedle = document.getElementById('compassNeedle');
    let mouseX = 0;
    let mouseY = 0;

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        if (compassNeedle && compassNeedle.closest('.compass-container')) {
            const compassRect = compassNeedle.closest('.compass-container').getBoundingClientRect();
            const compassCenterX = compassRect.left + compassRect.width / 2;
            const compassCenterY = compassRect.top + compassRect.height / 2;

            const angle = Math.atan2(mouseY - compassCenterY, mouseX - compassCenterX);
            const degrees = (angle * 180 / Math.PI) + 90;

            compassNeedle.style.transform = `rotate(${degrees}deg)`;
        }
    });

    // Button interactions
    const findCoursesBtn = document.getElementById('findCoursesBtn');
    if (findCoursesBtn) {
        findCoursesBtn.addEventListener('click', (event) => {
            // Add ripple effect
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(102, 126, 234, 0.3);
                width: 4px;
                height: 4px;
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;

            const rect = findCoursesBtn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';

            findCoursesBtn.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);

            // Navigate to courses (you can replace this with actual navigation)
            console.log('Navigating to courses...');
        });
    }

    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

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

    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
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

    const toggleBtn = document.getElementById('navbarToggle');
    const navLinks = document.getElementById('navLinks');
    toggleBtn.addEventListener('click', function() {
        navLinks.classList.toggle('show');
    });
});
</script>
</body>
</html>