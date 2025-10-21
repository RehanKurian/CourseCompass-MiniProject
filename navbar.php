<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e5e5e5;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1350px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Make the brand clickable and preserve layout */
        .brand-link {
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: inherit;
        }

        .brand-link:hover .brand-text,
        .brand-link:focus .brand-text {
            color: #667eea;
        }

        .logo {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .logo img,
        .logo svg {
            width: 28px;
            height: 28px;
            display: block;
            fill: #3b82f6;
            transition: fill 0.2s;
        }

        .brand-text {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-left: auto;
        }

        .nav-links a {
            text-decoration: none;
            white-space: nowrap;
            color: #777373;
            font-weight: 500;
            padding: 12px 12px;
            border-radius: 8px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            position: relative;
            transition: background 0.3s, color 0.3s;
        }

        .nav-links a.active,
        .nav-links a:hover {
            color: #667eea;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 60%;
        }

        .btn-primary {
            padding: 12px 24px;
            font-size: 14px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .profile-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 16px;
            width: 28px;
            height: 28px;
            cursor: pointer;
            text-decoration: none;
            background: none;
            border-radius: 0;
            box-shadow: none;
            overflow: visible;
            transition: background 0.2s;
        }

        .profile-logo img,
        .profile-logo svg {
            width: 28px;
            height: 28px;
            display: block;
            fill: #3b82f6;
            transition: fill 0.2s;
        }

        .profile-logo:hover img {
            fill: #613bb8ff;
        }

        .profile-logo::after {
            content: none !important;
        }

        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 2rem;
            color: #3b82f6;
            cursor: pointer;
            margin-left: 16px;
            z-index: 1002;
        }

        @media (max-width: 846px) {
            .navbar-toggle {
                display: flex !important;
            }

            .nav-links {
                position: absolute;
                top: 56px;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                flex-direction: column;
                gap: 0;
                box-shadow: 0 4px 16px rgba(59, 130, 246, 0.08);
                display: none;
                z-index: 1001;
            }

            .nav-links.show {
                display: flex;
            }

            .nav-links a,
            .nav-links button {
                padding: 16px 24px;
                border-radius: 0;
                border-bottom: 1px solid #eee;
                width: 100%;
                justify-content: flex-start;
                font-size: 1rem;
            }

            .nav-links {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links button.btn-primary {
                width: 100%;
                margin: 0;
                border-radius: 0;
                padding: 16px 24px;
                border-bottom: 1px solid #eee;
                font-size: 1rem;
                text-align: left;
                justify-content: flex-start;
                display: flex;
            }

            .profile-logo {
                width: 100%;
                margin: 0;
                margin-top: 8px;
                justify-content: flex-start;
                padding: 16px 24px;
                border-radius: 0;
                border-bottom: 1px solid #eee;
                background: none;
                box-shadow: none;
            }

            .profile-logo img {
                width: 28px;
                height: 28px;
            }

            .navbar {
                height: 56px;
                min-height: 56px;
            }

            .nav-container {
                height: 56px;
                min-height: 56px;
                padding: 0 8px;
            }
        }

        @media (max-width: 480px) {
            .navbar-toggle {
                display: flex !important;
            }

            .navbar {
                height: 56px;
                min-height: 56px;
            }

            .nav-container {
                height: 56px;
                min-height: 56px;
                padding: 0 4px;
            }

            .brand-text {
                font-size: 15px;
            }

            .logo {
                width: 22px;
                height: 22px;
            }

            .nav-links a,
            .nav-links button {
                font-size: 0.95rem;
                padding: 14px 16px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="home.php" class="brand-link">
                    <div class="logo">
                        <img src="compass.svg" alt="profile icon">
                    </div>
                    <span class="brand-text">CourseCompass</span>
                </a>
            </div>
            <button class="navbar-toggle" id="navbarToggle">&#9776;</button>
            <div class="nav-links" id="navLinks">
                <a href="home.php" class="<?= $currentPage == 'home.php' ? 'active' : '' ?>">Home</a>
                <a href="about.php" class="<?= $currentPage == 'about.php' ? 'active' : '' ?>">About</a>
                <a href="courses.php" class="<?= $currentPage == 'courses.php' ? 'active' : '' ?>">Courses</a>
                <a href="myrecommendations.php" class="<?= $currentPage == 'myrecommendations.php' ? 'active' : '' ?>">My Recommendations</a>
                <a href="contactus.php" class="<?= $currentPage == 'contactus.php' ? 'active' : '' ?>">Contact Us</a>
                <button class="btn-primary" onclick="location.href='signout.php'">Sign Out
                </button>
                <a href="profile.php" class="profile-logo" title="Profile">
                    <img src="profileicon.svg" alt="profile icon">
                </a>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('navbarToggle');
            const navLinks = document.getElementById('navLinks');
            toggleBtn.addEventListener('click', function () {
                navLinks.classList.toggle('show');
            });
        });
    </script>
</body>

</html>