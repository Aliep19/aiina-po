<footer class="footer">
    <div class="container-fluid px-4">

        <div class="row pt-4 pb-3">
            <!-- Left Side: Company Brand -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="brand-section">
                    <h4 class="text-gradient mb-2"
                        style="background: linear-gradient(135deg, #fb6340, #ff8a5c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700;">
                        PT Alpha Innovatech Indonesia
                    </h4>
                    <div class="social-icons d-flex gap-3">
                        <a href="#" class="social-icon" style="background: #fb634010; color: #fb6340;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-icon" style="background: #fb634010; color: #fb6340;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon" style="background: #fb634010; color: #fb6340;">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Middle: Quick Links -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="links-section">
                    <h6 class="text-uppercase small fw-bold mb-3" style="color: #fb6340; letter-spacing: 1.5px;">
                        <i class="fas fa-circle me-2" style="font-size: 0.4rem; vertical-align: middle;"></i> Explore
                    </h6>
                    <div class="d-flex flex-wrap gap-4">
                        <a href="{{ url('https://aiinaindonesia.com/') }}" class="footer-link" target="_blank">
                            <i class="fas fa-external-link-alt me-1" style="font-size: 0.7rem;"></i> Website
                        </a>
                        <a href="javascript:;" class="footer-link">
                            <i class="fas fa-newspaper me-1" style="font-size: 0.7rem;"></i> News
                        </a>
                        <a href="javascript:;" class="footer-link">
                            <i class="fas fa-briefcase me-1" style="font-size: 0.7rem;"></i> Careers
                        </a>
                        <a href="javascript:;" class="footer-link">
                            <i class="fas fa-headset me-1" style="font-size: 0.7rem;"></i> Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Copyright & Legal -->
            <div class="col-lg-3">
                <div class="copyright-section text-lg-end">
                    <div class="copyright-badge">
                        <i class="far fa-copyright me-1"></i>
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                    </div>
                    <div class="mt-2">
                        <span class="d-block text-muted small" style="font-size: 0.75rem;">
                            All rights reserved
                        </span>
                        <a href="javascript:;" class="text-muted small text-decoration-none" style="font-size: 0.7rem;">
                            Terms & Conditions
                        </a>
                        <span class="text-muted small mx-1">•</span>
                        <a href="javascript:;" class="text-muted small text-decoration-none" style="font-size: 0.7rem;">
                            Privacy
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom subtle line with gradient -->
        <div class="row">
            <div class="col-12">
                <div class="gradient-line"
                    style="height: 2px; background: linear-gradient(90deg, transparent, #fb6340, #ffb199, transparent); opacity: 0.3;">
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        position: relative;
        background: linear-gradient(165deg, #ffffff 0%, #fffaf8 100%);
        margin-top: 3rem;
        border-top-left-radius: 32px;
        border-top-right-radius: 32px;
        box-shadow: 0 -10px 30px rgba(251, 99, 64, 0.03);
    }

    /* Wave divider styling */
    .wave-divider {
        position: relative;
        top: -1px;
    }

    .wave-divider svg {
        display: block;
        margin-bottom: -1px;
    }

    /* Social icons style */
    .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        font-size: 0.9rem;
    }

    .social-icon:hover {
        background: #fb6340 !important;
        color: white !important;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(251, 99, 64, 0.25);
    }

    /* Footer links style */
    .footer-link {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.25s ease;
        position: relative;
        padding-bottom: 2px;
    }

    .footer-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 0;
        background: linear-gradient(90deg, #fb6340, #ff8a5c);
        transition: width 0.3s ease;
    }

    .footer-link:hover {
        color: #fb6340;
        transform: translateX(3px);
    }

    .footer-link:hover::after {
        width: 100%;
    }

    /* Gradient text effect */
    .text-gradient {
        background: linear-gradient(135deg, #fb6340, #ff8a5c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Copyright badge */
    .copyright-badge {
        display: inline-block;
        background: linear-gradient(135deg, #fb634010, #ffb19910);
        padding: 4px 12px;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #fb6340;
        backdrop-filter: blur(4px);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .footer {
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
        }

        .copyright-section {
            text-align: center !important;
            margin-top: 1rem;
        }

        .brand-section {
            text-align: center;
        }

        .brand-section p {
            margin-left: auto;
            margin-right: auto;
        }

        .social-icons {
            justify-content: center;
        }

        .links-section {
            text-align: center;
        }

        .footer-link::after {
            left: 50%;
            transform: translateX(-50%);
        }
    }

    @media (max-width: 768px) {
        .footer {
            margin-top: 2rem;
        }

        .wave-divider svg {
            height: 25px;
        }
    }

    /* Subtle animation for gradient line */
    @keyframes shimmer {
        0% {
            opacity: 0.2;
        }

        100% {
            opacity: 0.5;
        }
    }

    .gradient-line {
        animation: shimmer 3s ease-in-out infinite alternate;
    }
</style>

<!-- Make sure Font Awesome is included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
