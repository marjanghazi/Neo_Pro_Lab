@extends('layouts.main')

@section('content')
<div class="forms-container">
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Important Forms & Documents</h1>
            <p class="hero-subtitle">Download essential forms, agreements, and documents for seamless medical courier services.</p>
            
            <div class="contact-info">
                <a href="tel:7742970597" class="contact-link">📞 (774) 297-0597</a>
                <a href="mailto:info@neoprolab.com" class="contact-link">📧 info@neoprolab.com</a>
            </div>
        </div>
    </section>

    <!-- FORMS SECTION -->
    <section class="section">
        <h2 class="section-title">Download Our Forms</h2>
        <p class="section-subtitle">All necessary documentation for partnering with NeoProlab Couriers. Keep your operations compliant and efficient.</p>
        
        <div class="forms-grid">
            <!-- BAA Agreement -->
            <div class="form-card">
                <div class="form-icon">📄</div>
                <div class="form-content">
                    <h3 class="form-title">Business Associate Agreement (BAA)</h3>
                    <p class="form-description">HIPAA-compliant agreement outlining our responsibilities in protecting patient health information.</p>
                    <div class="form-details">
                        <span class="form-tag">Legal Document</span>
                        <span class="form-tag">Required for HIPAA</span>
                    </div>
                </div>
                <a href="{{ route('download', ['filename' => 'NeoProLab_Couriers_BAA.pdf']) }}" class="download-btn">
                    <span>Download PDF</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </a>
            </div>

            <!-- Rate Sheet -->
            <div class="form-card">
                <div class="form-icon">💰</div>
                <div class="form-content">
                    <h3 class="form-title">Service Rate Sheet</h3>
                    <p class="form-description">Comprehensive pricing for all our medical courier services and transport options.</p>
                    <div class="form-details">
                        <span class="form-tag">Pricing</span>
                        <span class="form-tag">Updated 2024</span>
                    </div>
                </div>
                <a href="{{ route('download', ['filename' => 'NeoProLab_Couriers_Rate_Sheet.pdf']) }}" class="download-btn">
                    <span>Download PDF</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </a>
            </div>

            <!-- Chain of Custody Form -->
            <div class="form-card">
                <div class="form-icon">🔗</div>
                <div class="form-content">
                    <h3 class="form-title">Chain of Custody Form</h3>
                    <p class="form-description">Document the secure transfer and handling of medical specimens with full traceability.</p>
                    <div class="form-details">
                        <span class="form-tag">Required</span>
                        <span class="form-tag">Compliance</span>
                    </div>
                </div>
                <a href="{{ route('download', ['filename' => 'NeoProLab_Chain_of_Custody_Form.pdf']) }}" class="download-btn">
                    <span>Download PDF</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </a>
            </div>

            <!-- Transport Forms & Proposal -->
            <div class="form-card">
                <div class="form-icon">🚚</div>
                <div class="form-content">
                    <h3 class="form-title">Specimen Transport Forms & Proposal</h3>
                    <p class="form-description">Complete package including transport forms and service proposal details.</p>
                    <div class="form-details">
                        <span class="form-tag">Complete Package</span>
                        <span class="form-tag">All Forms Included</span>
                    </div>
                </div>
                <a href="{{ route('download', ['filename' => 'NeoProLab_Specimen_Transport_Forms_and_Proposal.pdf']) }}" class="download-btn">
                    <span>Download PDF</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- IMPORTANT NOTES SECTION -->
    <section class="section bg-light-gradient">
        <h2 class="section-title">Important Information</h2>
        
        <div class="info-grid">
            <div class="info-card">
                <h3 class="info-title">📋 Before You Begin</h3>
                <ul class="info-list">
                    <li>Review all documents carefully</li>
                    <li>Print and sign where required</li>
                    <li>Keep copies for your records</li>
                    <li>Submit completed forms via email or portal</li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3 class="info-title">⚖️ Legal & Compliance</h3>
                <ul class="info-list">
                    <li>BAA is required for HIPAA compliance</li>
                    <li>Chain of Custody must accompany all specimens</li>
                    <li>Rate sheet is subject to service area</li>
                    <li>Contact us for customized proposals</li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3 class="info-title">❓ Need Help?</h3>
                <ul class="info-list">
                    <li>Questions about forms? Call (774) 297-0597</li>
                    <li>Email completed forms to info@neoprolab.com</li>
                    <li>Schedule a consultation for custom needs</li>
                    <li>Visit our FAQ section for common questions</li>
                </ul>
            </div>
        </div>
        
        <div class="contact-cta">
            <h3>Have Questions About Our Forms?</h3>
            <p>Our team is ready to assist you with any documentation needs.</p>
            <div class="cta-buttons">
                <a href="tel:7742970597" class="btn-primary">
                    📞 Call Us Now
                </a>
                <a href="mailto:info@neoprolab.com" class="btn-secondary">
                    📧 Email Us
                </a>
            </div>
        </div>
    </section>
</div>

