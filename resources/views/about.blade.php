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
            url('https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1200&q=80');
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
        0% {
            background-size: 100%;
        }

        100% {
            background-size: 110%;
        }
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
        0% {
            opacity: 0.7;
        }

        100% {
            opacity: 0.85;
        }
    }

    .hero h1 {
        font-size: 56px;
        margin-bottom: 25px;
        font-weight: 800;
        position: relative;
        z-index: 2;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
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

    /* About Content Styles */
    .about-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        margin-top: 30px;
    }

    /* --- NEW: Left-aligned images container --- */
    .about-images-left {
        display: flex;
        flex-direction: column;
        gap: 30px;
        animation: fadeInLeft 1s ease-out;
    }

    .about-image {
        position: relative;
        height: 250px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        transition: all 0.5s;
        width: 100%;
    }

    .about-image:hover {
        transform: scale(1.02);
        box-shadow: 0 30px 60px rgba(0, 169, 165, 0.3);
    }

    .about-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s;
    }

    .about-image:hover img {
        transform: scale(1.1);
    }

    .about-image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(13, 27, 42, 0.9), transparent);
        color: var(--white);
        padding: 20px;
        text-align: center;
        transform: translateY(100%);
        transition: transform 0.5s;
    }

    .about-image:hover .about-image-overlay {
        transform: translateY(0);
    }

    .about-image-overlay h4 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--teal);
    }

    .about-image-overlay p {
        font-size: 13px;
        opacity: 0.9;
        margin: 0;
    }

    .about-text {
        animation: fadeInRight 1s ease-out;
    }

    .about-text h3 {
        color: var(--navy);
        font-size: 28px;
        margin-bottom: 18px;
        margin-top: 30px;
        font-weight: 700;
        position: relative;
        display: inline-block;
    }

    .about-text h3::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--teal), #008B85);
        border-radius: 2px;
        animation: expandWidth 1s ease-out 0.5s both;
    }

    @keyframes expandWidth {
        from {
            width: 0;
        }

        to {
            width: 60px;
        }
    }

    .about-text p {
        color: var(--gray);
        margin-bottom: 20px;
        line-height: 1.9;
        font-weight: 500;
        font-size: 16px;
    }

    .badge {
        display: inline-block;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        color: var(--white);
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Certifications Section */
    .certifications {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 40px;
        border-radius: 20px;
        margin-top: 40px;
        border-left: 5px solid var(--teal);
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.15);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .certifications:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0, 169, 165, 0.25);
    }

    .certifications::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
        border-radius: 50%;
        opacity: 0.1;
        animation: float 6s infinite alternate;
    }

    @keyframes float {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        100% {
            transform: translate(20px, 20px) rotate(10deg);
        }
    }

    .certifications h4 {
        color: var(--navy);
        margin-bottom: 20px;
        font-weight: 700;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .certifications ul {
        list-style: none;
        padding-left: 0;
    }

    .certifications li {
        padding: 14px 0;
        color: var(--gray);
        font-weight: 500;
        border-bottom: 1px solid rgba(0, 169, 165, 0.1);
        transition: all 0.3s;
        display: flex;
        align-items: center;
    }

    .certifications li:last-child {
        border-bottom: none;
    }

    .certifications li:hover {
        color: var(--teal);
        transform: translateX(10px);
        background: linear-gradient(90deg, transparent, rgba(0, 169, 165, 0.05));
        padding-left: 10px;
    }

    .certifications li::before {
        content: '✓';
        color: var(--teal);
        font-weight: bold;
        margin-right: 15px;
        font-size: 18px;
        background: rgba(0, 169, 165, 0.1);
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .certifications li:hover::before {
        background: var(--teal);
        color: var(--white);
        transform: rotate(360deg);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 40px 30px;
        border-radius: 20px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .stat-card:nth-child(1) {
        animation-delay: 0.2s;
    }

    .stat-card:nth-child(2) {
        animation-delay: 0.4s;
    }

    .stat-card:nth-child(3) {
        animation-delay: 0.6s;
    }

    .stat-card:nth-child(4) {
        animation-delay: 0.8s;
    }

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

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(0, 169, 165, 0.1), transparent);
        transform: rotate(45deg);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%) rotate(45deg);
        }

        100% {
            transform: translateX(100%) rotate(45deg);
        }
    }

    .stat-card:hover {
        transform: translateY(-15px) scale(1.05);
        box-shadow: 0 30px 60px rgba(0, 169, 165, 0.3);
    }

    .stat-card h3 {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 15px;
        color: var(--teal);
        position: relative;
        z-index: 1;
        animation: countUp 2s ease-out;
    }

    @keyframes countUp {
        from {
            transform: scale(0.5);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .stat-card p {
        font-size: 18px;
        opacity: 0.9;
        position: relative;
        z-index: 1;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    /* Section Title */
    .section-title {
        font-size: 42px;
        color: var(--navy);
        margin-bottom: 20px;
        font-weight: 800;
        position: relative;
        display: inline-block;
        animation: fadeInLeft 0.8s ease-out;
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
        animation: expandWidthCenter 1s ease-out 0.3s both;
    }

    @keyframes expandWidthCenter {
        from {
            width: 0;
        }

        to {
            width: 80px;
        }
    }

    .text-center {
        text-align: center;
    }

    /* Animations */
    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .hero h1 {
            font-size: 42px;
        }

        .hero p {
            font-size: 18px;
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

        .about-content {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .about-images-left {
            order: 1;
        }

        .about-text {
            order: 2;
        }

        .about-image {
            height: 200px;
        }

        .section {
            padding: 50px 20px;
        }

        .section-title {
            font-size: 32px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .stat-card {
            padding: 30px;
        }

        .stat-card h3 {
            font-size: 36px;
        }
    }

    @media (max-width: 480px) {
        .hero h1 {
            font-size: 28px;
        }

        .about-image {
            height: 160px;
        }

        .certifications {
            padding: 25px;
        }
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        color: var(--white);
        padding: 14px 32px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: inline-block;
        position: relative;
        overflow: hidden;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(0, 169, 165, 0.4);
    }

    .btn-secondary {
        background: transparent;
        border: 2.5px solid var(--teal);
        color: var(--teal);
        padding: 14px 32px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: inline-block;
    }

    .btn-secondary:hover {
        background-color: var(--teal);
        color: var(--white);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(0, 169, 165, 0.3);
    }
</style>

<!-- HERO SECTION WITH BACKGROUND IMAGE -->
<section class="hero">
    <h1>A Modern, Reliable Medical Courier Built for Today's Healthcare</h1>
    <p>Serving 878 Washington street #19 , Attleboro , Ma 02703 with excellence since our founding</p>
</section>

<!-- ABOUT CONTENT SECTION -->
<section class="section">
    <div class="about-content">
        <!-- LEFT SIDE: JPEG + USPLASH IMAGES (stacked vertically) -->
        <div class="about-images-left">
            <div class="about-image">
                <img src="{{ asset('images/AboutGallery/About.jpeg') }}" alt="Medical courier team in action">
                <div class="about-image-overlay">
                    <h4>Your Trusted Medical Courier Partner</h4>
                    <p>Delivering excellence in medical logistics across Massachusetts and Rhode Island</p>
                </div>
            </div>
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1200&q=80" alt="Startup team working">
                <div class="about-image-overlay">
                    <h4>Professional Medical Courier Team</h4>
                    <p>Certified and trained in HIPAA compliance and medical specimen handling</p>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: TEXT CONTENT -->
        <div class="about-text">
            <span class="badge">About NeoProlab</span>
            <h3>Who We Are</h3>
            <p>NeoProlab Couriers LLC is a specialized medical delivery service based in Massachusetts, focused on providing fast, safe, and compliant transport for healthcare facilities of all sizes. Our team brings years of experience in medical logistics and patient care.</p>

            <h3>Our Commitment</h3>
            <p>We understand the critical nature of medical specimens and time-sensitive materials. Our team is trained in HIPAA, BBP (Bloodborne Pathogens), and medical specimen handling to maintain chain-of-custody and ensure accuracy at every step. Every delivery is handled with the utmost care and professionalism.</p>

            <h3>Our Mission</h3>
            <p><strong style="color: var(--teal); font-size: 18px;">"To deliver medical materials with accuracy, speed, and complete compliance—improving patient outcomes through reliable logistics."</strong></p>

            <div class="certifications">
                <h4>🏆 Our Certifications & Training</h4>
                <ul>
                    <li>HIPAA Certified & Compliant</li>
                    <li>Bloodborne Pathogens (BBP) Training</li>
                    <li>CPR Certified & First Aid</li>
                    <li>Medical Specimen Transport Certified</li>
                    <li>Nursing Assistant Certified (CNA)</li>
                    <li>Chain of Custody Protocol Expert</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- TRACK RECORD SECTION -->
<section class="section text-center" style="background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0, 169, 165, 0.05) 100%);">
    <h2 class="section-title">Our Track Record</h2>
    <p style="color: var(--gray); font-size: 18px; margin-bottom: 40px; max-width: 700px; margin-left: auto; margin-right: auto;">
        Numbers speak louder than words. Here's why healthcare providers trust us with their critical deliveries.
    </p>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>10,000+</h3>
            <p>Successful Deliveries</p>
        </div>
        <div class="stat-card">
            <h3>99.8%</h3>
            <p>On-Time Rate</p>
        </div>
        <div class="stat-card">
            <h3>150+</h3>
            <p>Healthcare Partners</p>
        </div>
        <div class="stat-card">
            <h3>0</h3>
            <p>HIPAA Violations</p>
        </div>
    </div>
</section>

<!-- ORIGINAL CONTENT PRESERVED WITH ENHANCEMENTS -->
<section class="section">
    <div class="about-text" style="max-width: 900px; margin: 0 auto; text-align: center;">
        <span class="badge">Our Services</span>
        <h3 style="font-size: 32px; margin-bottom: 25px;">Comprehensive Medical Courier Services</h3>
        <p style="font-size: 18px; color: var(--gray); line-height: 1.8;">
            NeoProLab provides reliable, HIPAA-compliant medical courier services to labs,
            clinics, hospitals, nursing homes, and pharmacies. Our mission is to ensure
            safe, timely, and professional medical transport across Massachusetts and Rhode Island.
            From routine specimen pickups to emergency deliveries, we're here 24/7 to support
            healthcare providers in delivering the best patient care.
        </p>

        <div style="display: flex; gap: 20px; justify-content: center; margin-top: 40px; flex-wrap: wrap;">
            <a href="{{ route('services') }}" class="btn-primary">Explore Our Services</a>
            <a href="{{ route('contact') }}" class="btn-secondary">Contact Us Today</a>
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

        // Observe stat cards and other elements
        document.querySelectorAll('.stat-card, .about-image, .about-text, .certifications').forEach(el => {
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

        // Counter animation for stats
        const statCards = document.querySelectorAll('.stat-card h3');
        const animateValue = (element, start, end, duration) => {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const value = Math.floor(progress * (end - start) + start);
                element.textContent = value.toLocaleString() + (element.textContent.includes('+') ? '+' : '%');
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        };

        // Trigger counter animation when stats come into view
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statValue = entry.target;
                    const value = statValue.textContent.replace(/[^0-9.]/g, '');
                    const numericValue = parseFloat(value);
                    if (!isNaN(numericValue)) {
                        animateValue(statValue, 0, numericValue, 2000);
                    }
                    statsObserver.unobserve(statValue);
                }
            });
        }, {
            threshold: 0.5
        });

        statCards.forEach(card => statsObserver.observe(card));
    });
</script>
@endsection