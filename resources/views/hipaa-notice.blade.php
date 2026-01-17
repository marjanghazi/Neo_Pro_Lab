@extends('layouts.main')

@section('content')
<!-- HIPAA NOTICE PAGE HTML -->
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
        padding: 100px 30px 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
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

    .hero-content {
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -1px;
    }

    .hero p {
        font-size: 20px;
        margin-bottom: 40px;
        opacity: 0.95;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        font-weight: 500;
    }

    .hipaa-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 80px 30px;
    }

    .content-section {
        background: var(--white);
        border-radius: 12px;
        padding: 50px;
        margin-bottom: 40px;
        border: 2px solid var(--light-gray);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .content-section h2 {
        color: var(--navy);
        font-size: 32px;
        margin-bottom: 25px;
        font-weight: 800;
        padding-bottom: 15px;
        border-bottom: 3px solid var(--teal);
    }

    .content-section h3 {
        color: var(--navy);
        font-size: 24px;
        margin: 30px 0 15px;
        font-weight: 700;
    }

    .content-section p {
        color: var(--gray);
        line-height: 1.8;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .content-section ul, .content-section ol {
        color: var(--gray);
        line-height: 1.8;
        margin: 20px 0;
        padding-left: 20px;
    }

    .content-section li {
        margin-bottom: 12px;
        font-weight: 500;
    }

    .highlight-box {
        background: linear-gradient(135deg, rgba(0, 169, 165, 0.1) 0%, rgba(0, 169, 165, 0.05) 100%);
        border-left: 5px solid var(--teal);
        padding: 25px;
        margin: 30px 0;
        border-radius: 8px;
    }

    .highlight-box p {
        color: var(--navy);
        font-weight: 600;
        margin: 0;
    }

    .contact-info-card {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 40px;
        border-radius: 12px;
        margin-top: 50px;
        text-align: center;
    }

    .contact-info-card h3 {
        color: var(--white);
        margin-bottom: 20px;
        font-size: 28px;
    }

    .contact-info-card p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 15px;
        font-weight: 500;
    }

    .contact-info-card a {
        color: var(--teal);
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s;
    }

    .contact-info-card a:hover {
        color: var(--white);
        text-decoration: underline;
    }

    .icon-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 10px;
        color: var(--white);
        font-size: 24px;
        margin-right: 15px;
        font-weight: bold;
    }

    .policy-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .policy-item {
        background: var(--light-gray);
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid var(--teal);
    }

    .policy-item h4 {
        color: var(--navy);
        margin-bottom: 10px;
        font-size: 18px;
        font-weight: 700;
    }

    .policy-item p {
        color: var(--gray);
        font-size: 14px;
        margin: 0;
    }

    @media (max-width: 768px) {
        .hero {
            padding: 60px 20px 40px;
        }
        .hero h1 {
            font-size: 32px;
        }
        .hero p {
            font-size: 16px;
        }
        .hipaa-content {
            padding: 40px 20px;
        }
        .content-section {
            padding: 30px 20px;
        }
        .content-section h2 {
            font-size: 26px;
        }
        .policy-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-content">
        <h1>HIPAA Privacy Notice</h1>
        <p>Protecting Your Health Information with Security, Compliance, and Trust</p>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 30px;">
            <span style="display: inline-block; background: rgba(255, 255, 255, 0.2); color: var(--white); padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 700;">HIPAA Compliant</span>
            <span style="display: inline-block; background: rgba(255, 255, 255, 0.2); color: var(--white); padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 700;">Patient Privacy</span>
            <span style="display: inline-block; background: rgba(255, 255, 255, 0.2); color: var(--white); padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 700;">Secure Transport</span>
        </div>
    </div>
</section>

<!-- HIPAA CONTENT -->
<div class="hipaa-content">
    <div class="content-section">
        <h2>Notice of Privacy Practices</h2>
        
        <div class="highlight-box">
            <p>This notice describes how medical information about you may be used and disclosed and how you can get access to this information. Please review it carefully.</p>
        </div>

        <h3>Our Commitment to Your Privacy</h3>
        <p>NeoProlab Couriers is dedicated to maintaining the privacy of your protected health information (PHI). As a medical courier service, we transport health information between healthcare providers, laboratories, and facilities, and we are required by law to maintain the privacy of PHI.</p>

        <h3>How We Use and Disclose Health Information</h3>
        <p>We use and disclose health information for treatment, payment, and healthcare operations, including:</p>
        
        <div class="policy-list">
            <div class="policy-item">
                <h4>Treatment Purposes</h4>
                <p>Transporting specimens, lab results, and medical records between healthcare providers for your care.</p>
            </div>
            <div class="policy-item">
                <h4>Payment Activities</h4>
                <p>Billing and collection activities related to our courier services.</p>
            </div>
            <div class="policy-item">
                <h4>Healthcare Operations</h4>
                <p>Quality assessment, training, and improving our courier services.</p>
            </div>
        </div>

        <h3>Your Health Information Rights</h3>
        <p>You have the right to:</p>
        <ol>
            <li><strong>Request Restrictions:</strong> Ask us to limit how we use or disclose your health information.</li>
            <li><strong>Access Your Information:</strong> Request to see or get a copy of your health information.</li>
            <li><strong>Request Amendments:</strong> Ask us to correct health information you believe is incorrect.</li>
            <li><strong>Receive an Accounting:</strong> Request a list of disclosures we have made of your health information.</li>
            <li><strong>Request Confidential Communications:</strong> Ask us to contact you in a specific way or at a specific location.</li>
            <li><strong>Receive a Paper Copy:</strong> Get a paper copy of this notice at any time.</li>
        </ol>

        <h3>Our Responsibilities</h3>
        <p>We are required by law to:</p>
        <ul>
            <li>Maintain the privacy and security of your protected health information</li>
            <li>Provide you with this notice of our legal duties and privacy practices</li>
            <li>Notify you promptly if a breach occurs that may have compromised the privacy or security of your information</li>
            <li>Follow the duties and privacy practices described in this notice</li>
            <li>Train our staff on privacy and security protocols</li>
            <li>Use secure transport methods and technology to protect health information</li>
        </ul>

        <h3>Security Measures</h3>
        <p>To protect your health information during transport, we implement:</p>
        <ul>
            <li>Secure, locked transport containers</li>
            <li>Tamper-evident seals and tracking systems</li>
            <li>Trained, certified couriers with background checks</li>
            <li>GPS tracking and real-time monitoring</li>
            <li>Chain-of-custody documentation</li>
            <li>Secure data transmission and storage</li>
        </ul>

        <h3>Changes to This Notice</h3>
        <p>We reserve the right to change the terms of this notice at any time. The new notice will be effective for all health information we maintain. You can always request the most current notice from our office.</p>

        <div class="contact-info-card">
            <h3>Questions or Concerns?</h3>
            <p>If you have questions about this notice or our privacy practices, please contact:</p>
            <p><strong>NeoProlab Couriers Privacy Officer</strong></p>
            <p><a href="tel:7742970597">📞 (774) 297-0597</a></p>
            <p><a href="mailto:privacy@neoprolab.com">📧 privacy@neoprolab.com</a></p>
            <p style="margin-top: 20px; font-size: 14px; color: rgba(255, 255, 255, 0.7);">
                Effective Date: January 1, 2024<br>
                Last Updated: January 1, 2024
            </p>
        </div>
    </div>
</div>
@endsection