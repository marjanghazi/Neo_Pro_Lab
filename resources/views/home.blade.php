@extends('layouts.main')

@section('content')
<div class="home-container">
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Reliable Medical Courier Services You Can Trust</h1>
            <p class="hero-subtitle">Fast, secure, and HIPAA-compliant transport for medical specimens, lab samples, medications, and critical healthcare materials.</p>
            
            <div class="hero-buttons">
                <a href="{{ route('pickup.create') }}" class="btn-primary">
                    📅 Schedule a Pickup
                </a>
                <a href="{{ route('services') }}" class="btn-secondary">
                    🚚 View Services
                </a>
            </div>

            <div class="contact-info">
                <a href="tel:7742970597" class="contact-link">📞 (774) 297-0597</a>
                <a href="mailto:info@neoprolab.com" class="contact-link">📧 info@neoprolab.com</a>
            </div>
        </div>
    </section>

    <!-- GALLERY SECTION - ADD YOUR PICTURES HERE -->
    <section class="gallery-section">
        <div class="gallery-grid">
            <!-- Add your images in the public/images folder and update the src paths below -->
            <div class="gallery-item">
                <img src="{{ asset('images/HomeGallery/Courier-1.png') }}" alt="Medical courier delivery" class="gallery-image">
            </div>
            <div class="gallery-item">
                <img src="{{ asset('images/HomeGallery/Courier-2.jpeg') }}" alt="Specimen transport" class="gallery-image">
            </div>
            <div class="gallery-item">
                <img src="{{ asset('images/HomeGallery/Courier-3.png') }}" alt="Temperature controlled transport" class="gallery-image">
            </div>
            <div class="gallery-item">
                <img src="{{ asset('images/HomeGallery/Courier-4.png') }}" alt="Lab sample delivery" class="gallery-image">
            </div>
            <div class="gallery-item">
                <img src="{{ asset('images/HomeGallery/Courier-5.PNG') }}" alt="Medical supplies transport" class="gallery-image">
            </div>
            <div class="gallery-item">
                <img src="{{ asset('images/HomeGallery/Courier-6.png') }}" alt="Professional courier service" class="gallery-image">
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US SECTION -->
    <section class="section">
        <h2 class="section-title">Why Choose NeoProlab Couriers?</h2>
        <p class="section-subtitle">We combine expertise, compliance, and reliability to serve healthcare providers with excellence.</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3 class="feature-title">HIPAA-Compliant</h3>
                <p class="feature-description">Complete privacy protection and secure data handling for all sensitive materials.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">✓</div>
                <h3 class="feature-title">Certified Transport</h3>
                <p class="feature-description">Trained and certified couriers with specialized expertise in specimen handling.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">On-Time, Every Time</h3>
                <p class="feature-description">Reliable, punctual deliveries because we understand critical healthcare timelines.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👔</div>
                <h3 class="feature-title">Professional Service</h3>
                <p class="feature-description">Clean, professional vehicles and courteous drivers representing your facility.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">❄️</div>
                <h3 class="feature-title">Temperature Control</h3>
                <p class="feature-description">Refrigerated & non-refrigerated transport for all specimen types.</</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3 class="feature-title">Real-Time Updates</h3>
                <p class="feature-description">Track deliveries with real-time notifications and confirmation updates.</p>
            </div>
        </div>
    </section>

    <!-- WHAT WE DELIVER SECTION -->
    <section class="section bg-light-gradient">
        <h2 class="section-title">What We Deliver</h2>
        <p class="section-subtitle">Comprehensive courier services for all your healthcare logistics needs.</p>
        
        <div class="services-grid">
            <div class="service-box">
                <h3 class="service-title">🧬 Specimens & Labs</h3>
                <ul class="service-list">
                    <li>Bloodwork & Blood Samples</li>
                    <li>Lab Specimens</li>
                    <li>Urine Samples</li>
                    <li>Swabs & Culture Tests</li>
                    <li>Biopsy Kits</li>
                </ul>
            </div>
            <div class="service-box">
                <h3 class="service-title">📄 Documents & Records</h3>
                <ul class="service-list">
                    <li>Medical Forms</li>
                    <li>Lab Results</li>
                    <li>Patient Files</li>
                    <li>Secure Paperwork</li>
                    <li>Chain-of-Custody Forms</li>
                </ul>
            </div>
            <div class="service-box">
                <h3 class="service-title">💊 Pharmaceuticals & Supplies</h3>
                <ul class="service-list">
                    <li>Medications</li>
                    <li>Vaccines</li>
                    <li>Pathology Kits</li>
                    <li>Lab Supplies</li>
                    <li>PPE & Supplies</li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('services') }}" class="btn-primary">Explore All Services</a>
        </div>
    </section>

    <!-- TRUSTED BY SECTION -->
    <section class="section">
        <div class="trusted-section">
            <h2 class="section-title text-white">Trusted by Clinics & Labs</h2>
            <p class="trusted-description">You can count on NeoProlab Couriers for reliability, professionalism, and strict compliance standards—every delivery, every day.</p>
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
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        z-index: 0;
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

    /* GALLERY SECTION - NEW STYLES */
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
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover {
        transform: translateY(-8px);
    }

    .gallery-image {
        width: 100%;
        height: 280px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }

    .gallery-item:hover .gallery-image {
        transform: scale(1.05);
    }

    /* SECTIONS */
    .section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 100px 30px;
    }

    .bg-light-gradient {
        background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0, 169, 165, 0.05) 100%);
    }

    .section-title {
        font-size: 42px;
        color: var(--navy);
        margin-bottom: 18px;
        font-weight: 800;
        letter-spacing: -1px;
        text-align: center;
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
    }

    .btn-secondary:hover {
        background-color: var(--teal);
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
    }

    .contact-link:hover {
        transform: translateX(5px);
        color: var(--white);
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
        border-radius: 12px;
        border: 2px solid transparent;
        border-left: 5px solid var(--teal);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .feature-card:hover {
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.2);
        transform: translateY(-8px);
        border-color: var(--teal);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
        font-weight: bold;
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
        border-radius: 12px;
        transition: all 0.3s;
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
    }

    .service-box:hover {
        border-color: var(--teal);
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.15);
        transform: translateY(-8px);
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
    }

    .service-list li:last-child {
        border-bottom: none;
    }

    .service-list li:hover {
        color: var(--teal);
        padding-left: 5px;
    }

    .service-list li::before {
        content: '→ ';
        color: var(--teal);
        font-weight: bold;
        margin-right: 10px;
    }

    /* TRUSTED SECTION */
    .trusted-section {
        text-align: center;
        padding: 60px 40px;
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        border-radius: 12px;
        color: var(--white);
    }

    .trusted-description {
        font-size: 20px;
        color: rgba(255, 255, 255, 0.9);
        margin-top: 20px;
        font-weight: 500;
        line-height: 1.6;
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
    }

    @media (max-width: 480px) {
        .gallery-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection