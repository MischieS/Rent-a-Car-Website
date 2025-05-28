<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Contact Us - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
</head>
<body>
    <div class="main-wrapper">
        <!-- Header -->
        <?php include('assets/includes/header.php') ?>
        <!-- /Header -->

        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Contact Us</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Contact</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Contact Section -->
        <section class="contact-section">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h6 class="section-subtitle">GET IN TOUCH</h6>
                    <h2 class="section-title">Contact Us</h2>
                    <p class="section-description">We're here to help and answer any questions you might have</p>
                </div>
                
                <div class="row mt-5">
                    <div class="col-lg-4 mb-4 mb-lg-0" data-aos="fade-up">
                        <div class="contact-info">
                            <div class="contact-info-item">
                                <div class="icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="content">
                                    <h4>Our Location</h4>
                                    <p>123 Main Street, New York, NY 10001, USA</p>
                                </div>
                            </div>
                            
                            <div class="contact-info-item">
                                <div class="icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="content">
                                    <h4>Phone Number</h4>
                                    <p>
                                        <a href="tel:+1234567890">+123 456 7890</a><br>
                                        <a href="tel:+1234567891">+123 456 7891</a>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="contact-info-item">
                                <div class="icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="content">
                                    <h4>Email Address</h4>
                                    <p>
                                        <a href="mailto:info@dreamsrent.com">info@dreamsrent.com</a><br>
                                        <a href="mailto:support@dreamsrent.com">support@dreamsrent.com</a>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="contact-info-item">
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="content">
                                    <h4>Working Hours</h4>
                                    <p>
                                        Monday - Friday: 8:00 AM - 7:00 PM<br>
                                        Saturday: 9:00 AM - 5:00 PM<br>
                                        Sunday: 10:00 AM - 4:00 PM
                                    </p>
                                </div>
                            </div>
                            
                            <div class="contact-social">
                                <h4>Follow Us</h4>
                                <div class="social-links">
                                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                    <a href="#"><i class="fab fa-instagram"></i></a>
                                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                        <div class="contact-form-wrapper">
                            <h3>Send Us a Message</h3>
                            <p>Have questions or need assistance? Fill out the form below and we'll get back to you as soon as possible.</p>
                            
                            <form id="contactForm" class="contact-form">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Your Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="subject" class="form-label">Subject</label>
                                            <select class="form-select" id="subject" name="subject" required>
                                                <option value="">Select subject</option>
                                                <option value="reservation">Reservation Inquiry</option>
                                                <option value="support">Customer Support</option>
                                                <option value="feedback">Feedback</option>
                                                <option value="partnership">Business Partnership</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="message" class="form-label">Your Message</label>
                                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                            <label class="form-check-label" for="privacy">
                                                I agree to the <a href="#">Privacy Policy</a> and consent to the processing of my personal data.
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Contact Section -->

        <!-- Branches Section -->
        <section class="branches-section">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h6 class="section-subtitle">OUR LOCATIONS</h6>
                    <h2 class="section-title">Our Branches</h2>
                    <p class="section-description">Visit one of our convenient locations throughout the city</p>
                </div>
                
                <div class="row g-4 mt-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="branch-card">
                            <div class="branch-header">
                                <h3>Downtown Office</h3>
                                <span class="badge">Main Branch</span>
                            </div>
                            <div class="branch-body">
                                <div class="branch-info">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <p>123 Main Street, New York, NY 10001</p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone-alt"></i>
                                        <p><a href="tel:+1234567890">+123 456 7890</a></p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <p><a href="mailto:downtown@dreamsrent.com">downtown@dreamsrent.com</a></p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <p>Mon-Fri: 8:00 AM - 7:00 PM<br>Sat: 9:00 AM - 5:00 PM<br>Sun: 10:00 AM - 4:00 PM</p>
                                    </div>
                                </div>
                                <a href="https://maps.google.com" target="_blank" class="btn btn-outline-primary btn-sm mt-3">Get Directions</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="branch-card">
                            <div class="branch-header">
                                <h3>Airport Terminal</h3>
                                <span class="badge">24/7 Service</span>
                            </div>
                            <div class="branch-body">
                                <div class="branch-info">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <p>Terminal 4, JFK International Airport, Queens, NY 11430</p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone-alt"></i>
                                        <p><a href="tel:+1234567891">+123 456 7891</a></p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <p><a href="mailto:airport@dreamsrent.com">airport@dreamsrent.com</a></p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <p>Open 24 hours, 7 days a week</p>
                                    </div>
                                </div>
                                <a href="https://maps.google.com" target="_blank" class="btn btn-outline-primary btn-sm mt-3">Get Directions</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="branch-card">
                            <div class="branch-header">
                                <h3>Uptown Office</h3>
                                <span class="badge">Premium Fleet</span>
                            </div>
                            <div class="branch-body">
                                <div class="branch-info">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <p>456 Park Avenue, New York, NY 10022</p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone-alt"></i>
                                        <p><a href="tel:+1234567892">+123 456 7892</a></p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <p><a href="mailto:uptown@dreamsrent.com">uptown@dreamsrent.com</a></p>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <p>Mon-Fri: 9:00 AM - 6:00 PM<br>Sat: 10:00 AM - 4:00 PM<br>Sun: Closed</p>
                                    </div>
                                </div>
                                <a href="https://maps.google.com" target="_blank" class="btn btn-outline-primary btn-sm mt-3">Get Directions</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Branches Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Form submission
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Here you would typically send the form data to the server
                // For demo purposes, we'll just show an alert
                alert('Thank you for your message! We will get back to you soon.');
                contactForm.reset();
            });
        }
    });
    </script>
</body>
</html>