<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
        --green: #2E7D32;
        --blue: #1565C0;
    }

    /* HERO SECTION */
    .hero-section {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        color: var(--white);
        padding: 140px 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
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
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: 56px;
        margin-bottom: 25px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -1px;
    }

    .hero-subtitle {
        font-size: 21px;
        margin-bottom: 50px;
        opacity: 0.95;
        max-width: 650px;
        margin-left: auto;
        margin-right: auto;
        font-weight: 500;
        line-height: 1.6;
    }

    /* SECTIONS */
    .section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 100px 30px;
    }

    .bg-light-gradient {
        background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0, 169, 165, 0.05) 100%);
    }

    .section-title {
        font-size: 42px;
        color: var(--navy);
        margin-bottom: 18px;
        font-weight: 800;
        letter-spacing: -1px;
        text-align: center;
    }

    .section-subtitle {
        font-size: 18px;
        color: var(--gray);
        margin-bottom: 50px;
        max-width: 700px;
        line-height: 1.8;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
    }

    /* CONTACT INFO */
    .contact-info {
        margin-top: 50px;
        font-size: 16px;
        display: flex;
        gap: 30px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .contact-link {
        color: var(--teal);
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.1);
        padding: 12px 24px;
        border-radius: 8px;
        border: 1px solid rgba(0, 169, 165, 0.3);
    }

    .contact-link:hover {
        transform: translateX(5px);
        color: var(--white);
        background: rgba(0, 169, 165, 0.3);
    }

    /* FORMS GRID */
    .forms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .form-card {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
        padding: 35px;
        border-radius: 12px;
        border: 2px solid transparent;
        border-left: 5px solid var(--teal);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 25px;
        position: relative;
        overflow: hidden;
    }

    .form-card:hover {
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.2);
        transform: translateY(-8px);
        border-color: var(--teal);
    }

    .form-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 32px;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
    }

    .form-content {
        flex: 1;
    }

    .form-title {
        color: var(--navy);
        margin-bottom: 12px;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.3;
    }

    .form-description {
        color: var(--gray);
        line-height: 1.6;
        margin-bottom: 15px;
        font-weight: 500;
    }

    .form-details {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .form-tag {
        background: rgba(0, 169, 165, 0.1);
        color: var(--teal);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid rgba(0, 169, 165, 0.3);
    }

    /* DOWNLOAD BUTTON */
    .download-btn {
        background: linear-gradient(135deg, var(--green) 0%, #1B5E20 100%);
        color: var(--white);
        padding: 14px 28px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .download-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(46, 125, 50, 0.4);
        background: linear-gradient(135deg, #1B5E20 0%, var(--green) 100%);
    }

    /* INFO GRID */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }

    .info-card {
        background: var(--white);
        border: 2.5px solid var(--light-gray);
        padding: 35px;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .info-card:hover {
        border-color: var(--teal);
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.15);
        transform: translateY(-5px);
    }

    .info-title {
        color: var(--navy);
        margin-bottom: 25px;
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        padding: 12px 0;
        color: var(--gray);
        border-bottom: 1px solid var(--light-gray);
        font-weight: 500;
        transition: all 0.3s;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-list li:hover {
        color: var(--teal);
        padding-left: 5px;
    }

    .info-list li::before {
        content: '✓ ';
        color: var(--teal);
        font-weight: bold;
        margin-right: 10px;
    }

    /* CONTACT CTA */
    .contact-cta {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        border-radius: 12px;
        padding: 60px 40px;
        text-align: center;
        margin-top: 80px;
        color: var(--white);
    }

    .contact-cta h3 {
        font-size: 32px;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .contact-cta p {
        font-size: 18px;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    .cta-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* BUTTONS */
    .btn-primary {
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

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 169, 165, 0.4);
    }

    .btn-secondary {
        background: transparent;
        border: 2.5px solid var(--teal);
        color: var(--teal);
        padding: 14px 32px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 16px;
        display: inline-block;
    }

    .btn-secondary:hover {
        background-color: var(--teal);
        color: var(--white);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .hero-section {
            padding: 80px 20px;
        }
        
        .hero-title {
            font-size: 36px;
        }
        
        .hero-subtitle {
            font-size: 18px;
        }
        
        .section {
            padding: 60px 20px;
        }
        
        .section-title {
            font-size: 32px;
        }
        
        .forms-grid {
            grid-template-columns: 1fr;
        }
        
        .form-card {
            flex-direction: column;
            text-align: center;
            padding: 30px;
        }
        
        .form-icon {
            width: 60px;
            height: 60px;
            font-size: 28px;
        }
        
        .form-details {
            justify-content: center;
        }
        
        .contact-info {
            flex-direction: column;
            gap: 20px;
        }
        
        .contact-link {
            justify-content: center;
        }
        
        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .form-card {
            padding: 25px;
        }
        
        .download-btn {
            width: 100%;
            justify-content: center;
        }
        
        .contact-cta {
            padding: 40px 20px;
        }
        
        .contact-cta h3 {
            font-size: 28px;
        }
    }
</style>
@endsection