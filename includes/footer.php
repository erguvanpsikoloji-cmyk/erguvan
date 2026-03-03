<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo">
                    <a href="<?php echo url(); ?>">
                        <img src="<?php echo asset_url('images/logo_icon.png'); ?>" alt="Erguvan Psikoloji">
                        <div class="logo-text">
                            <span class="logo-title">Erguvan Psikoloji</span>
                            <span class="logo-subtitle">Uzman Klinik Psikolog Desteği</span>
                        </div>
                    </a>
                </div>
                <p class="brand-desc">
                    Akademik temelli, etik ve profesyonel psikolojik danışmanlık hizmetleri ile yanınızdayız. Modern
                    bilimin ışığında profesyonel destek sunuyoruz.
                </p>
                <div class="footer-social">
                    <a href="https://instagram.com/erguvanpsikoloji" target="_blank" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <div class="footer-nav">
                <h4>Hızlı Menü</h4>
                <ul>
                    <li><a href="<?php echo url(); ?>">Ana Sayfa</a></li>
                    <li><a href="<?php echo url('#hizmetler'); ?>">Hizmetlerimiz</a></li>
                    <li><a href="<?php echo page_url('blog.php'); ?>">Blog</a></li>
                    <li><a href="<?php echo url('#iletisim'); ?>">İletişim</a></li>
                </ul>
            </div>

            <div class="footer-services">
                <h4>Uzmanlıklar</h4>
                <ul>
                    <li><a href="#">Bireysel Terapi</a></li>
                    <li><a href="#">Çift Terapisi</a></li>
                    <li><a href="#">Çocuk ve Ergen</a></li>
                    <li><a href="#">Online Danışmanlık</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>İletişim</h4>
                <ul>
                    <li><i class="fas fa-phone-alt"></i> <a href="tel:+905511765285">05511765285</a></li>
                    <li><i class="fas fa-envelope"></i> <a
                            href="mailto:info@erguvanpsikoloji.com">info@erguvanpsikoloji.com</a></li>
                    <li><i class="fas fa-map-marker-alt"></i> Şehremini, Millet Cd. 34098 Fatih/İstanbul</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Erguvan Psikoloji. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>

<style>
    .footer {
        background-color: #0F172A;
        color: white;
        padding: 6rem 0 2rem;
        font-family: 'Montserrat', sans-serif;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 4rem;
    }

    .footer-logo a {
        text-decoration: none;
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .footer-logo img {
        height: 50px;
        filter: brightness(0) invert(1);
    }

    .footer-logo .logo-title {
        display: block;
        font-family: 'Prata', serif;
        font-size: 1.8rem;
        color: white;
        margin-left: 1rem;
    }

    .footer-logo .logo-subtitle {
        display: block;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
        margin-left: 1rem;
        letter-spacing: 1px;
    }

    .brand-desc {
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.8;
        margin-bottom: 2.5rem;
        max-width: 420px;
        font-size: 0.95rem;
    }

    .footer-social {
        display: flex;
        gap: 15px;
    }

    .footer-social a {
        color: white;
        background: rgba(255, 255, 255, 0.08);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .footer-social a:hover {
        background: var(--secondary, #915F78);
        transform: translateY(-3px);
    }

    .footer h4 {
        color: white;
        font-family: 'Prata', serif;
        font-size: 1.25rem;
        margin-bottom: 2rem;
        font-weight: 400;
    }

    .footer ul {
        list-style: none;
        padding: 0;
    }

    .footer ul li {
        margin-bottom: 1rem;
    }

    .footer ul li a {
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        font-size: 0.92rem;
        transition: all 0.3s ease;
    }

    .footer ul li a:hover {
        color: white;
        padding-left: 5px;
    }

    .footer-contact li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.92rem;
    }

    .footer-contact i {
        color: var(--secondary, #915F78);
        margin-top: 5px;
    }

    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        margin-top: 5rem;
        padding-top: 2rem;
        text-align: left;
    }

    .footer-bottom p {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.85rem;
    }

    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
    }

    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Assets & Contact Scripts remain same -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="<?php echo asset_url('js/script.js'); ?>" defer></script>

<div class="floating-contact">
    <a href="https://wa.me/905511765285" class="fc-option-btn" target="_blank">
        <span class="fc-option-icon wa">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" fill="currentColor">
                <path
                    d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-4-10.5-6.7z" />
            </svg>
        </span>
        WhatsApp ile Yaz
    </a>
    <a href="tel:+905511765285" class="fc-option-btn">
        <span class="fc-option-icon ph">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="14" height="14" fill="currentColor">
                <path
                    d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.9-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.9 464-464 0-11.2-7.7-20.9-18.6-23.4z" />
            </svg>
        </span>
        Bizi Arayın
    </a>
</div>

<?php
// SEO Schemas remain same
if (!defined('BASE__URL')) {
    require_once __DIR__ . '/../config.php';
}
// ... (rest of the schema logic)
?>