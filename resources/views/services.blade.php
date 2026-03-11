@extends('layouts.main')

@section('content')
<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
    }

    /* Hero Section with Background Image */
    .hero {
        position: relative;
        background: linear-gradient(rgba(13, 27, 42, 0.7), rgba(0, 169, 165, 0.7)), 
                    url('https://images.unsplash.com/photo-1581093458791-9d1548248f2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: var(--white);
        padding: 140px 30px;
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        overflow: hidden;
        animation: heroZoom 10s infinite alternate;
    }

    @keyframes heroZoom {
        0% { background-size: 100%; }
        100% { background-size: 110%; }
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(13, 27, 42, 0.85) 0%, rgba(0, 169, 165, 0.75) 100%);
        animation: pulseOverlay 4s infinite alternate;
    }

    @keyframes pulseOverlay {
        0% { opacity: 0.7; }
        100% { opacity: 0.85; }
    }

    .hero h1 {
        font-size: 56px;
        margin-bottom: 25px;
        font-weight: 800;
        position: relative;
        z-index: 2;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        animation: slideUp 1s ease-out;
        line-height: 1.2;
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero p {
        font-size: 20px;
        opacity: 0.95;
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        animation: slideUp 1s ease-out 0.2s both;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 80px 30px;
        position: relative;
    }

    .section:nth-child(even) {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
    }

    .section-title {
        font-size: 42px;
        color: var(--navy);
        margin-bottom: 18px;
        font-weight: 800;
        position: relative;
        display: inline-block;
        animation: fadeInLeft 0.8s ease-out;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, var(--teal), #008B85);
        border-radius: 2px;
        animation: expandWidth 1s ease-out 0.3s both;
    }

    @keyframes expandWidth {
        from { width: 0; }
        to { width: 80px; }
    }

    .section-subtitle {
        font-size: 18px;
        color: var(--gray);
        margin-bottom: 50px;
        max-width: 700px;
        line-height: 1.8;
        animation: fadeInLeft 0.8s ease-out 0.1s both;
    }

    /* Services Grid */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .service-box {
        background: var(--white);
        border: 2.5px solid var(--light-gray);
        padding: 40px;
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .service-box:nth-child(1) { animation-delay: 0.1s; }
    .service-box:nth-child(2) { animation-delay: 0.2s; }
    .service-box:nth-child(3) { animation-delay: 0.3s; }
    .service-box:nth-child(4) { animation-delay: 0.4s; }
    .service-box:nth-child(5) { animation-delay: 0.5s; }
    .service-box:nth-child(6) { animation-delay: 0.6s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
        transition: transform 0.4s;
    }

    .service-box:hover {
        border-color: var(--teal);
        box-shadow: 0 25px 50px rgba(0, 169, 165, 0.2);
        transform: translateY(-10px) scale(1.02);
    }

    .service-box:hover::before {
        transform: scaleX(1);
    }

    .service-box h3 {
        color: var(--navy);
        margin-bottom: 25px;
        font-size: 26px;
        font-weight: 700;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .service-box:hover h3 {
        color: var(--teal);
        transform: translateX(5px);
    }

    .service-icon {
        font-size: 32px;
        transition: all 0.3s;
    }

    .service-box:hover .service-icon {
        transform: rotate(360deg);
    }

    .service-box ul {
        list-style: none;
        padding-left: 0;
    }

    .service-box li {
        padding: 14px 0;
        color: var(--gray);
        border-bottom: 1px solid var(--light-gray);
        font-weight: 500;
        line-height: 1.6;
        transition: all 0.3s;
        display: flex;
        align-items: center;
    }

    .service-box li:last-child {
        border-bottom: none;
    }

    .service-box li:hover {
        color: var(--teal);
        transform: translateX(10px);
        background: linear-gradient(90deg, transparent, rgba(0, 169, 165, 0.05));
        padding-left: 10px;
    }

    .service-box li::before {
        content: '✓';
        color: var(--teal);
        font-weight: bold;
        margin-right: 15px;
        font-size: 16px;
        background: rgba(0, 169, 165, 0.1);
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .service-box li:hover::before {
        background: var(--teal);
        color: var(--white);
        transform: rotate(360deg);
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        border-radius: 30px;
        padding: 80px 60px;
        text-align: center;
        margin-top: 60px;
        position: relative;
        overflow: hidden;
        animation: fadeIn 1s ease-out;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
        border-radius: 50%;
        opacity: 0.2;
        animation: float 10s infinite alternate;
    }

    .cta-section::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -20%;
        width: 400px;
        height: 400px;
        background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
        border-radius: 50%;
        opacity: 0.2;
        animation: float 10s infinite alternate-reverse;
    }

    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(30px, 30px) rotate(10deg); }
    }

    .cta-section h2 {
        color: var(--white);
        font-size: 42px;
        margin-bottom: 20px;
        font-weight: 800;
        position: relative;
        z-index: 2;
        animation: slideUp 0.8s ease-out;
    }

    .cta-section p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 18px;
        max-width: 600px;
        margin: 0 auto 30px;
        position: relative;
        z-index: 2;
        animation: slideUp 0.8s ease-out 0.2s both;
    }

    .cta-btn {
        background: var(--white);
        color: var(--navy);
        padding: 16px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        font-size: 18px;
        display: inline-block;
        position: relative;
        z-index: 2;
        overflow: hidden;
        animation: slideUp 0.8s ease-out 0.4s both;
    }

    .cta-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: var(--teal);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
        z-index: -1;
    }

    .cta-btn:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        color: var(--white);
    }

    .cta-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Feature Badges */
    .feature-badge {
        display: inline-block;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        color: var(--white);
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        margin-right: 10px;
        margin-bottom: 10px;
        box-shadow: 0 2px 10px rgba(0, 169, 165, 0.3);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .hero h1 {
            font-size: 42px;
        }
        
        .hero p {
            font-size: 18px;
        }
        
        .cta-section h2 {
            font-size: 36px;
        }
    }

    @media (max-width: 768px) {
        .hero {
            padding: 80px 20px;
            background-attachment: scroll;
        }
        
        .hero h1 {
            font-size: 32px;
        }
        
        .hero p {
            font-size: 16px;
        }
        
        .section {
            padding: 50px 20px;
        }
        
        .section-title {
            font-size: 32px;
        }
        
        .services-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .service-box {
            padding: 30px;
        }
        
        .cta-section {
            padding: 50px 30px;
        }
        
        .cta-section h2 {
            font-size: 28px;
        }
        
        .cta-section p {
            font-size: 16px;
        }
        
        .cta-btn {
            padding: 14px 30px;
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .hero h1 {
            font-size: 28px;
        }
        
        .service-box h3 {
            font-size: 22px;
        }
        
        .service-box li {
            font-size: 14px;
        }
        
        .cta-section {
            padding: 40px 20px;
        }
        
        .cta-section h2 {
            font-size: 24px;
        }
    }

    /* Service Categories Banner */
    .service-banner {
        background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0, 169, 165, 0.1) 100%);
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 60px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        animation: fadeIn 1s ease-out;
    }
