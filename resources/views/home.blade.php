@extends('layouts.main')

@section('content')
<div class="home-container">
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="hero-content animate-fade-in">
            <h1 class="hero-title animate-slide-up">Reliable Medical Courier Services You Can Trust</h1>
            <p class="hero-greeting animate-slide-up delay-1">Welcome, Marjan!</p>
            <p class="hero-subtitle animate-slide-up delay-1">Fast, secure, and HIPAA-compliant transport for medical specimens, lab samples, medications, and critical healthcare materials.</p>
            
            <div class="hero-buttons animate-slide-up delay-2">
                <a href="{{ route('pickup.create') }}" class="btn-primary pulse-hover">
                    📅 Schedule a Pickup
                </a>
                <a href="{{ route('services') }}" class="btn-secondary slide-hover">
                    🚚 View Services
                </a>
            </div>

            <div class="contact-info animate-slide-up delay-3">
                <a href="tel:7742970597" class="contact-link hover-scale">📞 (508) 933-6750</a>
                <a href="mailto:info@neoprolab.com" class="contact-link hover-scale">📧 info@neoprolab.com</a>
            </div>
        </div>
        
        <!-- Animated background elements -->
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
    </section>

    <!-- GALLERY SECTION - ADD YOUR PICTURES HERE -->
    <section class="gallery-section">
        <div class="gallery-grid">
            <!-- Add your images in the public/images folder and update the src paths below -->
            <div class="gallery-item animate-on-scroll">
                <div class="image-wrapper">
                    <img src="{{ asset('images/HomeGallery/Courier-1.png') }}" alt="Medical courier delivery" class="gallery-image">
                    <div class="image-overlay">
                        <span class="overlay-text">Medical Delivery</span>
                    </div>
                </div>
            </div>
            <div class="gallery-item animate-on-scroll delay-1">
                <div class="image-wrapper">
                    <img src="{{ asset('images/HomeGallery/Courier-2.jpeg') }}" alt="Specimen transport" class="gallery-image">
                    <div class="image-overlay">
                        <span class="overlay-text">Specimen Transport</span>
                    </div>
                </div>
            </div>
            <div class="gallery-item animate-on-scroll delay-2">
                <div class="image-wrapper">
                    <img src="{{ asset('images/HomeGallery/Courier-3.png') }}" alt="Temperature controlled transport" class="gallery-image">
                    <div class="image-overlay">
                        <span class="overlay-text">Temperature Controlled</span>
                    </div>
                </div>
            </div>
            <div class="gallery-item animate-on-scroll">
                <div class="image-wrapper">
                    <img src="{{ asset('images/HomeGallery/Courier-4.png') }}" alt="Lab sample delivery" class="gallery-image">
                    <div class="image-overlay">
                        <span class="overlay-text">Lab Sample Delivery</span>
                    </div>
                </div>
            </div>
            <div class="gallery-item animate-on-scroll delay-1">
                <div class="image-wrapper">
                    <img src="{{ asset('images/HomeGallery/Courier-5.PNG') }}" alt="Medical supplies transport" class="gallery-image">
                    <div class="image-overlay">
                        <span class="overlay-text">Medical Supplies</span>
                    </div>
                </div>
            </div>
            <div class="gallery-item animate-on-scroll delay-2">
                <div class="image-wrapper">
                    <img src="{{ asset('images/HomeGallery/Courier-6.png') }}" alt="Professional courier service" class="gallery-image">
                    <div class="image-overlay">
                        <span class="overlay-text">Professional Service</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US SECTION -->
    <section class="section">
        <h2 class="section-title animate-on-scroll">Why Choose NeoProlab Couriers?</h2>
        <p class="section-subtitle animate-on-scroll delay-1">We combine expertise, compliance, and reliability to serve healthcare providers with excellence.</p>
        
        <div class="features-grid">
            <div class="feature-card animate-on-scroll">
                <div class="feature-icon floating-icon">🔒</div>
                <h3 class="feature-title">HIPAA-Compliant</h3>
                <p class="feature-description">Complete privacy protection and secure data handling for all sensitive materials.</p>
                <div class="card-glow"></div>
            </div>
            <div class="feature-card animate-on-scroll delay-1">
                <div class="feature-icon floating-icon">✓</div>
                <h3 class="feature-title">Certified Transport</h3>
                <p class="feature-description">Trained and certified couriers with specialized expertise in specimen handling.</p>
                <div class="card-glow"></div>
            </div>
            <div class="feature-card animate-on-scroll delay-2">
                <div class="feature-icon floating-icon">⚡</div>
                <h3 class="feature-title">On-Time, Every Time</h3>
                <p class="feature-description">Reliable, punctual deliveries because we understand critical healthcare timelines.</p>
                <div class="card-glow"></div>
            </div>
            <div class="feature-card animate-on-scroll">
                <div class="feature-icon floating-icon">👔</div>
                <h3 class="feature-title">Professional Service</h3>
                <p class="feature-description">Clean, professional vehicles and courteous drivers representing your facility.</p>
                <div class="card-glow"></div>
            </div>
            <div class="feature-card animate-on-scroll delay-1">
                <div class="feature-icon floating-icon">❄️</div>
                <h3 class="feature-title">Temperature Control</h3>
                <p class="feature-description">Refrigerated & non-refrigerated transport for all specimen types.</p>
                <div class="card-glow"></div>
            </div>
            <div class="feature-card animate-on-scroll delay-2">
                <div class="feature-icon floating-icon">📱</div>
                <h3 class="feature-title">Real-Time Updates</h3>
                <p class="feature-description">Track deliveries with real-time notifications and confirmation updates.</p>
                <div class="card-glow"></div>
            </div>
        </div>
    </section>

    <!-- WHAT WE DELIVER SECTION -->
    <section class="section bg-light-gradient">
        <h2 class="section-title animate-on-scroll">What We Deliver</h2>
        <p class="section-subtitle animate-on-scroll delay-1">Comprehensive courier services for all your healthcare logistics needs.</p>
        
        <div class="services-grid">
            <div class="service-box animate-on-scroll">
                <div class="service-icon-wrapper">
                    <span class="service-icon">🧬</span>
                </div>
                <h3 class="service-title">Specimens & Labs</h3>
                <ul class="service-list">
                    <li class="list-item-hover">Bloodwork & Blood Samples</li>
                    <li class="list-item-hover">Lab Specimens</li>
                    <li class="list-item-hover">Urine Samples</li>
                    <li class="list-item-hover">Swabs & Culture Tests</li>
                    <li class="list-item-hover">Biopsy Kits</li>
                </ul>
            </div>
            <div class="service-box animate-on-scroll delay-1">
                <div class="service-icon-wrapper">
                    <span class="service-icon">📄</span>
                </div>
                <h3 class="service-title">Documents & Records</h3>
                <ul class="service-list">
                    <li class="list-item-hover">Medical Forms</li>
                    <li class="list-item-hover">Lab Results</li>
                    <li class="list-item-hover">Patient Files</li>
                    <li class="list-item-hover">Secure Paperwork</li>
                    <li class="list-item-hover">Chain-of-Custody Forms</li>
                </ul>
            </div>
            <div class="service-box animate-on-scroll delay-2">
                <div class="service-icon-wrapper">
                    <span class="service-icon">💊</span>
                </div>
                <h3 class="service-title">Pharmaceuticals & Supplies</h3>
                <ul class="service-list">
                    <li class="list-item-hover">Medications</li>
                    <li class="list-item-hover">Vaccines</li>
                    <li class="list-item-hover">Pathology Kits</li>
                    <li class="list-item-hover">Lab Supplies</li>
                    <li class="list-item-hover">PPE & Supplies</li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-10 animate-on-scroll">
            <a href="{{ route('services') }}" class="btn-primary pulse-hover">Explore All Services</a>
        </div>
    </section>

    <!-- TRUSTED BY SECTION -->
    <section class="section">
        <div class="trusted-section animate-on-scroll">
            <h2 class="section-title text-white">Trusted by Clinics & Labs</h2>
            <p class="trusted-description">You can count on NeoProlab Couriers for reliability, professionalism, and strict compliance standards—every delivery, every day.</p>
            
            <!-- Animated stats -->
            <div class="stats-container">
                <div class="stat-item">
                    <span class="stat-number" data-target="500">500+</span>
                    <span class="stat-label">Daily Deliveries</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="98">98%</span>
                    <span class="stat-label">On-Time Rate</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="1000">1000+</span>
                    <span class="stat-label">Happy Clients</span>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
    }

    /* ANIMATIONS */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-fade-in {
        animation: fadeIn 1s ease-out;
    }

    .animate-slide-up {
        opacity: 0;
        animation: slideUp 0.8s ease-out forwards;
    }

    .delay-1 {
        animation-delay: 0.2s;
    }

    .delay-2 {
        animation-delay: 0.4s;
    }

    .delay-3 {
        animation-delay: 0.6s;
    }

    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease-out;
    }

    .animate-on-scroll.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* HOVER EFFECTS */
    .pulse-hover {
        transition: all 0.3s ease;
    }

    .pulse-hover:hover {
        animation: pulse 1s infinite;
    }

    .slide-hover {
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .slide-hover::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--teal);
        transition: left 0.3s ease;
        z-index: -1;
    }

    .slide-hover:hover::before {
        left: 0;
    }

    .slide-hover:hover {
        color: var(--white);
        border-color: var(--teal);
    }

    .hover-scale {
        transition: transform 0.3s ease;
    }

    .hover-scale:hover {
        transform: scale(1.1);
    }

    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }

    /* HERO SECTION */
    .hero-section {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        color: var(--white);
        padding: 140px 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -15%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        z-index: 0;
        animation: rotate 60s linear infinite;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -15%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(0,169,165,0.1) 0%, rgba(0,169,165,0) 70%);
        border-radius: 50%;
        z-index: 0;
        animation: rotate 45s linear infinite reverse;
    }

    .floating-shape {
        position: absolute;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        z-index: 0;
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        top: 20%;
        left: -100px;
        animation: float 8s ease-in-out infinite;
    }

    .shape-2 {
        width: 200px;
        height: 200px;
        bottom: 20%;
        right: -50px;
        animation: float 6s ease-in-out infinite reverse;
    }

    .shape-3 {
        width: 150px;
        height: 150px;
        top: 40%;
        right: 20%;
        animation: float 10s ease-in-out infinite;
    }

    .hero-content {
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: 56px;
        margin-bottom: 25px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -1px;
    }

    .hero-greeting {
        font-size: 26px;
        margin-bottom: 14px;
        font-weight: 700;
        color: #8EF9F6;
    }

    .hero-subtitle {
        font-size: 21px;
        margin-bottom: 50px;
        opacity: 0.95;
        max-width: 650px;
        margin-left: auto;
        margin-right: auto;
        font-weight: 500;
        line-height: 1.6;
    }

    /* GALLERY SECTION */
    .gallery-section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 30px;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }

    .gallery-item {
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        cursor: pointer;
    }

    .gallery-image {
        width: 100%;
        height: 280px;
        object-fit: cover;
        display: block;
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 30px 20px 20px;
        transform: translateY(100%);
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .image-wrapper:hover .gallery-image {
        transform: scale(1.1);
    }

    .image-wrapper:hover .image-overlay {
        transform: translateY(0);
    }

    .overlay-text {
        color: white;
        font-size: 18px;
        font-weight: 600;
        display: block;
        text-align: center;
    }

    /* SECTIONS */
    .section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 100px 30px;
    }

    .bg-light-gradient {
        background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0, 169, 165, 0.05) 100%);
        border-radius: 30px;
        margin: 40px auto;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 42px;
        color: var(--navy);
        margin-bottom: 18px;
        font-weight: 800;
        letter-spacing: -1px;
        text-align: center;
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, var(--teal), #008B85);
        border-radius: 2px;
    }

    .section-subtitle {
        font-size: 18px;
        color: var(--gray);
        margin-bottom: 50px;
        max-width: 700px;
        line-height: 1.8;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
    }

    /* BUTTONS */
    .hero-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
        margin-bottom: 40px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        color: var(--white);
        padding: 14px 32px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
        font-size: 16px;
        display: inline-block;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-primary:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 169, 165, 0.4);
    }

    .btn-secondary {
        background: transparent;
        border: 2.5px solid var(--teal);
        color: var(--teal);
        padding: 14px 32px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 16px;
        display: inline-block;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .btn-secondary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--teal);
        transition: left 0.3s ease;
        z-index: -1;
    }

    .btn-secondary:hover::before {
        left: 0;
    }

    .btn-secondary:hover {
        color: var(--white);
    }

    /* CONTACT INFO */
    .contact-info {
        margin-top: 50px;
        font-size: 16px;
        display: flex;
        gap: 30px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .contact-link {
        color: var(--teal);
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 30px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(5px);
    }

    .contact-link:hover {
        transform: translateX(5px);
        color: var(--white);
        background: rgba(0,169,165,0.2);
    }

    /* FEATURES GRID */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .feature-card {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 40px;
        border-radius: 16px;
        border: 2px solid transparent;
        border-left: 5px solid var(--teal);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,169,165,0.1) 0%, rgba(0,169,165,0) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .feature-card:hover::before {
        opacity: 1;
    }

    .feature-card:hover {
        box-shadow: 0 20px 40px rgba(0, 169, 165, 0.15);
        transform: translateY(-8px) scale(1.02);
        border-color: var(--teal);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: rotate(5deg) scale(1.1);
    }

    .feature-title {
        color: var(--navy);
        margin-bottom: 15px;
        font-size: 22px;
        font-weight: 700;
    }

    .feature-description {
        color: var(--gray);
        line-height: 1.8;
        font-weight: 500;
    }

    .card-glow {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
        opacity: 0;
        transition: opacity 0.5s ease;
        pointer-events: none;
    }

    .feature-card:hover .card-glow {
        opacity: 1;
    }

    /* SERVICES GRID */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .service-box {
        background: var(--white);
        border: 2.5px solid var(--light-gray);
        padding: 35px;
        border-radius: 16px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .service-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--teal), #008B85);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .service-box:hover::before {
        transform: scaleX(1);
    }

    .service-box:hover {
        border-color: var(--teal);
        box-shadow: 0 20px 40px rgba(0, 169, 165, 0.15);
        transform: translateY(-8px);
    }

    .service-icon-wrapper {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0,169,165,0.1) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }

    .service-box:hover .service-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
    }

    .service-icon {
        font-size: 32px;
        transition: all 0.3s ease;
    }

    .service-box:hover .service-icon {
        transform: scale(1.1);
        color: white;
    }

    .service-title {
        color: var(--navy);
        margin-bottom: 25px;
        font-size: 24px;
        font-weight: 700;
    }

    .service-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .service-list li {
        padding: 12px 0;
        color: var(--gray);
        border-bottom: 1px solid var(--light-gray);
        font-weight: 500;
        transition: all 0.3s;
        position: relative;
        padding-left: 25px;
    }

    .service-list li:last-child {
        border-bottom: none;
    }

    .service-list li::before {
        content: '→';
        position: absolute;
        left: 0;
        color: var(--teal);
        font-weight: bold;
        transition: transform 0.3s ease;
    }

    .list-item-hover:hover {
        color: var(--teal);
        padding-left: 30px;
    }

    .list-item-hover:hover::before {
        transform: translateX(5px);
    }

    /* TRUSTED SECTION */
    .trusted-section {
        text-align: center;
        padding: 60px 40px;
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        border-radius: 30px;
        color: var(--white);
        position: relative;
        overflow: hidden;
    }

    .trusted-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(0,169,165,0.1) 0%, rgba(0,169,165,0) 70%);
        animation: rotate 30s linear infinite;
    }

    .trusted-description {
        font-size: 20px;
        color: rgba(255, 255, 255, 0.9);
        margin-top: 20px;
        font-weight: 500;
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }

    .stats-container {
        display: flex;
        justify-content: center;
        gap: 60px;
        margin-top: 50px;
        position: relative;
        z-index: 1;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 48px;
        font-weight: 800;
        color: var(--teal);
        display: block;
        margin-bottom: 10px;
        animation: pulse 2s ease-in-out infinite;
    }

    .stat-label {
        font-size: 16px;
        color: rgba(255,255,255,0.8);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* UTILITY CLASSES */
    .text-center {
        text-align: center;
    }

    .text-white {
        color: var(--white);
    }

    .mt-10 {
        margin-top: 40px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .hero-section {
            padding: 80px 20px;
        }
        
        .hero-title {
            font-size: 36px;
        }
        
        .hero-subtitle {
            font-size: 18px;
        }
        
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .gallery-image {
            height: 200px;
        }
        
        .section {
            padding: 60px 20px;
        }
        
        .section-title {
            font-size: 32px;
        }
        
        .features-grid,
        .services-grid {
            grid-template-columns: 1fr;
        }
        
        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .contact-info {
            flex-direction: column;
            gap: 20px;
        }

        .stats-container {
            flex-direction: column;
            gap: 30px;
        }
    }

    @media (max-width: 480px) {
        .gallery-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Intersection Observer Animation */
    .animate-on-scroll.visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<script>
// Add intersection observer for scroll animations
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    animatedElements.forEach(element => {
        observer.observe(element);
    });

    // Animate stats numbers
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const animateValue = (element, start, end, duration) => {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= end) {
                element.textContent = end + '+';
                clearInterval(timer);
            } else {
                element.textContent = Math.round(current) + '+';
            }
        }, 16);
    };

    // Trigger stats animation when they come into view
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numbers = entry.target.querySelectorAll('.stat-number');
                numbers.forEach(number => {
                    const target = parseInt(number.getAttribute('data-target'));
                    animateValue(number, 0, target, 2000);
                });
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const trustedSection = document.querySelector('.trusted-section');
    if (trustedSection) {
        statsObserver.observe(trustedSection);
    }
});
</script>
@endsection