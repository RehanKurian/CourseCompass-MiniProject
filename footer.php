<style>
.footer {
    background: #1a1a1a;
    color: white;
    padding: 60px 0 20px;
}
.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}
.footer-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}
.footer-description {
    color: #999;
    line-height: 1.6;
}
.footer-title {
    font-weight: 600;
    margin-bottom: 20px;
    color: white;
}
.footer-links {
    list-style: none;
    padding: 0;
}
.footer-links li {
    margin-bottom: 12px;
}
.footer-links a {
    color: #999;
    text-decoration: none;
    transition: color 0.3s ease;
}
.footer-links a:hover {
    color: white;
}
.footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #333;
    color: #999;
}
@media (max-width: 992px) {
    .footer-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        text-align: center;
    }
}
</style>

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