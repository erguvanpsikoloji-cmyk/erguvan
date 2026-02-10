<?php 
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';

$db = getDB();

// Durum güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $id = (int)$_POST['id'];
        $status = $_POST['status'] ?? 'pending';
        // MySQL'de updated_at otomatik olarak CURRENT_TIMESTAMP ile güncellenir
        $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
    redirect(admin_url('pages/appointments.php'));
}

// Silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $id = (int)$_POST['delete_id'];
        $db->prepare("DELETE FROM appointments WHERE id = ?")->execute([$id]);
    }
    redirect(admin_url('pages/appointments.php'));
}

// Filtreleme
$status_filter = $_GET['status'] ?? 'all';
$where = '';
$params = [];

if ($status_filter !== 'all') {
    $where = "WHERE status = ?";
    $params[] = $status_filter;
}

// Randevu taleplerini getir
$sql = "SELECT * FROM appointments " . $where . " ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

// İstatistikler
$total = $db->query("SELECT COUNT(*) as count FROM appointments")->fetch()['count'];
$pending = $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'")->fetch()['count'];
$confirmed = $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'confirmed'")->fetch()['count'];
$completed = $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'completed'")->fetch()['count'];

$page = 'appointments';
$page_title = 'Randevu Talepleri';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="blog-header">
    <div>
        <h2 style="margin: 0 0 5px 0;">Randevu Talepleri</h2>
        <p style="color: #64748b; margin: 0;">Toplam <?php echo count($appointments); ?> talep</p>
    </div>
</div>

<!-- İstatistikler -->
<div class="dashboard-stats" style="margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-card-title">Toplam Talep</div>
        <div class="stat-card-value"><?php echo $total; ?></div>
    </div>
    <div class="stat-card orange">
        <div class="stat-card-title">Bekleyen</div>
        <div class="stat-card-value"><?php echo $pending; ?></div>
    </div>
    <div class="stat-card green">
        <div class="stat-card-title">Onaylanan</div>
        <div class="stat-card-value"><?php echo $confirmed; ?></div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
        <div class="stat-card-title">Tamamlanan</div>
        <div class="stat-card-value"><?php echo $completed; ?></div>
    </div>
</div>

<!-- Filtreleme -->
<div class="blog-filters">
    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Tüm Durumlar</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Bekleyen</option>
                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Onaylanan</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Tamamlanan</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>İptal Edilen</option>
            </select>
        </div>
    </form>
</div>

<?php if (empty($appointments)): ?>
    <div class="empty-state">
        <p>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Henüz randevu talebi bulunmuyor.
        </p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ad Soyad</th>
                    <th>İletişim</th>
                    <th>Hizmet</th>
                    <th>Mesaj</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $app): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($app['name']); ?></strong>
                    </td>
                    <td>
                        <div style="font-size: 13px;">
                            <div>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <?php echo htmlspecialchars($app['email']); ?>
                            </div>
                            <div>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <?php echo htmlspecialchars($app['phone']); ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="category-badge"><?php 
                            $services = [
                                'bireysel' => 'Bireysel Terapi',
                                'online' => 'Online Terapi',
                                'cift' => 'Çift Terapisi',
                                'aile' => 'Aile Danışmanlığı'
                            ];
                            echo $services[$app['service']] ?? $app['service'];
                        ?></span>
                    </td>
                    <td>
                        <?php if ($app['message']): ?>
                            <small style="color: #64748b;"><?php echo htmlspecialchars(substr($app['message'], 0, 50)); ?><?php echo strlen($app['message']) > 50 ? '...' : ''; ?></small>
                        <?php else: ?>
                            <small style="color: #94a3b8;">-</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" class="form-control" style="width: auto; display: inline-block; padding: 4px 8px; font-size: 13px;" onchange="this.form.submit()">
                                <option value="pending" <?php echo $app['status'] === 'pending' ? 'selected' : ''; ?>>Bekleyen</option>
                                <option value="confirmed" <?php echo $app['status'] === 'confirmed' ? 'selected' : ''; ?>>Onaylanan</option>
                                <option value="completed" <?php echo $app['status'] === 'completed' ? 'selected' : ''; ?>>Tamamlanan</option>
                                <option value="cancelled" <?php echo $app['status'] === 'cancelled' ? 'selected' : ''; ?>>İptal</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <?php echo date('d.m.Y', strtotime($app['created_at'])); ?><br>
                        <small style="color: #94a3b8;"><?php echo date('H:i', strtotime($app['created_at'])); ?></small>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="showAppointmentDetails(<?php echo htmlspecialchars(json_encode($app)); ?>)">👁️ Detay</button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Bu randevu talebini silmek istediğinizden emin misiniz?');">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="delete_id" value="<?php echo $app['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Detay Modal -->
<div id="appointmentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 16px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Randevu Detayları</h3>
            <button onclick="closeAppointmentModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div id="appointmentDetails"></div>
    </div>
</div>

<script>
function showAppointmentDetails(app) {
    const services = {
        'bireysel': 'Bireysel Terapi',
        'online': 'Online Terapi',
        'cift': 'Çift Terapisi',
        'aile': 'Aile Danışmanlığı'
    };
    
    const statusLabels = {
        'pending': 'Bekleyen',
        'confirmed': 'Onaylanan',
        'completed': 'Tamamlanan',
        'cancelled': 'İptal Edilen'
    };
    
    const html = `
        <div style="line-height: 1.8;">
            <p><strong>Ad Soyad:</strong> ${app.name}</p>
            <p><strong>E-posta:</strong> ${app.email}</p>
            <p><strong>Telefon:</strong> ${app.phone}</p>
            <p><strong>Hizmet:</strong> ${services[app.service] || app.service}</p>
            <p><strong>Durum:</strong> ${statusLabels[app.status] || app.status}</p>
            <p><strong>Tarih:</strong> ${new Date(app.created_at).toLocaleString('tr-TR')}</p>
            ${app.message ? `<p><strong>Mesaj:</strong><br>${app.message}</p>` : ''}
        </div>
    `;
    
    document.getElementById('appointmentDetails').innerHTML = html;
    document.getElementById('appointmentModal').style.display = 'flex';
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').style.display = 'none';
}

// Modal dışına tıklanınca kapat
document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAppointmentModal();
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

