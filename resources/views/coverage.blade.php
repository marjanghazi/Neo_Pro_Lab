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

    .coverage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
        margin-top: 50px;
    }

    .coverage-item {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        border: 2.5px solid transparent;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .coverage-item:hover {
        border-color: var(--teal);
        background: linear-gradient(135deg, rgba(0, 169, 165, 0.05) 0%, var(--light-gray) 100%);
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 169, 165, 0.15);
    }

    .coverage-item h4 {
        color: var(--navy);
        margin-bottom: 10px;
        font-weight: 700;
        font-size: 20px;
    }

    .coverage-item p {
        color: var(--gray);
        font-weight: 500;
    }

    .info-box {
        background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0, 169, 165, 0.05) 100%);
        padding: 50px 40px;
        border-radius: 12px;
        margin-top: 60px;
        text-align: center;
    }

    .info-box h3 {
        color: var(--navy);
        font-size: 24px;
        margin-bottom: 15px;
        font-weight: 700;
    }

    .info-box p {
        color: var(--gray);
        font-size: 16px;
        font-weight: 500;
        max-width: 600px;
        margin: 0 auto 30px auto;
        line-height: 1.6;
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

    .hours-box {
        background: var(--light-gray);
        padding: 40px;
        border-radius: 12px;
        margin-top: 40px;
    }

    .hours-box h3 {
        color: var(--navy);
        font-size: 20px;
        margin-bottom: 15px;
        font-weight: 700;
    }

    .hours-box p {
        color: var(--gray);
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 8px;
        line-height: 1.5;
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
        
        .coverage-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .coverage-item {
            padding: 25px;
        }
        
        .info-box {
            padding: 30px 25px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Serving Massachusetts & Rhode Island</h1>
    <p>Expanded coverage to meet your healthcare logistics needs</p>
</section>

<!-- COVERAGE SECTION -->
<section class="section">
    <h2 class="section-title">Our Service Areas</h2>
    <p class="section-subtitle">We provide courier services across Massachusetts and Rhode Island with fast response times.</p>

    <div class="coverage-grid">
        <div class="coverage-item">
            <h4>📍 Attleboro, MA</h4>
            <p>Primary hub location</p>
        </div>
        <div class="coverage-item">
            <h4>📍 North Attleboro, MA</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <h4>📍 Providence, RI</h4>
            <p>Full coverage area</p>
        </div>
        <div class="coverage-item">
            <h4>📍 Pawtucket, RI</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <h4>📍 Plainville, MA</h4>
            <p>Service available</p>
        </div>
        <div class="coverage-item">
            <h4>📍 Seekonk, MA</h4>
            <p>Service available</p>
        </div>
    </div>

    <!-- ORIGINAL CONTENT PRESERVED -->
    <!-- <div class="info-box" style="margin-top: 40px;">
        <h3>🏙️ Our Service Coverage</h3>
        <p>We proudly serve the following regions:</p>
        <ul style="list-style: none; padding-left: 0; columns: 2; column-gap: 40px;">
            @foreach ($areas ?? ['Attleboro, MA', 'North Attleboro, MA', 'Providence, RI', 'Pawtucket, RI', 'Plainville, MA', 'Seekonk, MA'] as $area)
                <li style="padding: 8px 0; color: var(--gray);">✅ {{ $area }}</li>
            @endforeach
        </ul>
        <p style="margin-top: 25px; margin-bottom: 0;">
            Looking for coverage in a new area? Contact us for expansion options.
        </p>
    </div> -->

    <div class="info-box">
        <h3>📦 Service Beyond Our Primary Areas</h3>
        <p>We offer expanded service to surrounding areas! Contact us for availability in your zip code. Additional mileage rates apply.</p>
        <a href="/contact" class="cta-btn">Request Service in Your Area</a>
    </div>

    <div class="hours-box">
        <h3>🕒 Service Hours</h3>
        <p><strong>Monday-Friday:</strong> 8:00 AM – 6:00 PM</p>
        <p><strong>After-Hours & Weekends:</strong> Available with surcharge</p>
    </div>
</section>
@endsection