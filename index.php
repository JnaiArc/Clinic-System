<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftCare Clinic</title>
    <link rel="stylesheet" href="view/css/landing.css">
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="nav-brand">
            <img src="img/logo.png" alt="SwiftCare Clinic logo">
            <span>SwiftCare</span>
        </a>

        <nav class="nav-links">
            <a href="#home">Home</a>
            <a href="#services">Services</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>

        <div class="nav-actions">
            <a href="view/login/login.php" class="btn-login">Login</a>
            <a href="view/login/register.php" class="btn-register">Register</a>
        </div>

        <button class="nav-toggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-inner">
        <div class="hero-copy">
            <span class="hero-eyebrow">Trusted Clinic Appointment System</span>
            <h1>Healthcare made <span>swift</span>, simple, and personal.</h1>
            <p>SwiftCare Clinic connects you with the right doctor, faster. Book appointments, track follow-ups, and manage your care from one easy-to-use platform.</p>
            <div class="hero-cta">
                <a href="view/login/register.php" class="btn-primary">Book an Appointment</a>
                <a href="#services" class="btn-secondary">Explore Services</a>
            </div>
            <div class="hero-stats">
                <div>
                    <h3>15+</h3>
                    <p>Qualified Doctors</p>
                </div>
                <div>
                    <h3>10k+</h3>
                    <p>Patients Served</p>
                </div>
                <div>
                    <h3>24/7</h3>
                    <p>Online Booking</p>
                </div>
            </div>
        </div>

        <div class="hero-card">
            <h4>Why patients choose us</h4>
            <div class="hero-card-item">
                <div>
                    <p>Fast scheduling</p>
                    <span>Book in under 2 minutes</span>
                </div>
            </div>
            <div class="hero-card-item">
                <div>
                    <p>Experienced doctors</p>
                    <span>Across multiple specialties</span>
                </div>
            </div>
            <div class="hero-card-item">
                <div>
                    <p>Digital records</p>
                    <span>Your history, always on hand</span>
                </div>
            </div>
            <div class="hero-card-item">
                <div>
                    <p>Easy follow-ups</p>
                    <span>Never miss a checkup</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section class="services" id="services">
    <div class="section-wrap">
        <div class="section-head">
            <span class="eyebrow">What We Offer</span>
            <h2>Our Services</h2>
            <p>From general checkups to specialized care and follow-up consultations, SwiftCare Clinic covers your healthcare needs in one place.</p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <h3>General Consultation</h3>
                <p>Speak with a licensed physician about everyday health concerns, symptoms, or check-ups.</p>
            </div>
            <div class="service-card">
                <h3>Appointment Booking</h3>
                <p>Reserve a slot with your preferred doctor online, anytime, without waiting on the phone.</p>
            </div>
            <div class="service-card">
                <h3>Follow-Up Care</h3>
                <p>Stay on track with scheduled follow-up checkups so nothing falls through the cracks.</p>
            </div>
            <div class="service-card">
                <h3>Patient Records</h3>
                <p>Your medical history, allergies, and visit notes, securely stored and easy to access.</p>
            </div>
            <div class="service-card">
                <h3>Specialist Referrals</h3>
                <p>Get connected to the right specialist on our team based on your specific needs.</p>
            </div>
            <div class="service-card">
                <h3>Reminders &amp; Updates</h3>
                <p>Get notified about upcoming appointments and follow-ups so you're always prepared.</p>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section class="about" id="about">
    <div class="about-inner">
        <div class="about-visual">
            <img src="img/logo.png" alt="SwiftCare Clinic">
        </div>
        <div class="about-text">
            <span class="eyebrow">About SwiftCare</span>
            <h2>Care that fits around your life, not the other way around.</h2>
            <p>SwiftCare Clinic was built to remove the friction from getting care: no long phone queues, no lost paperwork, and no guesswork about your next visit. Our doctors and staff use the same system you do, so your records and appointments always stay in sync.</p>
            <ul class="about-list">
                <li>Licensed doctors across multiple specialties</li>
                <li>Simple online booking and rescheduling</li>
                <li>Secure, always up-to-date patient records</li>
                <li>Dedicated staff support for every visit</li>
            </ul>
        </div>
    </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
    <div class="section-wrap">
        <h2>Ready to take the next step in your care?</h2>
        <p>Create your patient account and book your first appointment in minutes.</p>
        <a href="view/login/register.php" class="btn-primary">Get Started</a>
    </div>
</section>

<!-- CONTACT -->
<section class="contact" id="contact">
    <div class="section-wrap">
        <div class="section-head">
            <span class="eyebrow">Get In Touch</span>
            <h2>Contact Us</h2>
            <p>Have a question before booking? Reach out and our staff will get back to you.</p>
        </div>

        <div class="contact-inner">
            <div class="contact-info">
                <h3>SwiftCare Clinic</h3>
                <p>Our front desk team is happy to help with appointment questions, records requests, or general inquiries.</p>

                <div class="contact-item">
                    <div>
                        <strong>Address</strong>
                        <span>123 Wellness Ave, Health City</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div>
                        <strong>Phone</strong>
                        <span>(02) 8888-0123</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div>
                        <strong>Email</strong>
                        <span>support@swiftcareclinic.com</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div>
                        <strong>Hours</strong>
                        <span>Mon - Sat, 8:00 AM - 6:00 PM</span>
                    </div>
                </div>
            </div>

            <form class="contact-form" onsubmit="return false;">
                <div class="row-2">
                    <div>
                        <label>Full Name</label>
                        <input type="text" placeholder="Juan Dela Cruz">
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" placeholder="you@email.com">
                    </div>
                </div>
                <div>
                    <label>Message</label>
                    <textarea placeholder="How can we help you?"></textarea>
                </div>
                <button type="submit">Send Message</button>
                <p class="form-note">Prefer to book directly? <a href="view/login/register.php" style="color:#02529c; font-weight:600;">Create a patient account</a> instead.</p>
            </form>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="section-wrap">
        <div class="footer-inner">
            <div>
                <div class="footer-brand">
                    <img src="img/logo.png" alt="SwiftCare Clinic logo">
                    <span>SwiftCare Clinic</span>
                </div>
                <p>A modern clinic appointment system built to make healthcare simple for patients, doctors, and staff.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Account</h4>
                <ul>
                    <li><a href="view/login/login.php">Login</a></li>
                    <li><a href="view/login/register.php">Register</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> SwiftCare Clinic. All rights reserved.
        </div>
    </div>
</footer>

<script src="view/js/landing.js"></script>

</body>
</html>
