            </div>
        </main>
    </div>
    <?php
    // Config dosyasını yükle (eğer yüklenmemişse)
    if (!defined('BASE_URL')) {
        require_once __DIR__ . '/../../config.php';
    }
    ?>
    <script src="<?php echo htmlspecialchars(admin_asset_url('admin.js')); ?>"></script>
</body>
</html>
