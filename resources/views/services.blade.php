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

    .service-box h3 {
        color: var(--navy);
        margin-bottom: 25px;
        font-size: 24px;
        font-weight: 700;
    }

    .service-box ul {
        list-style: none;
        padding-left: 0;
    }

    .service-box li {
        padding: 12px 0;
        color: var(--gray);
        border-bottom: 1px solid var(--light-gray);
        font-weight: 500;
        line-height: 1.5;
    }

    .service-box li:last-child {
        border-bottom: none;
    }

    .service-box li::before {
        content: '→ ';
        color: var(--teal);
        font-weight: bold;
        margin-right: 10px;
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
        
        .services-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .service-box {
            padding: 25px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Medical Courier Services Designed for Healthcare Providers</h1>
    <p>Complete logistics solutions tailored to your facility's needs</p>
</section>

<!-- SERVICES SECTION -->
<section class="section">
    <h2 class="section-title">Our Service Offerings</h2>
    <p class="section-subtitle">From specimens to supplies, we handle all your healthcare logistics needs with precision and care.</p>

    <div class="services-grid">
        <div class="service-box">
            <h3>🧬 Specimen Delivery</h3>
            <ul>
                <li>Blood samples</li>
                <li>Urine samples</li>
                <li>Swabs</li>
                <li>Biopsy kits</li>
                <li>Lab results</li>
                <li>Chain-of-custody handling</li>
                <li>STAT delivery available</li>
            </ul>
        </div>

        <div class="service-box">
            <h3>📦 Medical Supply Delivery</h3>
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
            <h3>💊 Pharmacy Delivery</h3>
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
            <h3>📄 Document Transport</h3>
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
            <h3>⚡ STAT & Emergency</h3>
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
            <h3>🎯 Specialized Services</h3>
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

    <div class="text-center">
        <a href="/schedule-pickup" class="cta-btn">Request a Pickup Today</a>
    </div>
</section>
@endsection