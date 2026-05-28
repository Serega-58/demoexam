<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include('db.php');

$user_id = (int)$_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review']) && isset($_POST['request_id'])) {
    $review = trim($_POST['review']);
    $request_id = (int)$_POST['request_id'];
    
    $check = $con->prepare("SELECT id, status FROM request WHERE id = ? AND user_id = ? AND status = 'Банкет завершен'");
    $check->bind_param("ii", $request_id, $user_id);
    $check->execute();
    $result_check = $check->get_result();
    
    if ($result_check->num_rows > 0) {
        $stmt = $con->prepare("UPDATE request SET review = ? WHERE id = ?");
        $stmt->bind_param("si", $review, $request_id);
        if ($stmt->execute()) {
            $message = '<div class="success-message">⭐ Отзыв успешно сохранён!</div>';
        }
        $stmt->close();
    }
    $check->close();
}

$query = $con->prepare("SELECT * FROM request WHERE user_id = ? ORDER BY date DESC");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Мои заявки - Банкетам.Нет</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Oswald', sans-serif;
            background: #fffdd0;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #FFFDD0;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        h1 { text-align: center; color: #006400; margin-bottom: 20px; }
        .btn-home {
            display: inline-block;
            background: #DAA520;
            color: #FFFDD0;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .btn-home:hover { background: #006400; }
        .success-message {
            background: #DAA520;
            color: #FFFDD0;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .request-card {
            border: 2px solid #FFDAB9;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background: #FFFDD0;
        }
        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .request-id { font-weight: bold; color: #006400; font-size: 18px; }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            color: #FFFDD0;
        }
        .status-new { background: #DAA520; }
        .status-assigned { background: #006400; }
        .status-completed { background: #DAA520; }
        .request-details p { margin: 8px 0; color: #006400; }
        .review-section { margin-top: 15px; padding-top: 15px; border-top: 1px solid #FFDAB9; }
        .review-text { background: #FFDAB9; padding: 10px; border-radius: 10px; margin-top: 10px; color: #006400; }
        .review-form { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
        .review-form input {
            flex: 1;
            padding: 10px;
            border: 2px solid #FFDAB9;
            border-radius: 10px;
            font-family: 'Oswald', sans-serif;
            background: #FFFDD0;
        }
        .review-form button {
            padding: 10px 20px;
            background: #DAA520;
            color: #FFFDD0;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .review-form button:hover { background: #006400; }
        .empty-state { text-align: center; padding: 40px; color: #006400; }
        @media (max-width: 600px) { .container { padding: 20px; } .request-header { flex-direction: column; gap: 10px; align-items: flex-start; } }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn-home">🏠 На главную</a>
        <h1>📋 Мои заявки на банкет</h1>
        
        <?php echo $message; ?>
        
        <?php if ($result->num_rows == 0): ?>
            <div class="empty-state">
                🎉 У вас пока нет заявок<br><br>
                <a href="create.php" style="color: #DAA520;">➕ Создать новую заявку</a>
            </div>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): 
                $status_class = match($row['status']) {
                    'Новая' => 'status-new',
                    'Банкет назначен' => 'status-assigned',
                    'Банкет завершен' => 'status-completed',
                    default => 'status-new'
                };
            ?>
            <div class="request-card">
                <div class="request-header">
                    <span class="request-id">🎯 Заявка #<?= $row['id'] ?></span>
                    <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($row['status']) ?></span>
                </div>
                <div class="request-details">
                    <p><strong>🍽️ Помещение:</strong> <?= htmlspecialchars($row['curses']) ?></p>
                    <p><strong>📅 Дата и время:</strong> <?= htmlspecialchars($row['date']) ?></p>
                    <p><strong>💳 Оплата:</strong> <?= htmlspecialchars($row['payment']) ?></p>
                </div>
                
                <div class="review-section">
                    <?php if (!empty($row['review'])): ?>
                        <div class="review-text">
                            <strong>⭐ Ваш отзыв:</strong> <?= htmlspecialchars($row['review']) ?>
                        </div>
                    <?php elseif ($row['status'] == 'Банкет завершен'): ?>
                        <form method="POST" class="review-form">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <input type="text" name="review" placeholder="✍️ Оставьте отзыв о проведённом банкете..." required>
                            <button type="submit">⭐ Оставить отзыв</button>
                        </form>
                    <?php elseif ($row['status'] != 'Банкет завершен'): ?>
                        <p style="color: #006400; font-size: 14px;">📝 Отзыв можно оставить после завершения банкета</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="create.php" style="background: #DAA520; color: #FFFDD0; padding: 12px 25px; text-decoration: none; border-radius: 10px; display: inline-block;">🎉 Создать новую заявку</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $query->close(); ?>