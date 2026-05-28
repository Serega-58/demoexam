<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

include('db.php');

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$status_updated = false;
$valid_statuses = ['Новая', 'Банкет назначен', 'Банкет завершен'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = (int)$_POST['request_id'];
    $status = $_POST['status'] ?? '';
    
    if (in_array($status, $valid_statuses)) {
        $stmt = $con->prepare("UPDATE request SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);
        if ($stmt->execute()) $status_updated = true;
        $stmt->close();
    }
}

$page = (int)($_GET['page'] ?? 1);
$limit = 5;
$offset = ($page - 1) * $limit;
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT r.*, u.login, u.fullname FROM request r JOIN users u ON r.user_id = u.id";
if ($status_filter && in_array($status_filter, $valid_statuses)) {
    $sql .= " WHERE r.status = '" . mysqli_real_escape_string($con, $status_filter) . "'";
}
$sql .= " ORDER BY r.date DESC LIMIT $limit OFFSET $offset";
$result = $con->query($sql);

$stats_sql = "SELECT COUNT(*) as total, 
    SUM(CASE WHEN status = 'Новая' THEN 1 ELSE 0 END) as new_requests,
    SUM(CASE WHEN status = 'Банкет назначен' THEN 1 ELSE 0 END) as assigned,
    SUM(CASE WHEN status = 'Банкет завершен' THEN 1 ELSE 0 END) as completed
FROM request";
$stats = $con->query($stats_sql)->fetch_assoc();

$count_sql = "SELECT COUNT(*) as cnt FROM request";
if ($status_filter) $count_sql .= " WHERE status = '" . mysqli_real_escape_string($con, $status_filter) . "'";
$total_rows = $con->query($count_sql)->fetch_assoc()['cnt'];
$total_pages = ceil($total_rows / $limit);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Админ-панель - Банкетам.Нет</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Oswald', sans-serif;
            background: #fffdd0;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: #FFFDD0;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header { background: #FFFDD0; padding: 25px 30px; border-bottom: 2px solid #FFDAB9; }
        .header h1 { color: #006400; font-size: 28px; }
        .nav-bar { display: flex; justify-content: space-between; padding: 15px 30px; background: #FFDAB9; flex-wrap: wrap; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 500; }
        .btn-outline { border: 2px solid #DAA520; color: #006400; background: #FFFDD0; }
        .btn-outline:hover { background: #DAA520; color: #FFFDD0; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 25px 30px; background: #FFFDD0; }
        .stat-card { background: #FFDAB9; padding: 20px; border-radius: 15px; text-align: center; border-left: 4px solid #DAA520; }
        .stat-number { font-size: 32px; font-weight: bold; color: #DAA520; }
        .filter-bar { padding: 0 30px 20px; display: flex; gap: 15px; flex-wrap: wrap; }
        .filter-bar select, .filter-bar button { padding: 8px 15px; border-radius: 10px; border: 2px solid #FFDAB9; font-family: 'Oswald', sans-serif; background: #FFFDD0; color: #006400; }
        .filter-bar button { background: #DAA520; color: #FFFDD0; border: none; cursor: pointer; }
        .filter-bar button:hover { background: #006400; }
        .requests-container { padding: 0 30px 30px; }
        .request-card { background: #FFFDD0; border-radius: 15px; padding: 20px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid; }
        .request-header { display: flex; justify-content: space-between; flex-wrap: wrap; margin-bottom: 15px; color: #006400; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 14px; color: #FFFDD0; }
        .status-new { background: #DAA520; }
        .status-assigned { background: #006400; }
        .status-completed { background: #DAA520; }
        .request-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin: 15px 0; }
        .detail-item { background: #FFDAB9; padding: 10px; border-radius: 10px; color: #006400; }
        .status-form { margin-top: 15px; padding-top: 15px; border-top: 1px solid #FFDAB9; }
        .status-form select { padding: 8px; border-radius: 10px; border: 2px solid #FFDAB9; margin-right: 10px; background: #FFFDD0; color: #006400; }
        .status-form button { padding: 8px 20px; background: #DAA520; color: #FFFDD0; border: none; border-radius: 10px; cursor: pointer; }
        .status-form button:hover { background: #006400; }
        .pagination { display: flex; justify-content: center; gap: 10px; padding: 20px 0; }
        .page-link { padding: 8px 15px; border: 2px solid #FFDAB9; border-radius: 10px; text-decoration: none; color: #006400; background: #FFFDD0; }
        .page-link.active, .page-link:hover { background: #DAA520; color: #FFFDD0; border-color: #DAA520; }
        .notification {
            position: fixed; top: 20px; right: 20px; background: #006400; color: #FFFDD0;
            padding: 15px 25px; border-radius: 10px; animation: slideIn 0.5s;
        }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } .request-details { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1><i class="fas fa-crown"></i> Панель администратора</h1></div>
        <div class="nav-bar">
            <a href="index.php" class="btn btn-outline"><i class="fas fa-home"></i> Главная</a>
            <a href="?logout=1" class="btn btn-outline" onclick="return confirm('Выйти?')"><i class="fas fa-sign-out-alt"></i> Выход</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-number"><?= $stats['total'] ?></div><div>Всего заявок</div></div>
            <div class="stat-card"><div class="stat-number"><?= $stats['new_requests'] ?></div><div>🆕 Новые</div></div>
            <div class="stat-card"><div class="stat-number"><?= $stats['assigned'] ?></div><div>🍽️ Назначены</div></div>
            <div class="stat-card"><div class="stat-number"><?= $stats['completed'] ?></div><div>✅ Завершены</div></div>
        </div>
        
        <div class="filter-bar">
            <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="status">
                    <option value="">Все статусы</option>
                    <option value="Новая" <?= $status_filter == 'Новая' ? 'selected' : '' ?>>🆕 Новая</option>
                    <option value="Банкет назначен" <?= $status_filter == 'Банкет назначен' ? 'selected' : '' ?>>🍽️ Банкет назначен</option>
                    <option value="Банкет завершен" <?= $status_filter == 'Банкет завершен' ? 'selected' : '' ?>>✅ Банкет завершен</option>
                </select>
                <button type="submit">Фильтровать</button>
            </form>
        </div>
        
        <div class="requests-container">
            <?php if ($result->num_rows == 0): ?>
                <p style="text-align: center; padding: 40px; color: #006400;">Нет заявок</p>
            <?php else: while ($row = $result->fetch_assoc()): 
                $status_class = match($row['status']) {
                    'Новая' => 'status-new',
                    'Банкет назначен' => 'status-assigned',
                    'Банкет завершен' => 'status-completed',
                    default => 'status-new'
                };
            ?>
            <div class="request-card" style="border-left-color: <?= $row['status'] == 'Новая' ? '#DAA520' : ($row['status'] == 'Банкет назначен' ? '#006400' : '#DAA520') ?>">
                <div class="request-header">
                    <div><strong><?= htmlspecialchars($row['fullname']) ?></strong> (@<?= htmlspecialchars($row['login']) ?>)</div>
                    <div><span class="request-id">№<?= $row['id'] ?></span> <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($row['status']) ?></span></div>
                </div>
                <div class="request-details">
                    <div class="detail-item"><strong>🍽️ Помещение:</strong> <?= htmlspecialchars($row['curses']) ?></div>
                    <div class="detail-item"><strong>📅 Дата:</strong> <?= htmlspecialchars($row['date']) ?></div>
                    <div class="detail-item"><strong>💳 Оплата:</strong> <?= htmlspecialchars($row['payment']) ?></div>
                    <div class="detail-item"><strong>📝 Пожелания:</strong> <?= htmlspecialchars($row['review'] ?: '—') ?></div>
                </div>
                <div class="status-form">
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                        <select name="status">
                            <option value="Новая" <?= $row['status'] == 'Новая' ? 'selected' : '' ?>>🆕 Новая</option>
                            <option value="Банкет назначен" <?= $row['status'] == 'Банкет назначен' ? 'selected' : '' ?>>🍽️ Банкет назначен</option>
                            <option value="Банкет завершен" <?= $row['status'] == 'Банкет завершен' ? 'selected' : '' ?>>✅ Банкет завершен</option>
                        </select>
                        <button type="submit">Изменить статус</button>
                    </form>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&status=<?= urlencode($status_filter) ?>" class="page-link <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($status_updated): ?>
    <div class="notification"><i class="fas fa-check-circle"></i> Статус заявки обновлён!</div>
    <script>setTimeout(() => document.querySelector('.notification')?.remove(), 3000);</script>
    <?php endif; ?>
</body>
</html>