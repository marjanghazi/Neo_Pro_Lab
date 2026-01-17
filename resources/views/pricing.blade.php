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

    .pricing-table {
        overflow-x: auto;
        margin-top: 50px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--white);
    }

    th {
        background: linear-gradient(90deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 18px;
        text-align: left;
        font-weight: 700;
        font-size: 15px;
    }

    td {
        padding: 18px;
        border-bottom: 2px solid var(--light-gray);
        color: var(--gray);
        font-weight: 500;
    }

    tr:hover {
        background-color: rgba(0, 169, 165, 0.05);
    }

    tr:last-child td {
        border-bottom: none;
    }

    .info-box {
        background: var(--light-gray);
        padding: 40px;
        border-radius: 12px;
        margin-top: 40px;
    }

    .info-box h3 {
        color: var(--navy);
        font-size: 22px;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .info-box p {
        color: var(--gray);
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 10px;
        line-height: 1.6;
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
        
        table {
            font-size: 14px;
        }
        
        td, th {
            padding: 12px 8px;
        }
        
        .info-box {
            padding: 25px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Transparent & Competitive Pricing</h1>
    <p>Simple, straightforward rates with no hidden fees</p>
</section>

<!-- PRICING SECTION -->
<section class="section">
    <h2 class="section-title">Standard Rate Schedule</h2>
    <p class="section-subtitle">Deliveries within 0–15 miles of our service area. Additional mileage billed at our per-mile rate.</p>

    <div class="pricing-table">
        <table>
            <thead>
                <tr>
                    <th>Service Item</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Base Trip (0–15 miles)</strong></td>
                    <td><strong style="color: var(--teal); font-size: 18px;">$50.00</strong></td>
                </tr>
                <tr>
                    <td>Mileage beyond 15 miles</td>
                    <td><strong>$2.00 per mile</strong></td>
                </tr>
                <tr>
                    <td>STAT / Urgent Delivery</td>
                    <td><strong>+$20.00</strong></td>
                </tr>
                <tr>
                    <td>Weekends/Holidays</td>
                    <td><strong>+35% of base rate</strong></td>
                </tr>
                <tr>
                    <td>Cold-Chain/Temperature Controlled</td>
                    <td><strong>+$7.00</strong></td>
                </tr>
                <tr>
                    <td>Additional Stop (Same Route)</td>
                    <td><strong>+$10.00 each</strong></td>
                </tr>
                <tr>
                    <td>Wait Time (after 10 minutes)</td>
                    <td><strong>$1.00 per minute</strong></td>
                </tr>
                <tr>
                    <td>Re-Attempt Fee</td>
                    <td><strong>$15.00</strong></td>
                </tr>
                <tr>
                    <td>Secure Signature Collection</td>
                    <td><strong>$5.00</strong></td>
                </tr>
                <tr>
                    <td>After-Hours Delivery (6PM-8AM)</td>
                    <td><strong>+$25.00</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="info-box">
        <h3>💰 Volume Discount</h3>
        <p>Facilities scheduling <span class="highlight">20+ trips monthly</span> receive a <span class="highlight">5% discount</span> on base trip rates. Contact us for bulk pricing.</p>
    </div>

    <div class="info-box">
        <h3>📋 Billing & Payment Options</h3>
        <p><strong>Flexible Billing:</strong> Daily • Weekly • Biweekly • Monthly</p>
        <p><strong>Payment Methods:</strong> ACH • Credit/Debit Cards • Business Checks</p>
    </div>

    <div class="info-box">
        <h3>🕒 Service Hours</h3>
        <p><strong>Standard Hours:</strong> Monday-Friday 8:00 AM – 6:00 PM</p>
        <p><strong>After-Hours & Weekend:</strong> Available with surcharge (call for rates)</p>
    </div>

    <!-- ORIGINAL CONTENT PRESERVED -->
    <div class="info-box">
        <h3>Custom Rate Quotes</h3>
        <p>
            Pricing varies based on distance, service type, STAT requests, and volume.
            Contact us for a custom rate quote.
        </p>
        <ul style="list-style: none; padding-left: 0;">
            <li style="padding: 8px 0; color: var(--gray);">✅ Scheduled Routes — custom pricing</li>
            <li style="padding: 8px 0; color: var(--gray);">✅ STAT Delivery — additional surcharge</li>
            <li style="padding: 8px 0; color: var(--gray);">✅ After-hours delivery — premium rate</li>
            <li style="padding: 8px 0; color: var(--gray);">✅ Volume discounts available</li>
        </ul>
    </div>
</section>
@endsection