</style>

<!-- HERO SECTION WITH BACKGROUND IMAGE -->
<section class="hero">
    <h1>Medical Courier Services Designed for Healthcare Providers</h1>
    <p>Complete logistics solutions tailored to your facility's needs</p>
</section>

<!-- SERVICES SECTION -->
<section class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 30px;">
        <div>
            <h2 class="section-title">Our Service Offerings</h2>
            <p class="section-subtitle">From specimens to supplies, we handle all your healthcare logistics needs with precision and care.</p>
        </div>
    </div>

    <!-- Service Categories Banner -->
    <div class="service-banner">
        <span class="feature-badge">HIPAA Compliant</span>
        <span class="feature-badge">Temperature Controlled</span>
        <span class="feature-badge">STAT Available</span>
        <span class="feature-badge">24/7 Service</span>
        <span class="feature-badge">Chain of Custody</span>
        <span class="feature-badge">Certified Couriers</span>
    </div>

    <div class="services-grid">
        <div class="service-box">
            <h3>
                <span class="service-icon">🧬</span>
                Specimen Delivery
            </h3>
            <ul>
                <li>Blood samples</li>
                <li>Urine samples</li>
                <li>Swabs & cultures</li>
                <li>Biopsy kits</li>
                <li>Lab results</li>
                <li>Chain-of-custody handling</li>
                <li>STAT delivery available</li>
            </ul>
        </div>

        <div class="service-box">
            <h3>
                <span class="service-icon">📦</span>
                Medical Supply Delivery
            </h3>
            <ul>
                <li>Pathology kits</li>
                <li>Lab supplies</li>
                <li>PPE (Personal Protective Equipment)</li>
                <li>Office supplies</li>
                <li>Medical equipment</li>
                <li>Urgent supply runs</li>
            </ul>
        </div>

        <div class="service-box">
            <h3>
                <span class="service-icon">💊</span>
                Pharmacy Delivery
            </h3>
            <ul>
                <li>Medications</li>
                <li>Vaccines</li>
                <li>Prescription transport</li>
                <li>Facility-to-patient delivery</li>
                <li>Controlled substances</li>
                <li>Refrigerated medications</li>
            </ul>
        </div>

        <div class="service-box">
            <h3>
                <span class="service-icon">📄</span>
                Document Transport
            </h3>
            <ul>
                <li>Medical forms</li>
                <li>Secure lab paperwork</li>
                <li>Patient files</li>
                <li>HIPAA compliant handling</li>
                <li>Confidential records</li>
                <li>Legal documentation</li>
            </ul>
        </div>

        <div class="service-box">
            <h3>
                <span class="service-icon">⚡</span>
                STAT & Emergency
            </h3>
            <ul>
                <li>Urgent same-day service</li>
                <li>Priority handling</li>
                <li>Expedited transport</li>
                <li>24/7 availability</li>
                <li>Critical sample runs</li>
                <li>Emergency logistics</li>
            </ul>
        </div>

        <div class="service-box">
            <h3>
                <span class="service-icon">🎯</span>
                Specialized Services
            </h3>
            <ul>
                <li>Temperature-controlled transport</li>
                <li>After-hours delivery</li>
                <li>Multiple stops (one route)</li>
                <li>Custom scheduling</li>
                <li>Weekend service</li>
                <li>Direct clinic partnerships</li>
            </ul>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section">
        <h2>Ready to Get Started?</h2>
        <p>Experience reliable, HIPAA-compliant medical courier services tailored to your healthcare facility's needs.</p>
        <a href="{{ route('pickup.create') }}" class="cta-btn">
            <i class="fas fa-calendar-alt mr-2"></i>Request a Pickup Today
        </a>
    </div>
