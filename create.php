<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$success = false;
$error = false;
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $review = trim($_POST['review'] ?? '');
    $date = $_POST['date'];
    $curses = $_POST['curses'];
    $payment = $_POST['payment'];
    
    include('db.php');
    
    $user_id = (int)$_SESSION['user_id'];
    
    $stmt = $con->prepare("INSERT INTO request (user_id, curses, date, payment, review, status) VALUES (?, ?, ?, ?, ?, 'Новая')");
    $stmt->bind_param("issss", $user_id, $curses, $date, $payment, $review);
    
    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = true;
        $error_msg = 'Ошибка при создании заявки';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Новая заявка - Банкетам.Нет</title>
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
            max-width: 550px;
            margin: 0 auto;
            background: #FFFDD0;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        h1 { text-align: center; color: #006400; margin-bottom: 25px; font-size: 28px; }
        .nav-buttons { display: flex; gap: 15px; margin-bottom: 25px; }
        .btn-nav {
            flex: 1;
            padding: 12px;
            background: #DAA520;
            color: #FFFDD0;
            text-decoration: none;
            text-align: center;
            border-radius: 10px;
            font-weight: 500;
        }
        .btn-nav:hover { background: #006400; }
        .success-message {
            background: #DAA520;
            color: #FFFDD0;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .error-message {
            background: #DC143C;
            color: #FFFDD0;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #006400;
        }
        .form-group select, .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #FFDAB9;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Oswald', sans-serif;
            background: #FFFDD0;
        }
        .form-group select:focus, .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #DAA520;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #DAA520;
            color: #FFFDD0;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover { background: #006400; }
        @media (max-width: 550px) { .container { padding: 20px; } .nav-buttons { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-buttons">
            <a href="index.php" class="btn-nav">🏠 Главная</a>
            <a href="history.php" class="btn-nav">📋 Мои заявки</a>
        </div>
        
        <h1>🎉 Бронирование площадки</h1>
        
        <?php if ($success): ?>
            <div class="success-message">
                ✅ Заявка успешно отправлена!<br>
                <a href="history.php" style="color: #FFFDD0;">📋 Перейти к истории заявок</a>
            </div>
        <?php elseif ($error): ?>
            <div class="error-message">❌ <?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>🍽️ Выберите помещение</label>
                <select name="curses" required>
                    <option value="">-- Выберите --</option>
                    <option value="Банкетный зал">🏛️ Банкетный зал</option>
                    <option value="Ресторан">🍷 Ресторан</option>
                    <option value="Летняя веранда">🌞 Летняя веранда</option>
                    <option value="Закрытая веранда">🏠 Закрытая веранда</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>📅 Дата и время начала банкета</label>
                <input type="datetime-local" name="date" required>
            </div>
            
            <div class="form-group">
                <label>💳 Способ оплаты</label>
                <select name="payment" required>
                    <option value="">-- Выберите --</option>
                    <option value="Наличные">💵 Наличные</option>
                    <option value="Карта">💳 Банковская карта</option>
                    <option value="Перевод">🌐 Онлайн-перевод</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>📝 Дополнительные пожелания</label>
                <textarea name="review" placeholder="Опишите особые пожелания: меню, декор, музыка..."></textarea>
            </div>
            
            <button type="submit" class="btn-submit">🎉 Забронировать</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>