<?php
/**
 * Footer Component
 */
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand Section -->
            <div class="footer-brand">
                <a href="<?php echo url(); ?>" class="footer-logo">
                    <img src="assets/images/logo_icon.png?v=<?php echo VERSION; ?>" alt="Erguvan Psikoloji"
                        class="logo-icon">
                    <div class="logo-text">
                        <span class="logo-title">Erguvan Psikoloji</span>
                        <span class="logo-subtitle">Uzman Klinik Psikolog Desteği</span>
                    </div>
                </a>
                <p class="brand-desc">
                    Akademik temelli, etik ve profesyonel psikolojik danışmanlık hizmetleri ile yanınızdayız. Modern
                    bilimin ışığında, rafine bir yaklaşımla profesyonel destek sunuyoruz.
                </p>
                <div class="footer-social">
                    <a href="https://instagram.com/erguvanpsikoloji" target="_blank" aria-label="Instagram">
                        <svg class="svg-icon" viewBox="0 0 448 512" style="width:20px; height:20px; fill:currentColor;">
                            <path
                                d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
                        </svg>
                    </a>
                    <a href="#" target="_blank" aria-label="LinkedIn">
                        <svg class="svg-icon" viewBox="0 0 448 512" style="width:20px; height:20px; fill:currentColor;">
                            <path
                                d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z" />
                        </svg>
                    </a>
                    <a href="#" target="_blank" aria-label="Facebook">
                        <svg class="svg-icon" viewBox="0 0 320 512" style="width:18px; height:20px; fill:currentColor;">
                            <path
                                d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Menu -->
            <div class="footer-nav">
                <h4>Hızlı Menü</h4>
                <ul>
                    <li><a href="<?php echo url(); ?>">Ana Sayfa</a></li>
                    <li><a href="<?php echo url('#hizmetler'); ?>">Hizmetlerimiz</a></li>
                    <li><a href="<?php echo page_url('blog.php'); ?>">Blog Yazıları</a></li>
                    <li><a href="<?php echo url('#iletisim'); ?>">İletişim Paneli</a></li>
                </ul>
            </div>

            <!-- Specializations -->
            <div class="footer-services">
                <h4>Uzmanlıklar</h4>
                <ul>
                    <li><a href="#">Bireysel Terapi</a></li>
                    <li><a href="#">Çift Terapisi</a></li>
                    <li><a href="#">Çocuk ve Ergen</a></li>
                    <li><a href="#">Online Seanslar</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="footer-contact">
                <h4>İletişim</h4>
                <ul>
                    <li style="display: flex; align-items: center; gap: 10px;">
                        <svg class="svg-icon" viewBox="0 0 512 512" style="width:16px; height:16px; fill:currentColor;">
                            <path
                                d="M497.39 361.8l-112-48a24 24 0 0 0-29.45 6.7L315.6 371.9a340.5 340.5 0 0 1-197.6-197.6L182.2 131.7a24 24 0 0 0 6.7-29.45l-48-112A24 24 0 0 0 113.2 0L64 96a344.2 344.2 0 0 0 484 484l96-49.2a24 24 0 0 0-14.61-42.8z" />
                        </svg>
                        <span>0551 176 52 85</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 10px;">
                        <svg class="svg-icon" viewBox="0 0 512 512" style="width:16px; height:16px; fill:currentColor;">
                            <path
                                d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 55.186s81.206-37.981 103.062-55.186c49.529-38.783 81.024-64.395 104.938-82.646V400H48z" />
                        </svg>
                        <span>info@erguvanpsikoloji.com</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 10px;">
                        <svg class="svg-icon" viewBox="0 0 384 512"
                            style="width:16px; height:16px; margin-top:4px; flex-shrink:0; fill:currentColor;">
                            <path
                                d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.931 13.773-39.466 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z" />
                        </svg>
                        <span>Şehremini, Millet Cd. 34098 Fatih/İstanbul</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Area -->
        <div class="footer-bottom">
            <p>&copy;
                <?php echo date('Y'); ?> Erguvan Psikoloji. Tüm hakları saklıdır.
            </p>
            <div class="footer-bottom-links">
                <a href="#">Kullanım Koşulları</a>
                <a href="#">KVKK Aydınlatma Metni</a>
            </div>
        </div>
    </div>
</footer>



<!-- Assets & Contact Scripts remain same -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>
<script src="<?php echo asset_url('js/script.js'); ?>" defer></script>

<div class="floating-contact">
    <a href="https://wa.me/905511765285" class="fc-classic wa" target="_blank" aria-label="WhatsApp ile Yaz">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="24" height="24" fill="currentColor">
            <path
                d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-4-10.5-6.7z" />
        </svg>
    </a>
    <a href="tel:+905511765285" class="fc-classic ph" aria-label="Bizi Arayın">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20" fill="currentColor">
            <path
                d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.9-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.9 464-464 0-11.2-7.7-20.9-18.6-23.4z" />
        </svg>
    </a>
</div>

<?php
// SEO Schemas
if (!defined('BASE__URL')) {
    require_once __DIR__ . '/../config.php';
}
// Keep original structured data logic if present or add minimal if needed for SEO
?>
</body>

</html>