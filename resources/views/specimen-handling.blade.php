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

    .section-title {
        font-size: 42px;
        color: var(--navy);
        margin-bottom: 18px;
        font-weight: 800;
    }

    .section-subtitle {
        font-size: 18px;
        color: var(--gray);
        margin-bottom: 50px;
        max-width: 700px;
        line-height: 1.6;
    }

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
        border-left: 5px solid var(--teal);
        transition: all 0.3s;
    }

    .feature-card:hover {
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.2);
        transform: translateY(-8px);
    }

    .feature-card h3 {
        color: var(--navy);
        margin-bottom: 15px;
        font-size: 22px;
        font-weight: 700;
    }

    .feature-card p {
        color: var(--gray);
        line-height: 1.8;
        font-weight: 500;
    }

    .icon {
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
        font-weight: bold;
    }

    .certifications {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 35px;
        border-radius: 12px;
        margin-top: 30px;
        border-left: 5px solid var(--teal);
        box-shadow: 0 10px 30px rgba(0, 169, 165, 0.1);
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
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
        line-height: 1.5;
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

    .cta-btn {
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

    .cta-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 169, 165, 0.4);
        text-decoration: none;
        color: var(--white);
    }

    .text-center {
        text-align: center;
        margin-top: 60px;
    }

    .highlight {
        color: var(--teal);
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 36px;
        }
        
        .hero {
            padding: 60px 20px;
        }
        
        .section {
            padding: 40px 20px;
        }
        
        .section-title {
            font-size: 32px;
        }
        
        .features-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .feature-card {
            padding: 25px;
        }
        
        .certifications {
            padding: 25px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Proper Chain-of-Custody & Specimen Safety</h1>
    <p>Industry-leading safety standards and compliance protocols</p>
</section>

<!-- SAFETY STANDARDS SECTION -->
<section class="section">
    <h2 class="section-title">Our Safety Standards</h2>
    <p class="section-subtitle">Every specimen is treated with the utmost care and precision.</p>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="icon">🌡️</div>
            <h3>Temperature-Controlled</h3>
            <p>Precise temperature management for all specimen types requiring specific storage conditions.</p>
        </div>
        <div class="feature-card">
            <div class="icon">🔐</div>
            <h3>Secure Sealed Transport</h3>
            <p>All specimens sealed and secured to prevent tampering or contamination.</p>
        </div>
        <div class="feature-card">
            <div class="icon">⚡</div>
            <h3>Timely Arrival</h3>
            <p>On-time delivery to maintain specimen integrity and lab accuracy.</p>
        </div>
        <div class="feature-card">
            <div class="icon">✓</div>
            <h3>Zero-Tamper Policy</h3>
            <p>Complete chain of custody with documented handoff at every step.</p>
        </div>
        <div class="feature-card">
            <div class="icon">📝</div>
            <h3>Documentation</h3>
            <p>Chain-of-custody forms included with every delivery.</p>
        </div>
        <div class="feature-card">
            <div class="icon">✓</div>
            <h3>CDC & OSHA Compliance</h3>
            <p>Full compliance with CDC and OSHA guidelines and regulations.</p>
        </div>
    </div>
</section>

<!-- DRIVER TRAINING SECTION -->
<section class="section" style="background: var(--light-gray);">
    <h2 class="section-title text-center">Driver Training & Certification</h2>
    <p class="section-subtitle text-center">All NeoProlab drivers receive comprehensive training in specialized medical handling.</p>

    <div class="certifications">
        <h4>🎓 Required Certifications</h4>
        <ul>
            <li>Specimen Packaging & Handling</li>
            <li>Biohazard Spill Response</li>
            <li>HIPAA Privacy Compliance</li>
            <li>Bloodborne Pathogens (BBP) Safety</li>
            <li>Chain-of-Custody Protocol</li>
            <li>Proper PPE Usage</li>
            <li>Emergency Response Procedures</li>
        </ul>
    </div>

    <!-- ORIGINAL CONTENT PRESERVED -->
    <div class="certifications" style="margin-top: 40px;">
        <h4>🔬 Our Comprehensive Protocols</h4>
        <p style="color: var(--gray); margin-bottom: 20px; line-height: 1.6;">
            We follow strict chain-of-custody, OSHA, HIPAA, and biohazard handling protocols. Our couriers are trained in:
        </p>
        <ul>
            <li>Specimen integrity preservation</li>
            <li>Temperature-controlled transport</li>
            <li>Secure documentation handling</li>
            <li>Biohazard awareness & OSHA standards</li>
        </ul>
    </div>

    <div class="text-center">
        <a href="/schedule-pickup" class="cta-btn">Download Chain-of-Custody Form</a>
    </div>
</section>
@endsection