<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
            max-width: 1200px;
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
            gap: 10px;
        }

        .logo {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .brand-text {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }

        .nav-links a {
            text-decoration: none;
            white-space: nowrap;
            color: #777373;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            transition: background 0.3s, color 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: rgba(102, 126, 234, 0.1);
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
            width: 100%;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
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

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="logo">🧭</div>
                <span class="brand-text">CourseCompass</span>
            </div>
            <div class="nav-links">
                <a href="home.php">Home</a>
                <a href="about.php">About</a>
                <a href="courses.php">Courses</a>
                <a href="contactus.php">Contact Us</a>
                <button class="btn-primary" onclick="location.href='signout.php'">Sign Out
                </button>
                <a href="profile.php" class="profile-logo" title="Profile">
                    <img src="profileicon.svg" alt="profile icon" style="width: 24px; height: 24px;">
                </a>
            </div>
        </div>
    </nav>