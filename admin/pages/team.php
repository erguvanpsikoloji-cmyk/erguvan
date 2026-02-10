<?php
// admin/pages/team.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';

$db = getDB();
$team_members = $db->query("SELECT * FROM team_members ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$page = 'team';
$page_title = 'Uzman Ekibimiz';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="header-action">
    <h2>Uzman Ekibimiz</h2>
    <a href="<?php echo admin_url('pages/team-add.php'); ?>" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            style="vertical-align: middle; margin-right: 5px;">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Yeni Uzman Ekle
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">Sıra</th>
                    <th style="width: 80px;">Görsel</th>
                    <th>Ad Soyad</th>
                    <th>Ünvan</th>
                    <th>Durum</th>
                    <th style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($team_members)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">
                            Henüz ekip üyesi eklenmemiş.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($team_members as $member): ?>
                        <tr>
                            <td><?php echo $member['display_order']; ?></td>
                            <td>
                                <?php if ($member['image']): ?>
                                    <img src="<?php echo htmlspecialchars($member['image']); ?>" alt=""
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <div
                                        style="width: 40px; height: 40px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #888;">
                                        Yok
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="font-medium"><?php echo htmlspecialchars($member['name']); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($member['title']); ?></td>
                            <td>
                                <?php if ($member['is_active']): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?php echo admin_url('pages/team-edit.php?id=' . $member['id']); ?>" class="btn-icon"
                                    title="Düzenle">
                                    ✏️
                                </a>
                                <a href="<?php echo admin_url('pages/team-delete.php?id=' . $member['id'] . '&token=' . $_SESSION['csrf_token']); ?>"
                                    class="btn-icon delete-process"
                                    onclick="return confirm('Bu uzmanı silmek istediğinize emin misiniz?');" title="Sil">
                                    🗑️
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
