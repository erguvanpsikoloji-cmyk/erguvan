<?php include 'includes/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section id="hero" class="hero reveal">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>Kendinizi Daha İyi Hissetmek Mümkün</h1>
                <p>Hayatınızdaki zorlukları birlikte aşmak için yanınızdayız.</p>
                <div style="display: flex; gap: 1rem;">
                    <a href="contact.php" class="btn-appointment">Randevu Al</a>
                    <a href="#hakkimizda"
                        style="text-decoration: none; color: var(--text-main); font-weight: 600; padding: 12px 28px;">Detaylı
                        Bilgi</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="../assets/images/team/sena.jpg" alt="Uzman Klinik Psikolog"
                    style="filter: brightness(0.95); transition: var(--transition);">
                <div
                    style="position: absolute; width: 100px; height: 100px; background: var(--secondary); border-radius: 50%; bottom: -20px; left: -20px; z-index: -1;">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="hizmetler" style="background-color: var(--bg-soft);" class="reveal">
        <div class="container">
            <div style="text-align: center; max-width: 700px; margin: 0 auto 80px;">
                <h2 class="font-heading" style="font-size: 2.5rem; margin-bottom: 1.5rem;">Hizmetlerimiz</h2>
                <p style="color: var(--text-muted);">Sizlere en uygun bilimsel yöntemlerle yardımcı oluyoruz.</p>
            </div>
            <div class="card-grid">
                <div class="card">
                    <i class="fas fa-user-friends"></i>
                    <h3>Yetişkin Terapisi</h3>
                    <p>Bireysel gelişim ve psikolojik dayanıklılık süreçleri.</p>
                </div>
                <div class="card">
                    <i class="fas fa-child"></i>
                    <h3>Oyun Terapisi</h3>
                    <p>Çocukların dünyasına iyileştirici bir dokunuş.</p>
                </div>
                <div class="card">
                    <i class="fas fa-brain"></i>
                    <h3>Bilişsel Davranışçı Terapi</h3>
                    <p>Çözüm odaklı ve bilimsel yaklaşımlar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Certificates Section -->
    <section id="sertifikalar" class="reveal">
        <div class="container">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 class="font-heading" style="font-size: 2.5rem;">Sertifikalar ve Eğitimler</h2>
            </div>
            <div class="card-grid">
                <div class="card" style="text-align: center; border: 1px solid var(--secondary); background: #fff;">
                    <i class="fas fa-certificate" style="opacity: 0.5;"></i>
                    <h4 class="font-heading">Klinik Psikoloji Yüksek Lisansı</h4>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Üsküdar Üniversitesi</p>
                </div>
                <div class="card" style="text-align: center; border: 1px solid var(--secondary); background: #fff;">
                    <i class="fas fa-certificate" style="opacity: 0.5;"></i>
                    <h4 class="font-heading">Oyun Terapisi Eğitimi</h4>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Uluslararası Onaylı</p>
                </div>
                <div class="card" style="text-align: center; border: 1px solid var(--secondary); background: #fff;">
                    <i class="fas fa-certificate" style="opacity: 0.5;"></i>
                    <h4 class="font-heading">Bilişsel Davranışçı Terapi</h4>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Akademik Eğitim</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section id="blog" style="background-color: var(--bg-soft);" class="reveal">
        <div class="container">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 class="font-heading" style="font-size: 2.5rem;">Blogdan Yazılar</h2>
                <p style="color: var(--text-muted);">Güncel psikoloji yazıları ve rehberler.</p>
            </div>
            <div class="card-grid">
                <!-- Blog Card 1 -->
                <div class="card" style="padding: 0; overflow: hidden; background: #fff;">
                    <div style="height: 200px; background: var(--secondary); overflow: hidden;">
                        <!-- Görsel yeri -->
                    </div>
                    <div style="padding: 2rem;">
                        <h4 class="font-heading" style="margin-bottom: 1rem;">Kaygı ile Başa Çıkma Yolları</h4>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem;">Günlük hayatta
                            karşılaştığımız stres faktörlerini nasıl yönetebiliriz?</p>
                        <a href="blog-detay.php"
                            style="color: var(--primary); font-weight: 600; text-decoration: none;">Devamını Oku →</a>
                    </div>
                </div>
                <!-- Blog Card 2 -->
                <div class="card" style="padding: 0; overflow: hidden; background: #fff;">
                    <div style="height: 200px; background: var(--accent); opacity: 0.3; overflow: hidden;">
                        <!-- Görsel yeri -->
                    </div>
                    <div style="padding: 2rem;">
                        <h4 class="font-heading" style="margin-bottom: 1rem;">Oyun Terapisi Nedir?</h4>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem;">Çocukların dili
                            olan oyunu anlamak ve iyileştirmek üzerine.</p>
                        <a href="blog-detay.php"
                            style="color: var(--primary); font-weight: 600; text-decoration: none;">Devamını Oku →</a>
                    </div>
                </div>
            </div>
            <div style="text-align: center; margin-top: 50px;">
                <a href="blog.php" class="btn-appointment"
                    style="background: transparent; color: var(--primary); border: 2px solid var(--primary);">Tüm
                    Yazıları Gör</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="yorumlar" class="reveal">
        <div class="container">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 class="font-heading" style="font-size: 2.5rem;">Danışan Yorumları</h2>
            </div>
            <div class="card-grid">
                <div class="card" style="text-align: center; background: var(--bg-soft);">
                    <i class="fas fa-quote-left" style="opacity: 0.2; font-size: 1.5rem; margin-bottom: 1rem;"></i>
                    <p style="font-style: italic; color: var(--text-muted); margin-bottom: 1.5rem;">"Sena hanım ile
                        başladığım terapi süreci hayatımdaki bakış açısını tamamen değiştirdi."</p>
                    <h4 class="font-heading" style="font-size: 1rem;">A. K.</h4>
                </div>
                <div class="card" style="text-align: center; background: var(--bg-soft);">
                    <i class="fas fa-quote-left" style="opacity: 0.2; font-size: 1.5rem; margin-bottom: 1rem;"></i>
                    <p style="font-style: italic; color: var(--text-muted); margin-bottom: 1.5rem;">"Profesyonel ve
                        samimi bir yaklaşım. Her şey için teşekkürler."</p>
                    <h4 class="font-heading" style="font-size: 1rem;">M. Y.</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section id="harita" style="padding-top: 0;" class="reveal">
        <div class="container">
            <div
                style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-soft); height: 400px; border: 1px solid var(--secondary);">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.518663806141!2d28.93240757655022!3d41.01511121901844!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cabbbebb427bb9%3A0x3fec28c0f52032ea!2sFatih%20Psikolog!5e0!3m2!1str!2str!4v1707010000000!5m2!1str!2str"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>