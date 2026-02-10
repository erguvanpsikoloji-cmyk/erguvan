<?php include 'includes/header.php'; ?>

<main>
    <section class="reveal">
        <div class="container">
            <!-- Title & Intro -->
            <div style="text-align: center; max-width: 700px; margin: 0 auto 80px;">
                <h1 class="font-heading" style="font-size: 3rem; margin-bottom: 1.5rem;">İletişime Geçin</h1>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Sizlere nasıl yardımcı olabileceğimizi konuşmak
                    için bizimle iletişime geçin. Her sorunuz için buradayız.</p>
            </div>

            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 5rem; align-items: start;">

                <!-- Left: Contact Details -->
                <div class="reveal" style="display: grid; gap: 3rem;">

                    <div style="display: flex; gap: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <h4 class="font-heading" style="margin-bottom: 0.5rem;">Adres</h4>
                            <p style="color: var(--text-muted);">Şehremini, Millet Cd. Aydın apt No:131 Daire 4 34098
                                Fatih/İstanbul</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-phone-alt" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <h4 class="font-heading" style="margin-bottom: 0.5rem;">Telefon</h4>
                            <p style="color: var(--text-muted);">0551 176 52 85</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-envelope" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <h4 class="font-heading" style="margin-bottom: 0.5rem;">E-posta</h4>
                            <p style="color: var(--text-muted);">info@erguvanpsikoloji.com</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-clock" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <h4 class="font-heading" style="margin-bottom: 0.5rem;">Çalışma Saatleri</h4>
                            <p style="color: var(--text-muted);">Pazartesi - Cumartesi: 09:00 - 20:00</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fab fa-instagram" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <h4 class="font-heading" style="margin-bottom: 0.5rem;">Instagram</h4>
                            <p style="color: var(--text-muted);">@uzm.psk.senaceren</p>
                        </div>
                    </div>

                </div>

                <!-- Right: Appointment Form -->
                <div class="card reveal" style="background: var(--bg-soft); padding: 3.5rem;">
                    <form action="#" method="POST" style="display: grid; gap: 1.5rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Ad Soyad</label>
                            <input type="text" placeholder="Adınız ve Soyadınız"
                                style="width: 100%; padding: 12px; border-radius: var(--radius-md); border: 1px solid rgba(0,0,0,0.1); outline: none;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">E-posta</label>
                            <input type="email" placeholder="E-posta adresiniz"
                                style="width: 100%; padding: 12px; border-radius: var(--radius-md); border: 1px solid rgba(0,0,0,0.1); outline: none;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Telefon</label>
                            <input type="tel" placeholder="05xx xxx xx xx"
                                style="width: 100%; padding: 12px; border-radius: var(--radius-md); border: 1px solid rgba(0,0,0,0.1); outline: none;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Hizmet
                                Seçimi</label>
                            <select
                                style="width: 100%; padding: 12px; border-radius: var(--radius-md); border: 1px solid rgba(0,0,0,0.1); outline: none; background: #fff;">
                                <option>Yetişkin Terapisi</option>
                                <option>Çocuk & Ergen Terapisi</option>
                                <option>Oyun Terapisi</option>
                                <option>Çift Danışmanlığı</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Mesajınız</label>
                            <textarea rows="4" placeholder="Size nasıl yardımcı olabiliriz?"
                                style="width: 100%; padding: 12px; border-radius: var(--radius-md); border: 1px solid rgba(0,0,0,0.1); outline: none;"></textarea>
                        </div>
                        <button type="submit" class="btn-appointment"
                            style="border: none; cursor: pointer; font-size: 1rem;">Randevu Talebi Gönder</button>
                    </form>
                </div>

            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>