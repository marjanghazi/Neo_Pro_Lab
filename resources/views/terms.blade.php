@extends('layouts.main')

@section('content')
<!-- TERMS AND CONDITIONS PAGE -->
<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
    }

    /* Hero Section */
    .terms-hero {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        color: var(--white);
        padding: 100px 30px 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .terms-hero::before {
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

    .terms-hero::after {
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

    .terms-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .terms-hero h1 {
        font-size: 52px;
        margin-bottom: 25px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -1px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .terms-hero p {
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
    .terms-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 80px 30px;
    }

    .terms-section {
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

    .terms-section h3 {
        color: var(--navy);
        font-size: 24px;
        margin: 40px 0 20px;
        font-weight: 700;
        position: relative;
        padding-left: 20px;
    }

    .terms-section h3::before {
        content: '';
        position: absolute;
        left: 0;
        top: 5px;
        bottom: 5px;
        width: 4px;
        background: var(--teal);
        border-radius: 2px;
    }

    .terms-section h4 {
        color: var(--navy);
        font-size: 20px;
        margin: 30px 0 15px;
        font-weight: 600;
    }

    .terms-section p {
        color: var(--gray);
        line-height: 1.8;
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: 500;
    }

    .terms-section ul, .terms-section ol {
        color: var(--gray);
        line-height: 1.8;
        margin: 20px 0 30px;
        padding-left: 30px;
    }

    .terms-section li {
        margin-bottom: 12px;
        font-weight: 500;
        position: relative;
    }

    .terms-section ul li::marker {
        color: var(--teal);
    }

    .highlight-box {
        background: linear-gradient(135deg, rgba(0, 169, 165, 0.05) 0%, rgba(0, 169, 165, 0.02) 100%);
        border: 1px solid rgba(0, 169, 165, 0.2);
        border-radius: 16px;
        padding: 30px;
        margin: 30px 0;
    }

    .highlight-box p {
        color: var(--navy);
        font-weight: 600;
        margin: 0;
        font-size: 18px;
    }

    .highlight-box.warning {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 193, 7, 0.02) 100%);
        border-color: rgba(255, 193, 7, 0.3);
    }

    .highlight-box.warning p {
        color: #856404;
    }

    .highlight-box.danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(220, 53, 69, 0.02) 100%);
        border-color: rgba(220, 53, 69, 0.2);
    }

    .highlight-box.danger p {
        color: #721c24;
    }

    /* Terms Grid */
    .terms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }

    .term-card {
        background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
        padding: 30px;
        border-radius: 16px;
        border: 1px solid #e0e7f0;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .term-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--teal), #008B85);
        border-radius: 4px 4px 0 0;
    }

    .term-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 169, 165, 0.1);
        border-color: var(--teal);
    }

    .term-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 22px;
        margin-bottom: 20px;
    }

    .term-card h4 {
        color: var(--navy);
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .term-card p {
        color: var(--gray);
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }

    /* Service Levels */
    .service-levels {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .service-level {
        background: var(--white);
        padding: 25px;
        border-radius: 16px;
        border: 2px solid var(--light-gray);
        transition: all 0.3s;
    }

    .service-level:hover {
        border-color: var(--teal);
    }

    .service-level h4 {
        color: var(--navy);
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .service-level h4 i {
        color: var(--teal);
    }

    .service-level ul {
        margin: 15px 0;
        padding-left: 20px;
    }

    .service-level li {
        font-size: 14px;
        margin-bottom: 8px;
    }

    .service-level .price {
        font-size: 24px;
        font-weight: 800;
        color: var(--teal);
        margin-top: 15px;
    }

    /* Liability Table */
    .liability-table {
        width: 100%;
        border-collapse: collapse;
        margin: 30px 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .liability-table th {
        background: var(--navy);
        color: var(--white);
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    .liability-table td {
        padding: 15px;
        border-bottom: 1px solid var(--light-gray);
    }

    .liability-table tr:last-child td {
        border-bottom: none;
    }

    .liability-table tr:hover td {
        background: var(--light-gray);
    }

    /* Contact Section */
    .terms-contact {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 60px;
        border-radius: 20px;
        margin-top: 50px;
        position: relative;
        overflow: hidden;
    }

    .terms-contact::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(0, 169, 165, 0.2) 0%, transparent 70%);
        border-radius: 50%;
    }

    .terms-contact::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(0, 169, 165, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .terms-contact-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    .terms-contact h3 {
        color: var(--white);
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .terms-contact p {
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

    .terms-footer {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
    }

    .terms-footer strong {
        color: var(--teal);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .terms-hero {
            padding: 60px 20px 40px;
        }
        .terms-hero h1 {
            font-size: 36px;
        }
        .terms-hero p {
            font-size: 18px;
        }
        .terms-content {
            padding: 40px 20px;
        }
        .terms-section {
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
        .terms-section h3 {
            font-size: 20px;
        }
        .terms-grid, .service-levels {
            grid-template-columns: 1fr;
        }
        .liability-table {
            display: block;
            overflow-x: auto;
        }
        .terms-contact {
            padding: 40px 20px;
        }
        .terms-contact h3 {
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
    }
</style>

<!-- HERO SECTION -->
<section class="terms-hero">
    <div class="terms-hero-content">
        <h1>Terms and Conditions</h1>
        <p>Understanding Our Service Agreement, Policies, and Your Rights</p>
        <div class="hero-badges">
            <span class="hero-badge">
                <i class="fas fa-file-contract"></i> Legal Agreement
            </span>
            <span class="hero-badge">
                <i class="fas fa-gavel"></i> Binding Terms
            </span>
            <span class="hero-badge">
                <i class="fas fa-shield-alt"></i> Your Protection
            </span>
            <span class="hero-badge">
                <i class="fas fa-handshake"></i> Mutual Agreement
            </span>
        </div>
    </div>
</section>

<!-- TERMS CONTENT -->
<div class="terms-content">
    <!-- Introduction -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-scroll"></i>
            </div>
            <h2>
                Agreement Overview
                <span>Last Updated: January 1, 2024</span>
            </h2>
        </div>

        <div class="highlight-box">
            <p>📋 PLEASE READ THESE TERMS AND CONDITIONS CAREFULLY BEFORE USING OUR SERVICES. BY USING NEOPROLAB COURIERS SERVICES, YOU AGREE TO BE BOUND BY THESE TERMS.</p>
        </div>

        <p>NeoProLab Couriers LLC ("Company," "we," "us," or "our") provides medical courier services subject to the following Terms and Conditions ("Terms"). These Terms constitute a legally binding agreement between you ("Client," "you," or "your") and NeoProLab Couriers LLC governing your use of our services and website.</p>
    </div>

    <!-- Definitions -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h2>Definitions</h2>
        </div>

        <div class="terms-grid">
            <div class="term-card">
                <div class="term-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h4>Services</h4>
                <p>Medical courier services including specimen transport, document delivery, and related logistics provided by NeoProLab Couriers LLC.</p>
            </div>
            <div class="term-card">
                <div class="term-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h4>Shipment</h4>
                <p>Any item or package accepted for transport by the Company, including specimens, medical records, lab results, and other healthcare materials.</p>
            </div>
            <div class="term-card">
                <div class="term-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <h4>Client</h4>
                <p>Individual, healthcare provider, facility, or organization requesting and utilizing our courier services.</p>
            </div>
            <div class="term-card">
                <div class="term-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4>Delivery Order</h4>
                <p>Specific instructions for pickup and delivery, including addresses, timing, and special handling requirements.</p>
            </div>
        </div>
    </div>

    <!-- Service Terms -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-concierge-bell"></i>
            </div>
            <h2>Service Terms</h2>
        </div>

        <h3>Service Availability</h3>
        <p>We provide medical courier services throughout Massachusetts and Rhode Island. Service availability may vary by location and time. We reserve the right to modify or discontinue services at any time without notice.</p>

        <h3>Service Levels</h3>
        <div class="service-levels">
            <div class="service-level">
                <h4><i class="fas fa-clock"></i> Standard Service</h4>
                <ul>
                    <li>Regular business hours (Mon-Fri, 8AM-6PM)</li>
                    <li>Standard transit times</li>
                    <li>Regular handling procedures</li>
                </ul>
                <div class="price">As quoted</div>
            </div>
            <div class="service-level">
                <h4><i class="fas fa-rocket"></i> Rush Service</h4>
                <ul>
                    <li>Priority handling</li>
                    <li>Expedited transit</li>
                    <li>Real-time tracking</li>
                </ul>
                <div class="price">+50% surcharge</div>
            </div>
            <div class="service-level">
                <h4><i class="fas fa-clock"></i> After Hours</h4>
                <ul>
                    <li>Evening and weekend service</li>
                    <li>On-call availability</li>
                    <li>Premium rates apply</li>
                </ul>
                <div class="price">+100% surcharge</div>
            </div>
        </div>

        <h3>Service Limitations</h3>
        <ul>
            <li>We do not transport hazardous materials without prior written approval.</li>
            <li>Temperature-sensitive items require proper packaging by the client.</li>
            <li>We reserve the right to refuse service for improperly packaged items.</li>
            <li>International shipments are not currently available.</li>
        </ul>
    </div>

    <!-- Client Responsibilities -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <h2>Client Responsibilities</h2>
        </div>

        <div class="terms-grid">
            <div class="term-card">
                <h4>Accurate Information</h4>
                <p>Provide complete and accurate pickup and delivery addresses, contact information, and special instructions.</p>
            </div>
            <div class="term-card">
                <h4>Proper Packaging</h4>
                <p>Ensure all items are properly packaged according to applicable regulations and industry standards.</p>
            </div>
            <div class="term-card">
                <h4>Accessibility</h4>
                <p>Ensure pickup and delivery locations are accessible to our couriers during scheduled times.</p>
            </div>
            <div class="term-card">
                <h4>Documentation</h4>
                <p>Provide all necessary documentation, including chain-of-custody forms when required.</p>
            </div>
            <div class="term-card">
                <h4>Payment</h4>
                <p>Pay all applicable fees and charges in accordance with our billing terms.</p>
            </div>
            <div class="term-card">
                <h4>Compliance</h4>
                <p>Comply with all applicable laws, regulations, and industry standards.</p>
            </div>
        </div>

        <div class="highlight-box warning">
            <p>⚠️ Failure to meet these responsibilities may result in service delays, additional charges, or cancellation of the delivery order.</p>
        </div>
    </div>

    <!-- Pricing and Payment -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h2>Pricing and Payment</h2>
        </div>

        <h3>Fees and Charges</h3>
        <ul>
            <li><strong>Base Rate:</strong> Determined by distance, service level, and item type.</li>
            <li><strong>Fuel Surcharge:</strong> Variable surcharge based on current fuel prices.</li>
            <li><strong>After Hours Fee:</strong> Additional charges for service outside regular business hours.</li>
            <li><strong>Waiting Time:</strong> Charges may apply for waiting time exceeding 15 minutes at pickup or delivery.</li>
            <li><strong>Rescheduling Fee:</strong> $25 fee for same-day cancellations or rescheduling.</li>
        </ul>

        <h3>Payment Terms</h3>
        <ul>
            <li>Invoices are due within 30 days of receipt.</li>
            <li>Late payments may incur a 1.5% monthly finance charge.</li>
            <li>We accept major credit cards, checks, and electronic payments.</li>
            <li>Accounts 60+ days overdue may be suspended until payment is received.</li>
        </ul>

        <div class="highlight-box">
            <p>💳 Volume discounts are available for regular clients. Contact our billing department for more information.</p>
        </div>
    </div>

    <!-- Liability and Insurance -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>Liability and Insurance</h2>
        </div>

        <h3>Liability Limitations</h3>
        <p>Our liability for loss, damage, or delay is limited as follows:</p>

        <table class="liability-table">
            <thead>
                <tr>
                    <th>Item Type</th>
                    <th>Maximum Liability</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Documents</strong></td>
                    <td>$100 per shipment</td>
                    <td>Unless higher value declared</td>
                </tr>
                <tr>
                    <td><strong>Medical Specimens</strong></td>
                    <td>$500 per shipment</td>
                    <td>Diagnostic value only</td>
                </tr>
                <tr>
                    <td><strong>Medical Equipment</strong></td>
                    <td>$1,000 per item</td>
                    <td>With proof of value</td>
                </tr>
                <tr>
                    <td><strong>Declared Value</strong></td>
                    <td>Up to $5,000</td>
                    <td>Additional insurance available</td>
                </tr>
            </tbody>
        </table>

        <h3>Insurance Options</h3>
        <p>Additional insurance coverage may be purchased for high-value shipments. Contact us for rates and terms.</p>

        <div class="highlight-box danger">
            <p>⚠️ We are NOT liable for consequential damages, including but not limited to lost profits, business interruption, or replacement costs.</p>
        </div>
    </div>

    <!-- Prohibited Items -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-ban"></i>
            </div>
            <h2>Prohibited Items</h2>
        </div>

        <p>The following items are strictly prohibited from transport:</p>

        <ul>
            <li><strong>Hazardous Materials:</strong> Explosives, flammable liquids, radioactive materials</li>
            <li><strong>Illegal Substances:</strong> Controlled substances without proper documentation</li>
            <li><strong>Perishables:</strong> Items requiring temperature control without proper packaging</li>
            <li><strong>Valuables:</strong> Cash, jewelry, precious metals, negotiable instruments</li>
            <li><strong>Weapons:</strong> Firearms, ammunition, explosive devices</li>
            <li><strong>Biological Hazards:</strong> Untreated infectious substances</li>
        </ul>

        <div class="highlight-box warning">
            <p>⚠️ Violation of prohibited items policy may result in immediate termination of service and legal action.</p>
        </div>
    </div>

    <!-- Cancellation and Refunds -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h2>Cancellation and Refunds</h2>
        </div>

        <div class="terms-grid">
            <div class="term-card">
                <h4>Before Pickup</h4>
                <p>Full refund if cancelled at least 2 hours before scheduled pickup.</p>
            </div>
            <div class="term-card">
                <h4>Within 2 Hours</h4>
                <p>50% refund for cancellations within 2 hours of pickup.</p>
            </div>
            <div class="term-card">
                <h4>After Pickup</h4>
                <p>No refunds once item has been picked up by courier.</p>
            </div>
            <div class="term-card">
                <h4>Service Issues</h4>
                <p>Partial refunds may be issued for service delays or issues at our discretion.</p>
            </div>
        </div>
    </div>

    <!-- Intellectual Property -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-copyright"></i>
            </div>
            <h2>Intellectual Property</h2>
        </div>

        <p>All content on our website and marketing materials, including logos, trademarks, and service marks, are the property of NeoProLab Couriers LLC. You may not use our intellectual property without prior written consent.</p>
    </div>

    <!-- Termination -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-stop-circle"></i>
            </div>
            <h2>Termination</h2>
        </div>

        <p>We reserve the right to terminate or suspend service immediately, without prior notice, for:</p>
        <ul>
            <li>Violation of these Terms</li>
            <li>Non-payment of fees</li>
            <li>Fraudulent or illegal activity</li>
            <li>Harassment of our staff</li>
            <li>Any conduct we deem harmful to our business</li>
        </ul>
    </div>

    <!-- Governing Law -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-gavel"></i>
            </div>
            <h2>Governing Law</h2>
        </div>

        <p>These Terms shall be governed by the laws of the Commonwealth of Massachusetts without regard to its conflict of law provisions. Any disputes arising under these Terms shall be resolved in the state or federal courts located in Massachusetts.</p>
    </div>

    <!-- Changes to Terms -->
    <div class="terms-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-history"></i>
            </div>
            <h2>Changes to Terms</h2>
        </div>

        <p>We reserve the right to modify these Terms at any time. Changes will be effective immediately upon posting to our website. Your continued use of our services constitutes acceptance of the modified Terms.</p>
    </div>

    <!-- Contact Section -->
    <div class="terms-contact">
        <div class="terms-contact-content">
            <h3>Questions About Our Terms?</h3>
            <p>Our team is available to answer any questions about our terms and conditions.</p>

            <div class="contact-methods">
                <div class="contact-method">
                    <i class="fas fa-phone-alt"></i>
                    <a href="tel:7742970597">(508) 933-6750</a>
                </div>
                <div class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:legal@neoprolab.com">legal@neoprolab.com</a>
                </div>
            </div>

            <div class="contact-method" style="justify-content: center; background: rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 15px; max-width: 300px; margin: 0 auto;">
                <i class="fas fa-building"></i>
                <span>NeoProLab Couriers LLC<br>Legal Department</span>
            </div>

            <div class="terms-footer">
                <p><strong>Effective Date:</strong> January 1, 2024</p>
                <p><strong>Version:</strong> 2.1.0</p>
                <p>These terms were last updated to ensure compliance with current regulations and industry standards.</p>
            </div>
        </div>
    </div>
</div>

<!-- Acceptance Simulation (Non-functional, just for design) -->
<script>
    // Any interactive elements if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript functionality here
    });
</script>
@endsection