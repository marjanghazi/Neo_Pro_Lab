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
                    url('https://images.unsplash.com/photo-1554224154-22dec7ec8818?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
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

    /* Pricing Cards */
    .pricing-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    .pricing-card {
        background: var(--white);
        border-radius: 30px;
        padding: 40px 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 2px solid var(--light-gray);
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
    }

    .pricing-card:nth-child(1) { animation-delay: 0.1s; }
    .pricing-card:nth-child(2) { animation-delay: 0.2s; }
    .pricing-card:nth-child(3) { animation-delay: 0.3s; }

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

    .pricing-card:hover {
        transform: translateY(-15px) scale(1.02);
        border-color: var(--teal);
        box-shadow: 0 30px 60px rgba(0, 169, 165, 0.2);
    }

    .pricing-card.popular {
        border: 3px solid var(--teal);
        transform: scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 169, 165, 0.15);
    }

    .popular-badge {
        position: absolute;
        top: 20px;
        right: -30px;
        background: var(--teal);
        color: var(--white);
        padding: 8px 40px;
        transform: rotate(45deg);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 2px 10px rgba(0, 169, 165, 0.3);
    }

    .pricing-icon {
        font-size: 48px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .pricing-card:hover .pricing-icon {
        transform: scale(1.2) rotate(360deg);
    }

    .pricing-card h3 {
        font-size: 24px;
        color: var(--navy);
        margin-bottom: 15px;
        font-weight: 700;
    }

    .price {
        font-size: 48px;
        color: var(--teal);
        font-weight: 800;
        margin-bottom: 20px;
        line-height: 1;
    }

    .price span {
        font-size: 16px;
        color: var(--gray);
        font-weight: 500;
    }

    .pricing-features {
        list-style: none;
        padding: 0;
        margin: 25px 0;
    }

    .pricing-features li {
        padding: 12px 0;
        color: var(--gray);
        border-bottom: 1px solid var(--light-gray);
        font-weight: 500;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .pricing-features li:last-child {
        border-bottom: none;
    }

    .pricing-features li:hover {
        color: var(--teal);
        transform: translateX(5px);
    }

    .pricing-features li i {
        color: var(--teal);
        font-size: 14px;
    }

    /* Pricing Table */
    .pricing-table {
        overflow-x: auto;
        margin-top: 50px;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-out 0.4s both;
        background: var(--white);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--white);
    }

    th {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 20px;
        text-align: left;
        font-weight: 700;
        font-size: 16px;
        position: relative;
        overflow: hidden;
    }

    th::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        animation: tableShimmer 3s infinite;
    }

    @keyframes tableShimmer {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }

    td {
        padding: 18px 20px;
        border-bottom: 2px solid var(--light-gray);
        color: var(--gray);
        font-weight: 500;
        transition: all 0.3s;
    }

    tr {
        transition: all 0.3s;
    }

    tr:hover {
        background-color: rgba(0, 169, 165, 0.05);
    }

    tr:hover td {
        color: var(--navy);
    }

    tr:last-child td {
        border-bottom: none;
    }

    .rate-highlight {
        color: var(--teal);
        font-weight: 700;
        font-size: 18px;
        transition: all 0.3s;
    }

    tr:hover .rate-highlight {
        transform: scale(1.1);
        display: inline-block;
    }

    /* Info Boxes */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .info-box {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 40px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s;
        border: 2px solid transparent;
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
    }

    .info-box:nth-child(1) { animation-delay: 0.1s; }
    .info-box:nth-child(2) { animation-delay: 0.2s; }
    .info-box:nth-child(3) { animation-delay: 0.3s; }
    .info-box:nth-child(4) { animation-delay: 0.4s; }

    .info-box:hover {
        border-color: var(--teal);
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0, 169, 165, 0.15);
    }

    .info-box::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
        border-radius: 50%;
        opacity: 0.1;
        animation: float 10s infinite alternate;
    }

    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(20px, 20px) rotate(10deg); }
    }

    .info-icon {
        font-size: 42px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .info-box:hover .info-icon {
        transform: scale(1.2) rotate(360deg);
    }

    .info-box h3 {
        color: var(--navy);
        font-size: 24px;
        margin-bottom: 20px;
        font-weight: 700;
        position: relative;
        z-index: 2;
    }

    .info-box p {
        color: var(--gray);
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 15px;
        line-height: 1.7;
        position: relative;
        z-index: 2;
    }

    .highlight {
        color: var(--teal);
        font-weight: 700;
        background: rgba(0, 169, 165, 0.1);
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-block;
        transition: all 0.3s;
    }

    .highlight:hover {
        background: var(--teal);
        color: var(--white);
        transform: scale(1.1);
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin-top: 20px;
    }

    .feature-list li {
        padding: 10px 0;
        color: var(--gray);
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .feature-list li:hover {
        color: var(--teal);
        transform: translateX(10px);
    }

    .feature-list li i {
        color: var(--teal);
        font-size: 16px;
        transition: all 0.3s;
    }

    .feature-list li:hover i {
        transform: rotate(360deg);
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
        margin-top: 30px;
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
        
        .pricing-card.popular {
            transform: scale(1);
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
        
        .pricing-cards {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .pricing-card {
            padding: 30px 20px;
        }
        
        .price {
            font-size: 36px;
        }
        
        table {
            font-size: 14px;
        }
        
        td, th {
            padding: 12px 10px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .info-box {
            padding: 30px 25px;
        }
        
        .info-box h3 {
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        .hero h1 {
            font-size: 28px;
        }
        
        .section-title {
            font-size: 28px;
        }
        
        .rate-highlight {
            font-size: 16px;
        }
        
        .cta-btn {
            padding: 14px 30px;
            font-size: 16px;
            width: 100%;
            text-align: center;
        }
    }
</style>

<!-- HERO SECTION WITH BACKGROUND IMAGE -->
<section class="hero">
    <h1>Transparent & Competitive Pricing</h1>
    <p>Simple, straightforward rates with no hidden fees</p>
</section>

<!-- PRICING SECTION -->
<section class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 30px;">
        <div>
            <h2 class="section-title">Flexible Pricing Plans</h2>
            <p class="section-subtitle">Choose the plan that fits your facility's needs. Volume discounts available for high-volume shippers.</p>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="pricing-cards">
        <div class="pricing-card">
            <div class="pricing-icon">🚚</div>
            <h3>Pay-Per-Trip</h3>
            <div class="price">$50 <span>/ trip</span></div>
            <ul class="pricing-features">
                <li><i class="fas fa-check"></i> 0-15 miles included</li>
                <li><i class="fas fa-check"></i> No monthly commitment</li>
                <li><i class="fas fa-check"></i> Ideal for occasional use</li>
                <li><i class="fas fa-check"></i> Online tracking</li>
                <li><i class="fas fa-check"></i> HIPAA compliant</li>
            </ul>
        </div>

        <div class="pricing-card popular">
            <div class="popular-badge">MOST POPULAR</div>
            <div class="pricing-icon">📦</div>
            <h3>Volume Discount</h3>
            <div class="price">$47.50 <span>/ trip</span></div>
            <ul class="pricing-features">
                <li><i class="fas fa-check"></i> 20+ trips per month</li>
                <li><i class="fas fa-check"></i> <span class="highlight">5% discount</span></li>
                <li><i class="fas fa-check"></i> Priority scheduling</li>
                <li><i class="fas fa-check"></i> Dedicated account manager</li>
                <li><i class="fas fa-check"></i> Monthly invoicing</li>
            </ul>
        </div>

        <div class="pricing-card">
            <div class="pricing-icon">⚡</div>
            <h3>STAT Service</h3>
            <div class="price">$70 <span>/ trip</span></div>
            <ul class="pricing-features">
                <li><i class="fas fa-check"></i> Urgent/emergency delivery</li>
                <li><i class="fas fa-check"></i> Priority handling</li>
                <li><i class="fas fa-check"></i> 24/7 availability</li>
                <li><i class="fas fa-check"></i> Guaranteed on-time</li>
                <li><i class="fas fa-check"></i> Real-time tracking</li>
            </ul>
        </div>
    </div>

    <h2 class="section-title" style="margin-top: 40px;">Standard Rate Schedule</h2>
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
                    <td><span class="rate-highlight">$50.00</span></td>
                </tr>
                <tr>
                    <td>Mileage beyond 15 miles</td>
                    <td><span class="rate-highlight">$2.00 per mile</span></td>
                </tr>
                <tr>
                    <td>STAT / Urgent Delivery</td>
                    <td><span class="rate-highlight">+$20.00</span></td>
                </tr>
                <tr>
                    <td>Weekends/Holidays</td>
                    <td><span class="rate-highlight">+35% of base rate</span></td>
                </tr>
                <tr>
                    <td>Cold-Chain/Temperature Controlled</td>
                    <td><span class="rate-highlight">+$7.00</span></td>
                </tr>
                <tr>
                    <td>Additional Stop (Same Route)</td>
                    <td><span class="rate-highlight">+$10.00 each</span></td>
                </tr>
                <tr>
                    <td>Wait Time (after 10 minutes)</td>
                    <td><span class="rate-highlight">$1.00 per minute</span></td>
                </tr>
                <tr>
                    <td>Re-Attempt Fee</td>
                    <td><span class="rate-highlight">$15.00</span></td>
                </tr>
                <tr>
                    <td>Secure Signature Collection</td>
                    <td><span class="rate-highlight">$5.00</span></td>
                </tr>
                <tr>
                    <td>After-Hours Delivery (6PM-8AM)</td>
                    <td><span class="rate-highlight">+$25.00</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-box">
            <div class="info-icon">💰</div>
            <h3>Volume Discount</h3>
            <p>Facilities scheduling <span class="highlight">20+ trips monthly</span> receive a <span class="highlight">5% discount</span> on base trip rates. Contact us for bulk pricing and custom quotes.</p>
            <div class="feature-list">
                <li><i class="fas fa-check-circle"></i> 5% off base rates</li>
                <li><i class="fas fa-check-circle"></i> Priority scheduling</li>
                <li><i class="fas fa-check-circle"></i> Dedicated support</li>
            </div>
        </div>

        <div class="info-box">
            <div class="info-icon">📋</div>
            <h3>Billing & Payment</h3>
            <p><span class="highlight">Flexible Billing:</span> Daily • Weekly • Biweekly • Monthly</p>
            <p><span class="highlight">Payment Methods:</span> ACH • Credit/Debit Cards • Business Checks</p>
            <div class="feature-list">
                <li><i class="fas fa-check-circle"></i> Online invoicing</li>
                <li><i class="fas fa-check-circle"></i> Multiple payment options</li>
                <li><i class="fas fa-check-circle"></i> Net terms available</li>
            </div>
        </div>

        <div class="info-box">
            <div class="info-icon">🕒</div>
            <h3>Service Hours</h3>
            <p><span class="highlight">Standard Hours:</span> Monday-Friday 8:00 AM – 6:00 PM</p>
            <p><span class="highlight">After-Hours:</span> Available with surcharge (6PM-8AM)</p>
            <p><span class="highlight">Weekend Service:</span> Additional 35% of base rate</p>
        </div>

        <div class="info-box">
            <div class="info-icon">📊</div>
            <h3>Custom Rate Quotes</h3>
            <p>Pricing varies based on distance, service type, STAT requests, and volume. Contact us for a custom rate quote tailored to your facility's needs.</p>
            <div class="feature-list">
                <li><i class="fas fa-check-circle"></i> Scheduled Routes — custom pricing</li>
                <li><i class="fas fa-check-circle"></i> STAT Delivery — additional surcharge</li>
                <li><i class="fas fa-check-circle"></i> After-hours delivery — premium rate</li>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <a href="{{ route('contact') }}" class="cta-btn">
            <i class="fas fa-calculator mr-2"></i>Get a Custom Quote
        </a>
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
    document.querySelectorAll('.pricing-card, .info-box, .pricing-table, .cta-btn').forEach(el => {
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

    // Add hover animation for table rows
    const tableRows = document.querySelectorAll('tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s';
        });
    });

    // Animate numbers on scroll
    const animateValue = (element, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            if (element.classList.contains('price')) {
                element.innerHTML = '$' + value + ' <span>/ trip</span>';
            }
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    // Trigger number animation when prices come into view
    const priceObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const priceElement = entry.target.querySelector('.price');
                if (priceElement) {
                    const priceText = priceElement.textContent;
                    const numericValue = parseInt(priceText.replace(/[^0-9]/g, ''));
                    if (!isNaN(numericValue)) {
                        animateValue(priceElement, 0, numericValue, 2000);
                    }
                }
                priceObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.pricing-card').forEach(card => {
        priceObserver.observe(card);
    });
});
</script>
@endsection