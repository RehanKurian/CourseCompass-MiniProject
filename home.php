<?php
session_start();
// Clear browser cache to prevent back button access
//header is used to set HTTP headers that control caching behavior
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");//this line prevents the browser from caching the page
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");// this line sets the expiration date of the page to a date in the past
header("Pragma: no-cache");//this line is used for HTTP/1.0 compatibility

// Check if user is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
include  'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseCompass </title>
    <link rel="stylesheet" href="home.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Navigate Your
                    <span class="gradient-text">Learning Journey</span>
                </h1>
                <p class="hero-description">
                    Discover personalized certification courses that align with your career goals. 
                    From college placements to professional advancement, CourseCompass guides you 
                    to the right courses on top platforms like Coursera, Udemy, and more.
                </p>
                <div class="hero-buttons">
                        <a class="btn-primary btn-large" href="quiz.php">
                           <span>Take The Questionnaire</span>
                            <svg width="25" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" >
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12,5 19,12 12,19"></polyline>
                            </svg>
                        </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10,00+</div>
                    <div class="stat-label">Courses Available</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Placement Success</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Industry Partners</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support Available</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Why Choose CourseCompass?</h2>
                <p class="section-description">
                    Our intelligent platform combines personalization with comprehensive course discovery 
                    to accelerate your learning journey.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polygon points="10,8 16,12 10,16 10,8"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Personalized Recommendations</h3>
                    <p class="feature-description">
                        Get course suggestions tailored to your skill level, interests, and career goals through our smart questionnaire.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Comprehensive Course Catalog</h3>
                    <p class="feature-description">
                        Access courses from top platforms like Coursera, Udemy, and more, all in one place with advanced filtering.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">For Students & Professionals</h3>
                    <p class="feature-description">
                        Whether you're preparing for placements or advancing your career, find the right certification courses.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">How CourseCompass Works</h2>
                <p class="section-description">Simple steps to discover your perfect course</p>
            </div>
            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Take the Quiz</h3>
                    <p class="step-description">Answer questions about your interests, skill level, and career goals</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Get Recommendations</h3>
                    <p class="step-description">Receive personalized course suggestions from top platforms</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Start Learning</h3>
                    <p class="step-description">Enroll in courses and track your progress toward your goals</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Target Audience Section -->
    <section id="about" class="target-audience">
        <div class="container">
            <div class="audience-grid">
                <div class="audience-content">
                    <h2 class="section-title">Built for Your Success</h2>
                    <p class="audience-description">
                        CourseCompass is specifically designed for college students preparing for placements 
                        and working professionals looking to advance their careers. Our platform understands 
                        the unique challenges you face in today's competitive job market.
                    </p>
                    <div class="checklist">
                        <div class="check-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>Industry-relevant certifications</span>
                        </div>
                        <div class="check-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>Placement-focused skill development</span>
                        </div>
                        <div class="check-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>Career advancement opportunities</span>
                        </div>
                        <div class="check-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>Flexible learning schedules</span>
                        </div>
                    </div>
                </div>
                <div class="audience-cards">
                    <div class="audience-card student-card">
                        <h3>For Students</h3>
                        <p>Get industry-ready with certifications that matter for placements</p>
                        <ul class="skills-list">
                            <li>Web Development</li>
                            <li>Data Science</li>
                            <li>Cloud Computing</li>
                            <li>Digital Marketing</li>
                        </ul>
                        <a class="btn btn-secondary" href="courses.php">Explore Student Paths</a>
                    </div>
                    <div class="audience-card professional-card">
                        <h3>For Professionals</h3>
                        <p>Advance your career with strategic skill enhancement</p>
                        <ul class="skills-list">
                            <li>Leadership & Management</li>
                            <li>AI & Machine Learning</li>
                            <li>Project Management</li>
                            <li>Cybersecurity</li>
                        </ul>
                        <a class="btn btn-secondary" href="courses.php">Explore Professional Paths</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Discover Your Perfect Course?</h2>
                <p class="cta-description">
                    Join thousands of students and professionals who have accelerated their careers with CourseCompass
                </p>
                <div class="cta-buttons">
                    <a class="btn btn-secondary btn-large" href="quiz.php">
                        Take the Questionnaire
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12,5 19,12 12,19"></polyline>
                        </svg>
                    </a>
                    <a class="btn btn-outline-white btn-large" href="courses.php">Browse Courses</a>
                </div>
            </div>
        </div>
    </section>

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
                <p>&copy; 2024 CourseCompass. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
