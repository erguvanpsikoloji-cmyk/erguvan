<footer style="background-color: var(--bg-soft); padding: 80px 0 40px; border-top: 1px solid rgba(0,0,0,0.03);">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 4rem;">
            <div>
                <h3 class="font-heading" style="margin-bottom: 1.5rem;">Erguvan Psikoloji</h3>
                <p style="color: var(--text-muted);">Sizlere daha sağlıklı ve mutlu bir yaşam yolculuğunda eşlik etmek
                    için buradayım. Modern ve bilimsel yöntemlerle yanınızdayız.</p>
            </div>
            <div>
                <h4 class="font-heading" style="margin-bottom: 1.5rem;">Hızlı Bağlantılar</h4>
                <ul style="list-style: none; display: grid; gap: 0.8rem;">
                    <li><a href="#hizmetler" style="text-decoration: none; color: var(--text-muted);">Hizmetlerimiz</a>
                    </li>
                    <li><a href="#hakkimizda" style="text-decoration: none; color: var(--text-muted);">Hakkımızda</a>
                    </li>
                    <li><a href="#ekibimiz" style="text-decoration: none; color: var(--text-muted);">Ekibimiz</a></li>
                    <li><a href="blog.php" style="text-decoration: none; color: var(--text-muted);">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-heading" style="margin-bottom: 1.5rem;">İletişim</h4>
                <ul style="list-style: none; display: grid; gap: 0.8rem; color: var(--text-muted);">
                    <li><i class="fas fa-phone-alt" style="margin-right: 10px; color: var(--primary);"></i> 0551 176 52
                        85</li>
                    <li><i class="fas fa-envelope" style="margin-right: 10px; color: var(--primary);"></i>
                        info@erguvanpsikoloji.com</li>
                    <li><i class="fas fa-map-marker-alt" style="margin-right: 10px; color: var(--primary);"></i> Fatih,
                        İstanbul</li>
                </ul>
            </div>
        </div>
        <div
            style="margin-top: 60px; text-align: center; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 30px; color: var(--text-muted); font-size: 0.9rem;">
            &copy; 2026 Erguvan Psikoloji. Tüm Hakları Saklıdır.
        </div>
    </div>
</footer>

<!-- Floating Actions -->
<div class="floating-actions">
    <a href="tel:+905511765285" class="float-btn phone" title="Hemen Ara">
        <i class="fas fa-phone-alt"></i>
    </a>
    <a href="https://wa.me/905511765285" target="_blank" class="float-btn whatsapp" title="WhatsApp Destek">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

<!-- Reveal Animation Script -->
<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

</body>

</html>