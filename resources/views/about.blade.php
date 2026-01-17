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

    .hero {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        color: var(--white);
        padding: 80px 30px;
        text-align: center;
        margin-bottom: 60px;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
        font-weight: 800;
    }

    .hero p {
        font-size: 18px;
        opacity: 0.9;
        max-width: 800px;
        margin: 0 auto;
    }

    .section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 30px;
    }

    .about-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        margin-top: 30px;
    }

    .about-text h3 {
        color: var(--navy);
        font-size: 26px;
        margin-bottom: 18px;
        margin-top: 30px;
        font-weight: 700;
    }

    .about-text p {
        color: var(--gray);
        margin-bottom: 18px;
        line-height: 1.9;
        font-weight: 500;
    }

    .badge {
        display: inline-block;
        background-color: rgba(0, 169, 165, 0.1);
        color: var(--teal);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .certifications {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 35px;
        border-radius: 12px;
        margin-top: 30px;
        border-left: 5px solid var(--teal);
        box-shadow: 0 10px 30px rgba(0, 169, 165, 0.1);
    }

    .certifications h4 {
        color: var(--navy);
        margin-bottom: 20px;
        font-weight: 700;
        font-size: 18px;
    }

    .certifications ul {
        list-style: none;
        padding-left: 0;
    }

    .certifications li {
        padding: 12px 0;
        color: var(--gray);
        font-weight: 500;
        border-bottom: 1px solid rgba(0, 169, 165, 0.1);
    }

    .certifications li:last-child {
        border-bottom: none;
    }

    .certifications li::before {
        content: '✓ ';
        color: var(--teal);
        font-weight: bold;
        margin-right: 12px;
        font-size: 18px;
    }

    .about-image {
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 50%, var(--navy) 100%);
        height: 450px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 18px;
        text-align: center;
        padding: 40px;
        font-weight: 600;
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.2);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: scale(1.05);
    }

    .stat-card h3 {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 10px;
        color: var(--teal);
    }

    .stat-card p {
        font-size: 16px;
        opacity: 0.9;
    }

    .text-center {
        text-align: center;
    }

    @media (max-width: 768px) {
        .about-content {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .about-image {
            height: 300px;
        }
        
        .hero h1 {
            font-size: 36px;
        }
        
        .hero {
            padding: 60px 20px;
        }
        
        .section {
            padding: 40px 20px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <h1>A Modern, Reliable Medical Courier Built for Today's Healthcare</h1>
    <p>Serving Massachusetts & Rhode Island with excellence since our founding</p>
</section>

<!-- ABOUT CONTENT SECTION -->
<section class="section">
    <div class="about-content">
        <div class="about-image">
            🏥 Medical Courier Team Excellence
        </div>
        
        <div class="about-text">
            <span class="badge">About NeoProlab</span>
            <h3>Who We Are</h3>
            <p>NeoProlab Couriers LLC is a specialized medical delivery service based in Massachusetts, focused on providing fast, safe, and compliant transport for healthcare facilities of all sizes.</p>
            
            <h3>Our Commitment</h3>
            <p>We understand the critical nature of medical specimens and time-sensitive materials. Our team is trained in HIPAA, BBP (Bloodborne Pathogens), and medical specimen handling to maintain chain-of-custody and ensure accuracy at every step.</p>
            
            <h3>Our Mission</h3>
            <p><strong style="color: var(--teal);">To deliver medical materials with accuracy, speed, and complete compliance—improving patient outcomes through reliable logistics.</strong></p>

            <div class="certifications">
                <h4>🏆 Our Certifications</h4>
                <ul>
                    <li>HIPAA Certified</li>
                    <li>Bloodborne Pathogens (BBP) Training</li>
                    <li>CPR Certified</li>
                    <li>Medical Specimen Transport Certified</li>
                    <li>Nursing Assistant Certified</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- TRACK RECORD SECTION -->
<section class="section text-center" style="background: var(--light-gray);">
    <h2 style="font-size: 42px; color: var(--navy); margin-bottom: 15px; font-weight: 800;">Our Track Record</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>1000+</h3>
            <p>Successful Deliveries</p>
        </div>
        <div class="stat-card">
            <h3>99.8%</h3>
            <p>On-Time Rate</p>
        </div>
        <div class="stat-card">
            <h3>50+</h3>
            <p>Healthcare Partners</p>
        </div>
        <div class="stat-card">
            <h3>0</h3>
            <p>HIPAA Violations</p>
        </div>
    </div>
</section>

<!-- ORIGINAL CONTENT PRESERVED -->
<section class="section">
    <div class="about-text">
        <h3>Comprehensive Medical Courier Services</h3>
        <p>
            NeoProLab provides reliable, HIPAA-compliant medical courier services to labs,
            clinics, hospitals, nursing homes, and pharmacies. Our mission is to ensure
            safe, timely, and professional medical transport across New England.
        </p>
    </div>
</section>
@endsection