</section>

<!-- Additional Features Section -->
<section class="section" style="padding-top: 0;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
        <div style="text-align: center; animation: fadeInUp 0.8s ease-out;">
            <div style="font-size: 48px; margin-bottom: 20px; color: var(--teal);">🚚</div>
            <h4 style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: var(--navy);">Same-Day Delivery</h4>
            <p style="color: var(--gray); line-height: 1.6;">Guaranteed same-day service for urgent medical specimens and supplies.</p>
        </div>
        
        <div style="text-align: center; animation: fadeInUp 0.8s ease-out 0.2s both;">
            <div style="font-size: 48px; margin-bottom: 20px; color: var(--teal);">🌡️</div>
            <h4 style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: var(--navy);">Temperature Controlled</h4>
            <p style="color: var(--gray); line-height: 1.6;">Refrigerated and ambient transport options for temperature-sensitive materials.</p>
        </div>
        
        <div style="text-align: center; animation: fadeInUp 0.8s ease-out 0.4s both;">
            <div style="font-size: 48px; margin-bottom: 20px; color: var(--teal);">🔒</div>
            <h4 style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: var(--navy);">Secure Handling</h4>
            <p style="color: var(--gray); line-height: 1.6;">Chain-of-custody protocols and HIPAA-compliant documentation.</p>
        </div>
        
        <div style="text-align: center; animation: fadeInUp 0.8s ease-out 0.6s both;">
            <div style="font-size: 48px; margin-bottom: 20px; color: var(--teal);">📱</div>
            <h4 style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: var(--navy);">Real-Time Tracking</h4>
            <p style="color: var(--gray); line-height: 1.6;">Track your deliveries in real-time with instant notifications.</p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe service boxes and feature items
    document.querySelectorAll('.service-box, .service-banner, .cta-section, [style*="animation: fadeInUp"]').forEach(el => {
        observer.observe(el);
    });

    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const hero = document.querySelector('.hero');
        const scrolled = window.pageYOffset;
        if (hero) {
            hero.style.backgroundPositionY = (scrolled * 0.3) + 'px';
        }
    });

    // Add hover sound effect (optional, can be removed if not wanted)
    const serviceBoxes = document.querySelectorAll('.service-box');
    serviceBoxes.forEach(box => {
        box.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
    });
});
</script>
@endsection