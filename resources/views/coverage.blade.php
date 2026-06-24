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

    /* Coverage Grid */
    .coverage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    .coverage-item {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 35px 25px;
        border-radius: 20px;
        text-align: center;
        border: 2.5px solid transparent;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }

    .coverage-item:nth-child(1) { animation-delay: 0.1s; }
    .coverage-item:nth-child(2) { animation-delay: 0.2s; }
    .coverage-item:nth-child(3) { animation-delay: 0.3s; }
    .coverage-item:nth-child(4) { animation-delay: 0.4s; }
    .coverage-item:nth-child(5) { animation-delay: 0.5s; }
    .coverage-item:nth-child(6) { animation-delay: 0.6s; }

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

    .coverage-item::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(0, 169, 165, 0.1), transparent);
        transform: rotate(45deg);
        animation: shimmer 3s infinite;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .coverage-item:hover::before {
        opacity: 1;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }

    .coverage-item:hover {
        border-color: var(--teal);
        background: linear-gradient(135deg, var(--white) 0%, rgba(0, 169, 165, 0.1) 100%);
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0, 169, 165, 0.2);
    }

    .coverage-icon {
        font-size: 48px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .coverage-item:hover .coverage-icon {
        transform: scale(1.2) rotate(360deg);
    }

    .coverage-item h4 {
        color: var(--navy);
        margin-bottom: 10px;
        font-weight: 700;
        font-size: 22px;
        transition: all 0.3s;
    }

    .coverage-item:hover h4 {
        color: var(--teal);
    }

    .coverage-item p {
        color: var(--gray);
        font-weight: 500;
        font-size: 14px;
        background: rgba(0, 169, 165, 0.1);
        display: inline-block;
        padding: 5px 15px;
        border-radius: 30px;
        transition: all 0.3s;
    }

    .coverage-item:hover p {
        background: var(--teal);
        color: var(--white);
    }

    /* Map Section */
    .map-container {
        margin-top: 60px;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        animation: fadeIn 1s ease-out;
        position: relative;
        height: 450px;
    }

    .map-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s;
    }

    .map-container:hover .map-image {
        transform: scale(1.05);
    }

    .map-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(13, 27, 42, 0.3), rgba(0, 169, 165, 0.3));
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s;
    }

    .map-container:hover .map-overlay {
        opacity: 1;
    }

    .map-text {
        background: var(--white);
        padding: 15px 30px;
        border-radius: 50px;
        color: var(--navy);
        font-weight: 700;
        font-size: 18px;
        transform: translateY(20px);
        transition: transform 0.4s;
    }

    .map-container:hover .map-text {
        transform: translateY(0);
    }

    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, var(--white) 0%, rgba(0, 169, 165, 0.05) 100%);
        padding: 60px 50px;
        border-radius: 30px;
        margin-top: 60px;
        text-align: center;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.4s;
        animation: fadeIn 1s ease-out 0.3s both;
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.1);
    }

    .info-box:hover {
        border-color: var(--teal);
        transform: scale(1.02);
        box-shadow: 0 30px 60px rgba(0, 169, 165, 0.2);
    }

    .info-box::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
        border-radius: 50%;
        opacity: 0.1;
        animation: float 10s infinite alternate;
    }

    .info-box::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -20%;
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
        border-radius: 50%;
        opacity: 0.1;
        animation: float 10s infinite alternate-reverse;
    }

    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(30px, 30px) rotate(10deg); }
    }

    .info-box h3 {
        color: var(--navy);
        font-size: 32px;
        margin-bottom: 20px;
        font-weight: 700;
        position: relative;
        z-index: 2;
    }

    .info-box p {
        color: var(--gray);
        font-size: 18px;
        font-weight: 500;
        max-width: 700px;
        margin: 0 auto 30px auto;
        line-height: 1.8;
        position: relative;
        z-index: 2;
    }

    .service-areas-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
        position: relative;
        z-index: 2;
    }

    .area-badge {
        background: var(--white);
        color: var(--navy);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.15);
        transition: all 0.3s;
        border: 2px solid transparent;
    }

    .area-badge:hover {
        background: var(--teal);
        color: var(--white);
        transform: translateY(-3px) scale(1.05);
        border-color: var(--white);
        box-shadow: 0 10px 25px rgba(0, 169, 165, 0.3);
    }

    .area-badge i {
        margin-right: 5px;
        color: var(--teal);
        transition: all 0.3s;
    }

    .area-badge:hover i {
        color: var(--white);
    }

    /* Hours Box */
    .hours-box {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        padding: 50px;
        border-radius: 30px;
        margin-top: 40px;
        color: var(--white);
        position: relative;
        overflow: hidden;
        animation: fadeIn 1s ease-out 0.4s both;
    }

    .hours-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0, 169, 165, 0.2) 0%, transparent 100%);
        animation: pulse 3s infinite;
    }

    @keyframes pulse {
        0% { opacity: 0.3; }
        50% { opacity: 0.6; }
        100% { opacity: 0.3; }
    }

    .hours-box h3 {
        font-size: 28px;
        margin-bottom: 25px;
        font-weight: 700;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .hours-content {
        display: flex;
        justify-content: center;
        gap: 50px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .hours-item {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        padding: 25px 35px;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        transition: all 0.3s;
        border: 2px solid transparent;
    }

    .hours-item:hover {
        transform: translateY(-5px);
        border-color: var(--teal);
        background: rgba(255, 255, 255, 0.15);
    }

    .hours-item i {
        font-size: 32px;
        color: var(--teal);
        margin-bottom: 15px;
        transition: all 0.3s;
    }

    .hours-item:hover i {
        transform: rotate(360deg);
    }

    .hours-item h4 {
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .hours-item p {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.8);
    }

    .surcharge-note {
        margin-top: 25px;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
        position: relative;
        z-index: 2;
    }

    /* CTA Button */
    .cta-btn {
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        color: var(--white);
        padding: 16px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(0, 169, 165, 0.3);
        font-size: 18px;
        display: inline-block;
        position: relative;
        overflow: hidden;
        z-index: 2;
    }

    .cta-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
        z-index: -1;
    }

    .cta-btn:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 20px 40px rgba(0, 169, 165, 0.4);
    }

    .cta-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .hero h1 {
            font-size: 42px;
        }
        
        .hero p {
            font-size: 18px;
        }
        
        .info-box h3 {
            font-size: 28px;
        }
        
        .hours-box h3 {
            font-size: 24px;
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
        
        .coverage-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .coverage-item {
            padding: 25px;
        }
        
        .info-box {
            padding: 40px 25px;
        }
        
        .info-box h3 {
            font-size: 24px;
        }
        
        .info-box p {
            font-size: 16px;
        }
        
        .hours-box {
            padding: 40px 25px;
        }
        
        .hours-content {
            flex-direction: column;
            gap: 20px;
        }
        
        .hours-item {
            width: 100%;
        }
        
        .map-container {
            height: 300px;
        }
        
        .service-areas-list {
            gap: 10px;
        }
        
        .area-badge {
            padding: 8px 15px;
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .hero h1 {
            font-size: 28px;
        }
        
        .section-title {
            font-size: 28px;
        }
        
        .coverage-icon {
            font-size: 36px;
        }
        
        .coverage-item h4 {
            font-size: 18px;
        }
        
        .info-box {
            padding: 30px 20px;
        }
        
        .hours-item {
            padding: 20px;
        }
        
        .hours-item i {
            font-size: 28px;
        }
        
        .cta-btn {
            padding: 14px 30px;
            font-size: 16px;
        }
    }
</style>

<!-- HERO SECTION WITH BACKGROUND IMAGE -->
<section class="hero">
    <h1>Serving 878 Washington street  #19 , Attleboro , Ma 02703</h1>
    <p>Expanded coverage to meet your healthcare logistics needs</p>
</section>

<!-- COVERAGE SECTION -->
<section class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 30px;">
        <div>
            <h2 class="section-title">Our Service Areas</h2>
            <p class="section-subtitle">We provide courier services across Massachusetts and Rhode Island with fast response times and reliable coverage.</p>
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-container">
        <img src="https://images.unsplash.com/photo-1569336415962-a4bd9f69cdc5?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
             alt="Massachusetts and Rhode Island map" 
             class="map-image">
        <div class="map-overlay">
            <span class="map-text">📍 MA & RI Coverage Area</span>
        </div>
    </div>

    <div class="coverage-grid">
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>Attleboro, MA</h4>
            <p>Primary hub location</p>
        </div>
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>North Attleboro, MA</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>Providence, RI</h4>
            <p>Full coverage area</p>
        </div>
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>Pawtucket, RI</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>Plainville, MA</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>Seekonk, MA</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>Taunton, MA</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <div class="coverage-icon">📍</div>
            <h4>Fall River, MA</h4>
            <p>Service available</p>
        </div>
    </div>

    <div class="info-box">
        <h3>📦 Service Beyond Our Primary Areas</h3>
        <p>We offer expanded service to surrounding areas! Contact us for availability in your zip code. Additional mileage rates apply.</p>
        
        <div class="service-areas-list">
            <span class="area-badge"><i class="fas fa-check-circle"></i> Bristol County</span>
            <span class="area-badge"><i class="fas fa-check-circle"></i> Norfolk County</span>
            <span class="area-badge"><i class="fas fa-check-circle"></i> Providence County</span>
            <span class="area-badge"><i class="fas fa-check-circle"></i> Kent County</span>
            <span class="area-badge"><i class="fas fa-check-circle"></i> Worcester County</span>
            <span class="area-badge"><i class="fas fa-check-circle"></i> Newport County</span>
        </div>
        
        <a href="{{ route('contact') }}" class="cta-btn">
            <i class="fas fa-paper-plane mr-2"></i>Request Service in Your Area
        </a>
    </div>

    <div class="hours-box">
        <h3>
            <i class="fas fa-clock"></i>
            Service Hours
        </h3>
        <div class="hours-content">
            <div class="hours-item">
                <i class="fas fa-calendar-week"></i>
                <h4>Monday - Friday</h4>
                <p>8:00 AM – 6:00 PM</p>
            </div>
            <div class="hours-item">
                <i class="fas fa-calendar-weekend"></i>
                <h4>Weekends & After-Hours</h4>
                <p>Available with surcharge</p>
            </div>
            <div class="hours-item">
                <i class="fas fa-clock"></i>
                <h4>STAT Service</h4>
                <p>24/7 Emergency</p>
            </div>
        </div>
        <p class="surcharge-note">* After-hours and weekend services available with prior arrangement. Additional fees apply.</p>
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

    // Observe elements for animation
    document.querySelectorAll('.coverage-item, .info-box, .hours-box, .map-container, .area-badge').forEach(el => {
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

    // Add hover animation for area badges
    const areaBadges = document.querySelectorAll('.area-badge');
    areaBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s';
        });
    });
});
</script>
@endsection