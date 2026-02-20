@extends('layouts.main')

@section('content')
<!-- PRIVACY POLICY PAGE -->
<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
    }

    /* Hero Section */
    .privacy-hero {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        color: var(--white);
        padding: 100px 30px 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .privacy-hero::before {
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

    .privacy-hero::after {
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

    .privacy-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .privacy-hero h1 {
        font-size: 52px;
        margin-bottom: 25px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -1px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .privacy-hero p {
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
    .privacy-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 80px 30px;
    }

    .privacy-section {
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

    .privacy-section h3 {
        color: var(--navy);
        font-size: 24px;
        margin: 40px 0 20px;
        font-weight: 700;
        position: relative;
        padding-left: 20px;
    }

    .privacy-section h3::before {
        content: '';
        position: absolute;
        left: 0;
        top: 5px;
        bottom: 5px;
        width: 4px;
        background: var(--teal);
        border-radius: 2px;
    }

    .privacy-section p {
        color: var(--gray);
        line-height: 1.8;
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: 500;
    }

    .privacy-section ul, .privacy-section ol {
        color: var(--gray);
        line-height: 1.8;
        margin: 20px 0 30px;
        padding-left: 30px;
    }

    .privacy-section li {
        margin-bottom: 12px;
        font-weight: 500;
        position: relative;
    }

    .privacy-section ul li::marker {
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

    /* Data Collection Cards */
    .data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }

    .data-card {
        background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
        padding: 25px;
        border-radius: 16px;
        border: 1px solid #e0e7f0;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .data-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--teal), #008B85);
        border-radius: 4px 4px 0 0;
    }

    .data-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 169, 165, 0.1);
        border-color: var(--teal);
    }

    .data-icon {
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

    .data-card h4 {
        color: var(--navy);
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .data-card p {
        color: var(--gray);
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }

    /* Rights Grid */
    .rights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .right-card {
        background: var(--white);
        padding: 25px;
        border-radius: 16px;
        border: 1px solid var(--light-gray);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        transition: all 0.3s;
        display: flex;
        gap: 15px;
    }

    .right-card:hover {
        border-color: var(--teal);
        box-shadow: 0 10px 25px rgba(0, 169, 165, 0.1);
    }

    .right-number {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }

    .right-content {
        flex: 1;
    }

    .right-content h4 {
        color: var(--navy);
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .right-content p {
        color: var(--gray);
        font-size: 14px;
        margin: 0;
        line-height: 1.5;
    }

    /* Cookie Preferences */
    .cookie-prefs {
        background: var(--light-gray);
        border-radius: 16px;
        padding: 30px;
        margin: 30px 0;
    }

    .cookie-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #e0e7f0;
    }

    .cookie-toggle:last-child {
        border-bottom: none;
    }

    .cookie-toggle label {
        color: var(--navy);
        font-weight: 600;
        font-size: 16px;
    }

    .toggle-switch {
        position: relative;
        width: 50px;
        height: 26px;
        background: #ddd;
        border-radius: 13px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .toggle-switch.active {
        background: var(--teal);
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        background: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: all 0.3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch.active::after {
        left: 26px;
    }

    /* Contact Section */
    .privacy-contact {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 60px;
        border-radius: 20px;
        margin-top: 50px;
        position: relative;
        overflow: hidden;
    }

    .privacy-contact::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(0, 169, 165, 0.2) 0%, transparent 70%);
        border-radius: 50%;
    }

    .privacy-contact::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(0, 169, 165, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .privacy-contact-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    .privacy-contact h3 {
        color: var(--white);
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .privacy-contact p {
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

    .privacy-footer {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
    }

    .privacy-footer strong {
        color: var(--teal);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .privacy-hero {
            padding: 60px 20px 40px;
        }
        .privacy-hero h1 {
            font-size: 36px;
        }
        .privacy-hero p {
            font-size: 18px;
        }
        .privacy-content {
            padding: 40px 20px;
        }
        .privacy-section {
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
        .privacy-section h3 {
            font-size: 20px;
        }
        .data-grid, .rights-grid {
            grid-template-columns: 1fr;
        }
        .right-card {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .privacy-contact {
            padding: 40px 20px;
        }
        .privacy-contact h3 {
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
        .contact-methods {
            gap: 15px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="privacy-hero">
    <div class="privacy-hero-content">
        <h1>Privacy Policy</h1>
        <p>Your Privacy Matters: How We Protect Your Personal and Health Information</p>
        <div class="hero-badges">
            <span class="hero-badge">
                <i class="fas fa-shield-alt"></i> GDPR Compliant
            </span>
            <span class="hero-badge">
                <i class="fas fa-lock"></i> 256-bit Encryption
            </span>
            <span class="hero-badge">
                <i class="fas fa-user-secret"></i> Privacy First
            </span>
            <span class="hero-badge">
                <i class="fas fa-check-circle"></i> Verified Secure
            </span>
        </div>
    </div>
</section>

<!-- PRIVACY CONTENT -->
<div class="privacy-content">
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>
                Our Commitment to Privacy
                <span>Last Updated: January 1, 2024</span>
            </h2>
        </div>

        <div class="highlight-box">
            <p>🔒 NeoProLab Couriers LLC is committed to protecting your privacy and ensuring the security of your personal and health information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our services.</p>
        </div>

        <p>We value the trust you place in us when entrusting us with your sensitive information. This policy demonstrates our commitment to transparency and data protection, in compliance with applicable privacy laws including HIPAA, GDPR, and state regulations.</p>
    </div>

    <!-- Information We Collect -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-database"></i>
            </div>
            <h2>Information We Collect</h2>
        </div>

        <p>We collect various types of information to provide and improve our courier services:</p>

        <div class="data-grid">
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h4>Personal Information</h4>
                <p>Name, email address, phone number, and physical address for service delivery and communication.</p>
            </div>
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <h4>Health Information</h4>
                <p>Protected health information (PHI) transported between healthcare providers, as required for medical courier services.</p>
            </div>
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h4>Payment Information</h4>
                <p>Billing details, payment method information, and transaction history for service payments.</p>
            </div>
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h4>Location Data</h4>
                <p>Pickup and delivery addresses, GPS tracking during active deliveries for service optimization.</p>
            </div>
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h4>Technical Data</h4>
                <p>IP address, browser type, device information, and usage data when you visit our website.</p>
            </div>
            <div class="data-card">
                <div class="data-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h4>Communication Data</h4>
                <p>Records of your communications with us, including emails, chat messages, and phone calls.</p>
            </div>
        </div>
    </div>

    <!-- How We Use Information -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <h2>How We Use Your Information</h2>
        </div>

        <p>We use the collected information for the following purposes:</p>

        <ul>
            <li><strong>Service Delivery:</strong> To provide, maintain, and improve our courier services, including tracking and delivery updates.</li>
            <li><strong>Communication:</strong> To contact you about your deliveries, respond to inquiries, and send service notifications.</li>
            <li><strong>Billing and Payments:</strong> To process payments, send invoices, and manage accounts.</li>
            <li><strong>Legal Compliance:</strong> To comply with HIPAA regulations and other legal obligations.</li>
            <li><strong>Security:</strong> To protect against fraud, unauthorized transactions, and other liabilities.</li>
            <li><strong>Analytics:</strong> To analyze usage patterns and improve our website and services.</li>
        </ul>
    </div>

    <!-- Information Sharing -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <h2>When We Share Your Information</h2>
        </div>

        <p>We may share your information in the following circumstances:</p>

        <div class="data-grid">
            <div class="data-card">
                <h4>Healthcare Providers</h4>
                <p>With healthcare providers involved in your care as part of our courier services.</p>
            </div>
            <div class="data-card">
                <h4>Service Partners</h4>
                <p>With trusted partners who assist in operating our business (under confidentiality agreements).</p>
            </div>
            <div class="data-card">
                <h4>Legal Requirements</h4>
                <p>When required by law, regulation, or legal process.</p>
            </div>
            <div class="data-card">
                <h4>With Your Consent</h4>
                <p>In other circumstances, we will ask for your consent before sharing.</p>
            </div>
        </div>

        <div class="highlight-box">
            <p>⚠️ We NEVER sell your personal information to third parties for marketing purposes.</p>
        </div>
    </div>

    <!-- Your Privacy Rights -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <h2>Your Privacy Rights</h2>
        </div>

        <p>Depending on your location, you may have the following rights regarding your information:</p>

        <div class="rights-grid">
            <div class="right-card">
                <div class="right-number">1</div>
                <div class="right-content">
                    <h4>Right to Access</h4>
                    <p>Request a copy of your personal information we hold.</p>
                </div>
            </div>
            <div class="right-card">
                <div class="right-number">2</div>
                <div class="right-content">
                    <h4>Right to Rectification</h4>
                    <p>Correct inaccurate or incomplete information.</p>
                </div>
            </div>
            <div class="right-card">
                <div class="right-number">3</div>
                <div class="right-content">
                    <h4>Right to Erasure</h4>
                    <p>Request deletion of your information (subject to legal limits).</p>
                </div>
            </div>
            <div class="right-card">
                <div class="right-number">4</div>
                <div class="right-content">
                    <h4>Right to Restrict</h4>
                    <p>Limit how we use your information.</p>
                </div>
            </div>
            <div class="right-card">
                <div class="right-number">5</div>
                <div class="right-content">
                    <h4>Right to Data Portability</h4>
                    <p>Receive your information in a portable format.</p>
                </div>
            </div>
            <div class="right-card">
                <div class="right-number">6</div>
                <div class="right-content">
                    <h4>Right to Object</h4>
                    <p>Object to certain processing of your information.</p>
                </div>
            </div>
        </div>

        <p>To exercise any of these rights, please contact our Privacy Officer using the information below.</p>
    </div>

    <!-- Cookie Policy -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-cookie-bite"></i>
            </div>
            <h2>Cookie Policy</h2>
        </div>

        <p>Our website uses cookies to enhance your browsing experience:</p>

        <div class="cookie-prefs">
            <div class="cookie-toggle">
                <label>Essential Cookies (Required)</label>
                <div class="toggle-switch active"></div>
            </div>
            <div class="cookie-toggle">
                <label>Analytics Cookies</label>
                <div class="toggle-switch"></div>
            </div>
            <div class="cookie-toggle">
                <label>Marketing Cookies</label>
                <div class="toggle-switch"></div>
            </div>
            <div class="cookie-toggle">
                <label>Functional Cookies</label>
                <div class="toggle-switch"></div>
            </div>
        </div>

        <p class="text-sm text-gray-500 mt-4">You can manage your cookie preferences through your browser settings at any time.</p>
    </div>

    <!-- Data Security -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2>Data Security Measures</h2>
        </div>

        <p>We implement industry-standard security measures to protect your information:</p>

        <ul>
            <li><strong>Encryption:</strong> 256-bit SSL/TLS encryption for all data transmissions</li>
            <li><strong>Access Controls:</strong> Strict authentication and authorization protocols</li>
            <li><strong>Audit Logs:</strong> Comprehensive logging of all data access and modifications</li>
            <li><strong>Employee Training:</strong> Regular privacy and security training for all staff</li>
            <li><strong>Physical Security:</strong> Secure facilities and transport containers</li>
            <li><strong>Breach Notification:</strong> Immediate notification procedures in case of any breach</li>
        </ul>
    </div>

    <!-- Children's Privacy -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-child"></i>
            </div>
            <h2>Children's Privacy</h2>
        </div>

        <p>Our services are not directed to children under 13. We do not knowingly collect information from children under 13. If you become aware that a child has provided us with personal information, please contact us immediately.</p>
    </div>

    <!-- Changes to Policy -->
    <div class="privacy-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-history"></i>
            </div>
            <h2>Changes to This Policy</h2>
        </div>

        <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last Updated" date. In case of significant changes, we will provide additional notice (such as email notification).</p>
    </div>

    <!-- Contact Section -->
    <div class="privacy-contact">
        <div class="privacy-contact-content">
            <h3>Questions About Your Privacy?</h3>
            <p>Our Privacy Officer is here to help with any questions or concerns about how we handle your information.</p>

            <div class="contact-methods">
                <div class="contact-method">
                    <i class="fas fa-phone-alt"></i>
                    <a href="tel:7742970597">(774) 297-0597</a>
                </div>
                <div class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:privacy@neoprolab.com">privacy@neoprolab.com</a>
                </div>
            </div>

            <div class="contact-method" style="justify-content: center; background: rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 15px; max-width: 300px; margin: 0 auto;">
                <i class="fas fa-building"></i>
                <span>NeoProLab Couriers LLC<br>Privacy Officer</span>
            </div>

            <div class="privacy-footer">
                <p><strong>Effective Date:</strong> January 1, 2024</p>
                <p><strong>Version:</strong> 2.1.0</p>
                <p>This policy was last reviewed and updated to ensure compliance with current privacy regulations.</p>
            </div>
        </div>
    </div>
</div>

<!-- Cookie Consent Simulation (Non-functional, just for design) -->
<script>
    // Toggle switches for cookie preferences (UI only)
    document.querySelectorAll('.toggle-switch').forEach(toggle => {
        toggle.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
</script>
@endsection