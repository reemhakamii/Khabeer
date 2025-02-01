<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khabeer</title>
    <style> /* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.6;
}

h1, h2, h3 {
    font-weight: 600;
}

/* Header */
header {
    background-color: #183041;
    color: #fff;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .logo h1 {
    font-size: 2rem;
}

nav ul {
    list-style: none;
    display: flex;
}

nav ul li {
    margin-left: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
}

nav ul li .button {
    background-color: #035a66;
    padding: 8px 15px;
    border-radius: 5px;
}

/* Hero Section */
.hero {
    background: url('your-image-url.jpg') no-repeat center center/cover;
    height: 100vh;
    color: black;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.hero-content h2 {
    font-size: 3rem;
    margin-bottom: 20px;
}

.hero-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.cta-button {
    background-color: #04d07e;
    color: #fff;
    padding: 15px 25px;
    font-size: 1.2rem;
    text-decoration: none;
    border-radius: 5px;
}

/* Features Section */
.features {
    background-color: #ffffff;
    padding: 4rem 2rem;
    text-align: center;
}

.features h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.feature-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.feature-cards .card {
    background-color: #eeeeee;
    padding: 20px;
    border-radius: 10px;
}

.card h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

/* About Section */
.about {
    padding: 4rem 2rem;
    background-color: #f9f9f9;
    text-align: center;
}

.about p {
    font-size: 1.2rem;
    margin-top: 20px;
}

/* Contact Section */
.contact {
    padding: 4rem 2rem;
    background-color: #ffffff;
    text-align: center;
}

.contact form {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
}

.contact input, .contact textarea {
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
}

.contact button {
    background-color: #04d07e;
    color: #fff;
    padding: 12px;
    border-radius: 5px;
    font-size: 1.1rem;
    cursor: pointer;
}

/* Footer */
footer {
    background-color: #183041;
    color: #fff;
    padding: 2rem 0;
    text-align: center;
}

footer .footer-content p {
    font-size: 1rem;
    margin-top: 10px;
}
</style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Khabeer</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#features">Features</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="login.php" class="button">Login</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2>Empowering Saudi Arabia's Expertise</h2>
            <p>Connect with verified consultants, unlock specialized knowledge, and grow your business or career.</p>
        </div>
    </section>

    <section id="features" class="features">
        <h2>Key Features</h2>
        <div class="feature-cards">
            <div class="card">
                <h3>Verified Consultants</h3>
                <p>Browse detailed profiles with expert ratings and reviews.</p>
            </div>
            <div class="card">
                <h3>AI-Powered Matchmaking</h3>
                <p>Find the perfect consultant tailored to your needs.</p>
            </div>
            <div class="card">
                <h3>Easy Scheduling</h3>
                <p>Book consultations with ease, anytime, anywhere.</p>
            </div>
            <div class="card">
                <h3>Secure Payments</h3>
                <p>Pay through secure, local payment methods like Mada.</p>
            </div>
        </div>
    </section>

    <section id="about" class="about">
        <h2>About Us</h2>
        <p>Our platform connects individuals and businesses in Saudi Arabia with the best local consultants to support their professional growth. We provide a user-friendly experience that’s tailored for the Saudi market.</p>
    </section>

    <section id="contact" class="contact">
        <h2>Contact Us</h2>
        <form>
            <input type="text" placeholder="Your Name" required>
            <input type="email" placeholder="Your Email" required>
            <textarea placeholder="Your Message" required></textarea>
            <button type="submit" class="cta-button">Send Message</button>
        </form>
    </section>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 Khabeer | All Rights Reserved</p>
            <p>Designed for Saudi Arabia's growing professional ecosystem</p>
        </div>
    </footer>
</body>
</html>