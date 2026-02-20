@extends('layouts.main')

@section('content')
<!-- INSURANCE AND CERTIFICATIONS PAGE -->
<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
    }

    /* Hero Section */
    .insurance-hero {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        color: var(--white);
        padding: 100px 30px 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .insurance-hero::before {
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

    .insurance-hero::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -10%;
        width: 400px;
        height: 400px;
        background-color: rgba(0, 169, 165, 0.1);
        border-radius: 50%;
        z-index: 0;
    }

    .insurance-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .insurance-hero h1 {
        font-size: 52px;
        margin-bottom: 25px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -1px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .insurance-hero p {
        font-size: 22px;
        margin-bottom: 40px;
        opacity: 0.95;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        font-weight: 500;
    }

    .hero-badges {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        color: var(--white);
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s;
    }

    .hero-badge:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }

    .hero-badge i {
        margin-right: 8px;
        color: var(--teal);
    }

    /* Main Content */
    .insurance-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 80px 30px;
    }

    .insurance-section {
        background: var(--white);
        border-radius: 20px;
        padding: 60px;
        margin-bottom: 40px;
        border: 1px solid var(--light-gray);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid var(--teal);
    }

    .section-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 28px;
        box-shadow: 0 10px 20px rgba(0, 169, 165, 0.2);
    }

    .section-header h2 {
        color: var(--navy);
        font-size: 36px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .section-header h2 span {
        color: var(--teal);
        font-size: 16px;
        font-weight: 500;
        display: block;
        margin-top: 5px;
        letter-spacing: normal;
    }

    .insurance-section h3 {
        color: var(--navy);
        font-size: 24px;
        margin: 40px 0 20px;
        font-weight: 700;
        position: relative;
        padding-left: 20px;
    }

    .insurance-section h3::before {
        content: '';
        position: absolute;
        left: 0;
        top: 5px;
        bottom: 5px;
        width: 4px;
        background: var(--teal);
        border-radius: 2px;
    }

    .insurance-section p {
        color: var(--gray);
        line-height: 1.8;
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: 500;
    }

    /* Insurance Cards Grid */
    .insurance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }

    .insurance-card {
        background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
        padding: 30px;
        border-radius: 16px;
        border: 1px solid #e0e7f0;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .insurance-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--teal), #008B85);
        border-radius: 4px 4px 0 0;
    }

    .insurance-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 169, 165, 0.1);
        border-color: var(--teal);
    }

    .insurance-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 28px;
        margin-bottom: 20px;
    }

    .insurance-card h4 {
        color: var(--navy);
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .insurance-card p {
        color: var(--gray);
        font-size: 14px;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .coverage-amount {
        display: inline-block;
        background: var(--teal);
        color: var(--white);
        padding: 6px 15px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        margin-top: 15px;
    }

    /* Certification Badges */
    .cert-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 40px 0;
    }

    .cert-badge {
        background: var(--white);
        padding: 25px;
        border-radius: 16px;
        border: 2px solid var(--light-gray);
        transition: all 0.3s;
        text-align: center;
    }

    .cert-badge:hover {
        border-color: var(--teal);
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 169, 165, 0.1);
    }

    .cert-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 32px;
        margin: 0 auto 20px;
    }

    .cert-badge h4 {
        color: var(--navy);
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .cert-badge p {
        color: var(--gray);
        font-size: 13px;
        margin: 0;
    }

    .cert-badge .cert-id {
        color: var(--teal);
        font-size: 12px;
        font-weight: 600;
        margin-top: 10px;
    }

    /* Coverage Details */
    .coverage-details {
        background: var(--light-gray);
        border-radius: 16px;
        padding: 30px;
        margin: 30px 0;
    }

    .coverage-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #e0e7f0;
    }

    .coverage-item:last-child {
        border-bottom: none;
    }

    .coverage-item i {
        color: var(--teal);
        font-size: 20px;
        margin-top: 2px;
    }

    .coverage-item-content {
        flex: 1;
    }

    .coverage-item-content h5 {
        color: var(--navy);
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .coverage-item-content p {
        color: var(--gray);
        font-size: 14px;
        margin: 0;
    }

    /* Statistics */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
    }

    .stat-number {
        font-size: 36px;
        font-weight: 800;
        color: var(--teal);
        margin-bottom: 5px;
    }

    .stat-label {
        color: var(--gray);
        font-size: 14px;
        font-weight: 500;
    }

    /* Certificate of Insurance */
    .certificate-box {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 40px;
        border-radius: 16px;
        margin: 30px 0;
        text-align: center;
    }

    .certificate-box h4 {
        color: var(--white);
        font-size: 24px;
        margin-bottom: 15px;
    }

    .certificate-box p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 25px;
    }

    .btn-request {
        background: var(--teal);
        color: var(--white);
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-request:hover {
        background: #008B85;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 169, 165, 0.3);
    }

    /* Contact Section */
    .insurance-contact {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 60px;
        border-radius: 20px;
        margin-top: 50px;
        position: relative;
        overflow: hidden;
    }

    .insurance-contact::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(0, 169, 165, 0.2) 0%, transparent 70%);
        border-radius: 50%;
    }

    .insurance-contact::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(0, 169, 165, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .insurance-contact-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    .insurance-contact h3 {
        color: var(--white);
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .insurance-contact p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 18px;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .contact-methods {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
        margin: 40px 0;
    }

    .contact-method {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        padding: 20px 30px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s;
    }

    .contact-method:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .contact-method i {
        color: var(--teal);
        font-size: 20px;
    }

    .contact-method a {
        color: var(--white);
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
    }

    .insurance-footer {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
    }

    .insurance-footer strong {
        color: var(--teal);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .insurance-hero {
            padding: 60px 20px 40px;
        }
        .insurance-hero h1 {
            font-size: 36px;
        }
        .insurance-hero p {
            font-size: 18px;
        }
        .insurance-content {
            padding: 40px 20px;
        }
        .insurance-section {
            padding: 30px 20px;
        }
        .section-header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
        .section-header h2 {
            font-size: 28px;
        }
        .section-header h2 span {
            font-size: 14px;
        }
        .insurance-section h3 {
            font-size: 20px;
        }
        .insurance-grid, .cert-grid {
            grid-template-columns: 1fr;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .insurance-contact {
            padding: 40px 20px;
        }
        .insurance-contact h3 {
            font-size: 24px;
        }
        .contact-method {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .hero-badges {
            flex-direction: column;
            align-items: stretch;
        }
        .hero-badge {
            text-align: center;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="insurance-hero">
    <div class="insurance-hero-content">
        <h1>Insurance & Certifications</h1>
        <p>Fully Insured & Professionally Certified for Your Peace of Mind</p>
        <div class="hero-badges">
            <span class="hero-badge">
                <i class="fas fa-shield-alt"></i> $2M General Liability
            </span>
            <span class="hero-badge">
                <i class="fas fa-truck"></i> Commercial Auto
            </span>
            <span class="hero-badge">
                <i class="fas fa-box"></i> Cargo Coverage
            </span>
            <span class="hero-badge">
                <i class="fas fa-certificate"></i> HIPAA Certified
            </span>
        </div>
    </div>
</section>

<!-- INSURANCE CONTENT -->
<div class="insurance-content">
    <!-- Insurance Coverage Section -->
    <div class="insurance-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>
                Comprehensive Insurance Coverage
                <span>Protecting Your Shipments Every Step of the Way</span>
            </h2>
        </div>

        <p>NeoProLab Couriers maintains robust insurance coverage to protect your valuable medical shipments, specimens, and documents. Our policies are designed to provide complete peace of mind throughout the transportation process.</p>

        <div class="insurance-grid">
            <!-- General Liability -->
            <div class="insurance-card">
                <div class="insurance-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h4>General Liability</h4>
                <p>Comprehensive coverage protecting against third-party claims of bodily injury, property damage, and personal injury arising from our operations.</p>
                <div class="coverage-amount">$2,000,000 per occurrence</div>
            </div>

            <!-- Commercial Auto -->
            <div class="insurance-card">
                <div class="insurance-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h4>Commercial Auto</h4>
                <p>Full coverage for our fleet of courier vehicles, protecting against accidents, damage, and liability during transit.</p>
                <div class="coverage-amount">$1,000,000 combined single limit</div>
            </div>

            <!-- Cargo / Specimen Coverage -->
            <div class="insurance-card">
                <div class="insurance-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h4>Cargo & Specimen Coverage</h4>
                <p>Specialized coverage for medical specimens, lab samples, documents, and other cargo during transport.</p>
                <div class="coverage-amount">$100,000 per shipment</div>
            </div>

            <!-- Worker's Compensation -->
            <div class="insurance-card">
                <div class="insurance-icon">
                    <i class="fas fa-hard-hat"></i>
                </div>
                <h4>Worker's Compensation</h4>
                <p>State-mandated coverage protecting our employees in case of work-related injuries or illnesses.</p>
                <div class="coverage-amount">Statutory limits</div>
            </div>

            <!-- Umbrella Liability -->
            <div class="insurance-card">
                <div class="insurance-icon">
                    <i class="fas fa-umbrella"></i>
                </div>
                <h4>Umbrella Liability</h4>
                <p>Additional liability coverage extending beyond primary policy limits for extra protection.</p>
                <div class="coverage-amount">$5,000,000 aggregate</div>
            </div>

            <!-- Cyber Liability -->
            <div class="insurance-card">
                <div class="insurance-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <h4>Cyber Liability</h4>
                <p>Coverage for data breaches and cyber incidents affecting electronic health information.</p>
                <div class="coverage-amount">$1,000,000 per incident</div>
            </div>
        </div>

        <div class="highlight-box" style="margin-top: 30px;">
            <p>📋 Certificate of Insurance available upon request. All policies are underwritten by A-rated carriers.</p>
        </div>
    </div>

    <!-- Coverage Details -->
    <div class="insurance-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h2>What Our Coverage Includes</h2>
        </div>

        <div class="coverage-details">
            <div class="coverage-item">
                <i class="fas fa-check-circle"></i>
                <div class="coverage-item-content">
                    <h5>Specimen Transport</h5>
                    <p>Full coverage for blood, tissue, urine, and other medical specimens during transport, including temperature-sensitive materials.</p>
                </div>
            </div>
            <div class="coverage-item">
                <i class="fas fa-check-circle"></i>
                <div class="coverage-item-content">
                    <h5>Medical Records</h5>
                    <p>Protection for confidential patient documents and medical records, including HIPAA-compliant handling.</p>
                </div>
            </div>
            <div class="coverage-item">
                <i class="fas fa-check-circle"></i>
                <div class="coverage-item-content">
                    <h5>Lab Equipment</h5>
                    <p>Coverage for medical equipment and devices transported between facilities.</p>
                </div>
            </div>
            <div class="coverage-item">
                <i class="fas fa-check-circle"></i>
                <div class="coverage-item-content">
                    <h5>Pharmaceuticals</h5>
                    <p>Protection for prescription medications and pharmaceutical samples.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Certifications Section -->
    <div class="insurance-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <h2>
                Professional Certifications
                <span>All Couriers Are Fully Certified</span>
            </h2>
        </div>

        <p>Every NeoProLab courier undergoes rigorous training and holds current certifications in critical areas:</p>

        <div class="cert-grid">
            <!-- HIPAA -->
            <div class="cert-badge">
                <div class="cert-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>HIPAA Certified</h4>
                <p>Complete training in HIPAA privacy and security rules for handling protected health information.</p>
                <div class="cert-id">Certification #: HIPAA-2024-001</div>
            </div>

            <!-- OSHA -->
            <div class="cert-badge">
                <div class="cert-icon">
                    <i class="fas fa-hard-hat"></i>
                </div>
                <h4>OSHA Certified</h4>
                <p>Occupational Safety and Health Administration workplace safety standards.</p>
                <div class="cert-id">Certification #: OSHA-2024-089</div>
            </div>

            <!-- BBP -->
            <div class="cert-badge">
                <div class="cert-icon">
                    <i class="fas fa-biohazard"></i>
                </div>
                <h4>Bloodborne Pathogens</h4>
                <p>Specialized training in handling and transport of potentially infectious materials.</p>
                <div class="cert-id">Certification #: BBP-2024-234</div>
            </div>

            <!-- CPR -->
            <div class="cert-badge">
                <div class="cert-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h4>CPR Certified</h4>
                <p>Current CPR and First Aid certification for emergency response.</p>
                <div class="cert-id">Certification #: CPR-2024-567</div>
            </div>

            <!-- Safety Protocols -->
            <div class="cert-badge">
                <div class="cert-icon">
                    <i class="fas fa-traffic-light"></i>
                </div>
                <h4>Safety Protocols</h4>
                <p>Advanced defensive driving and vehicle safety certification.</p>
                <div class="cert-id">Certification #: SAFE-2024-890</div>
            </div>

            <!-- Hazardous Materials -->
            <div class="cert-badge">
                <div class="cert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4>Hazmat Certified</h4>
                <p>DOT hazardous materials handling and transport certification.</p>
                <div class="cert-id">Certification #: HAZ-2024-123</div>
            </div>
        </div>

        <div class="highlight-box">
            <p>✅ All certifications are current and renewed annually. Continuing education is required for all couriers.</p>
        </div>
    </div>

    <!-- Training Statistics -->
    <div class="insurance-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h2>Our Commitment to Excellence</h2>
        </div>

        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Hours of Annual Training</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Couriers Certified</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">0</div>
                <div class="stat-label">HIPAA Violations</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Compliance Support</div>
            </div>
        </div>
    </div>

    <!-- Certificate of Insurance -->
    <div class="certificate-box">
        <h4>Request a Certificate of Insurance</h4>
        <p>Need proof of insurance for your records or compliance department? We can provide a certificate of insurance showing our current coverage limits and policy details.</p>
        <button class="btn-request" onclick="window.location.href='{{ route('contact') }}?subject=Insurance%20Certificate%20Request'">
            <i class="fas fa-file-pdf"></i> Request Certificate
        </button>
    </div>

    <!-- Why Choose Us -->
    <div class="insurance-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-star"></i>
            </div>
            <h2>Why Our Coverage Matters</h2>
        </div>

        <div class="insurance-grid">
            <div class="term-card" style="padding: 20px;">
                <h4 style="margin-bottom: 10px;">Peace of Mind</h4>
                <p style="font-size: 14px;">Comprehensive coverage ensures your valuable medical shipments are protected at all times.</p>
            </div>
            <div class="term-card" style="padding: 20px;">
                <h4 style="margin-bottom: 10px;">Compliance Ready</h4>
                <p style="font-size: 14px;">Meet healthcare facility and insurance requirements with our verified coverage.</p>
            </div>
            <div class="term-card" style="padding: 20px;">
                <h4 style="margin-bottom: 10px;">Professional Standards</h4>
                <p style="font-size: 14px;">Certified couriers demonstrate our commitment to excellence and safety.</p>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="insurance-contact">
        <div class="insurance-contact-content">
            <h3>Questions About Our Insurance or Certifications?</h3>
            <p>Our compliance team is ready to provide detailed information about our coverage and certifications.</p>

            <div class="contact-methods">
                <div class="contact-method">
                    <i class="fas fa-phone-alt"></i>
                    <a href="tel:7742970597">(774) 297-0597</a>
                </div>
                <div class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:compliance@neoprolab.com">compliance@neoprolab.com</a>
                </div>
            </div>

            <div class="contact-method" style="justify-content: center; background: rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 15px; max-width: 300px; margin: 0 auto;">
                <i class="fas fa-building"></i>
                <span>NeoProLab Couriers LLC<br>Compliance Department</span>
            </div>

            <div class="insurance-footer">
                <p><strong>Insurance Provider:</strong> Liberty Mutual Insurance</p>
                <p><strong>Policy Numbers:</strong> Available upon request</p>
                <p><strong>Last Updated:</strong> January 1, 2024</p>
            </div>
        </div>
    </div>
</div>

<!-- Request Form Simulation (Non-functional, just for design) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any interactive functionality here
        const requestBtn = document.querySelector('.btn-request');
        if (requestBtn) {
            requestBtn.addEventListener('click', function(e) {
                // This will redirect to contact page with subject
                // The actual functionality is handled by the onclick attribute
            });
        }
    });
</script>
@endsection