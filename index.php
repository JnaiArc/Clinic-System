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
        </div>

        <div class="hero-card">
            <h4>Why patients choose us</h4>
            <div class="hero-card-item">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                </div>
                <div>
                    <p>Fast scheduling</p>
                    <span>Book in under 2 minutes</span>
                </div>
            </div>
            <div class="hero-card-item">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 11.5a8.5 8.5 0 1 1-8.66-8.5"></path><path d="M20 4l-8.5 8.5-3-3"></path></svg>
                </div>
                <div>
                    <p>Experienced doctors</p>
                    <span>Across multiple specialties</span>
                </div>
            </div>
            <div class="hero-card-item">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="15" y2="17"></line></svg>
                </div>
                <div>
                    <p>Digital records</p>
                    <span>Your history, always on hand</span>
                </div>
            </div>
            <div class="hero-card-item">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><path d="M9 16l2 2 4-4"></path></svg>
                </div>
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
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4.8 2v6a4.2 4.2 0 0 0 8.4 0V2"></path><path d="M9 14v3a5 5 0 0 0 10 0v-2"></path><circle cx="20" cy="10" r="2"></circle></svg>
                </div>
                <h3>General Consultation</h3>
                <p>Speak with a licensed physician about everyday health concerns, symptoms, or check-ups.</p>
            </div>
            <div class="service-card">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <h3>Appointment Booking</h3>
                <p>Reserve a slot with your preferred doctor online, anytime, without waiting on the phone.</p>
            </div>
            <div class="service-card">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                </div>
                <h3>Follow-Up Care</h3>
                <p>Stay on track with scheduled follow-up checkups so nothing falls through the cracks.</p>
            </div>
            <div class="service-card">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="15" y2="17"></line></svg>
                </div>
                <h3>Patient Records</h3>
                <p>Your medical history, allergies, and visit notes, securely stored and easy to access.</p>
            </div>
            <div class="service-card">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <h3>Specialist Referrals</h3>
                <p>Get connected to the right specialist on our team based on your specific needs.</p>
            </div>
            <div class="service-card">
                <div class="icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>
                </div>
                <h3>Online Consultation</h3>
                <p>Talk to your doctor from anywhere through a secure video call, no clinic visit needed.</p>
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
                    <div class="icon-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div>
                        <strong>Address</strong>
                        <span>123 Wellness Ave, Health City</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="icon-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div>
                        <strong>Phone</strong>
                        <span>(02) 8888-0123</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="icon-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" opacity="0"></path><path d="M22 6c0-1.1-.9-2-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6z"></path><polyline points="22 6 12 13 2 6"></polyline></svg>
                    </div>
                    <div>
                        <strong>Email</strong>
                        <span>support@swiftcareclinic.com</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="icon-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
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