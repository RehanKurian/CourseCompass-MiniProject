<?php
session_start();
$home_link = (isset($_SESSION['email']) && isset($_SESSION['role'])) ? 'home.php' : 'index.php';

include "navbar.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CourseCompass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="about.css">
</head>
<body>
   
 <!-- About Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">About <span class="gradient-text">CourseCompass</span></h1>
                <p class="hero-description">
                    Navigating your learning journey with purpose and clarity
                </p>
            </div>
        </div>
        
    </section>

    <!-- Mission Section -->
    <section class="mission">
        <div class="container">
            <div class="mission-content">
                <div class="mission-text">
                    <h2 class="section-title">Our Mission</h2>
                    <p class="mission-description">
                        CourseCompass was created specifically for <strong>students</strong> and <strong>working professionals</strong> 
                        who are passionate about upskilling themselves but often find themselves lost in the overwhelming 
                        world of online education.
                    </p>
                    <p class="mission-description">
                        We understand the struggle of not knowing where to start, which course to choose, or how to 
                        align your learning goals with your career aspirations. That's why we built a platform that 
                        takes the guesswork out of course selection.
                    </p>
                </div>
                <div class="mission-visual">
                    <div class="visual-card">
                        <div class="visual-icon">🎯</div>
                        <h3>Personalized Guidance</h3>
                        <p>Tailored recommendations based on your unique goals and interests</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="problem">
        <div class="container">
            <div class="problem-grid">
                <div class="problem-content">
                    <h2 class="section-title">The Challenge We Solve</h2>
                    <div class="challenge-list">
                        <div class="challenge-item">
                            <div class="challenge-icon">😕</div>
                            <div class="challenge-text">
                                <h3>Overwhelming Options</h3>
                                <p>Thousands of courses across multiple platforms make it difficult to choose the right one</p>
                            </div>
                        </div>
                        <div class="challenge-item">
                            <div class="challenge-icon">❓</div>
                            <div class="challenge-text">
                                <h3>Unclear Learning Paths</h3>
                                <p>Students and professionals struggle to identify which skills will benefit their career goals</p>
                            </div>
                        </div>
                        <div class="challenge-item">
                            <div class="challenge-icon">⏰</div>
                            <div class="challenge-text">
                                <h3>Time Constraints</h3>
                                <p>Busy schedules make it hard to find courses that fit your available time commitment</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="problem-visual">
                    <div class="stats-card">
                        <div class="stat">
                            <div class="stat-number">78%</div>
                            <div class="stat-label">of learners feel overwhelmed by course options</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">65%</div>
                            <div class="stat-label">struggle to find relevant courses</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">52%</div>
                            <div class="stat-label">abandon courses due to poor fit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Solution Section -->
    <section class="solution">
        <div class="container">
            <h2 class="section-title">How CourseCompass Helps</h2>
            <div class="solution-grid">
                <div class="solution-card">
                    <div class="solution-icon">📝</div>
                    <h3>Smart Questionnaire</h3>
                    <p>Our interactive questionnaire understands your interests, skill level, and time availability to create a personalized learning profile.</p>
                </div>
                <div class="solution-card">
                    <div class="solution-icon">🤖</div>
                    <h3>Intelligent Recommendations</h3>
                    <p>Advanced algorithms match you with courses that align perfectly with your goals and learning preferences.</p>
                </div>
                <div class="solution-card">
                    <div class="solution-icon">🔍</div>
                    <h3>Curated Course Catalog</h3>
                    <p>Browse through carefully selected courses from top platforms like Coursera, Udemy, and more, all in one place.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Target Audience Section -->
    <section class="audience">
        <div class="container">
            <h2 class="section-title">Who We Serve</h2>
            <div class="audience-cards">
                <div class="audience-card student-card">
                    <div class="card-icon">🎓</div>
                    <h3>Students</h3>
                    <p>
                        Preparing for the industry and looking to enhance your resume with valuable certifications? 
                        We help you identify courses that will make you more attractive to employers and give you 
                        a competitive edge in placements.
                    </p>
                    <ul class="benefits-list">
                        <li>Industry-relevant skill development</li>
                        <li>Placement preparation support</li>
                        <li>Career-focused course recommendations</li>
                    </ul>
                </div>
                <div class="audience-card professional-card">
                    <div class="card-icon">💼</div>
                    <h3>Working Professionals</h3>
                    <p>
                        Already in the workforce but want to stay ahead of the curve? We understand your time 
                        constraints and help you find courses that fit your schedule while adding valuable 
                        skills to your professional toolkit.
                    </p>
                    <ul class="benefits-list">
                        <li>Flexible learning schedules</li>
                        <li>Career advancement opportunities</li>
                        <li>Skill gap identification and filling</li>
                    </ul>
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
                    <a class="btn btn-primary btn-large" href="quiz.php">
                        Take the Questionnaire
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12,5 19,12 12,19"></polyline>
                        </svg>
                    </a>
                    <a class="btn btn-outline-white btn-large" href="courses.php">Browse Courses</a>
                </div>
            </div>
        </div>
    </section>
    <?php include "footer.php"; ?>
</body>
</html>
