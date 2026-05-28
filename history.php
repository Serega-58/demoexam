<?php
session_start();
if(!isset($_SESSION['user_id'])) die('Чтобы посмотреть историю заявок, надо войти в аккаунт.');
include('db.php');

// Код изменения отзыва
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review'])) {
    $review = $con->real_escape_string($_POST['review']);
    $user_id = (int)$_SESSION['user_id'];
    $request_id = (int)$_POST['request_id'];
    $con->query("UPDATE request SET review='$review' WHERE id='$request_id' AND user_id='$user_id'");
    echo '<div class="success-message">✓ Отзыв успешно оставлен!</div>';
}

// Код истории заявок
$user_id = (int)$_SESSION['user_id'];
$query = $con->query("SELECT * FROM request WHERE user_id='$user_id' ORDER BY date DESC");
if(!$query) die('query error: ' . $con->error); 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои заявки - Банкетам.Нет</title>
    <!-- Подключение шрифта Oswald -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
   
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn-home">🏠 На главную</a>
        
        <h1>📋 Мои заявки на банкет</h1>
        
        <?php
        $i = 0;
        if($query->num_rows == 0) {
            echo '<div class="empty-state">🎉 У вас пока нет заявок.<br><br>✍️ <a href="create.php" style="color: var(--gold);">Создать новую заявку</a></div>';
        }
        while($request = $query->fetch_assoc()) {
            $i++; 
            
            // Определяем класс статуса
            $status_class = 'status-new';
            $status_text = htmlspecialchars($request['status']);
            if($status_text == 'Новая') $status_class = 'status-new';
            elseif($status_text == 'В обработке') $status_class = 'status-processing';
            elseif($status_text == 'Завершено') $status_class = 'status-completed';
            elseif($status_text == 'Отменено') $status_class = 'status-cancelled';
            
            echo '
            <div class="request">
                <h2>🎯 Заявка #' . $request['id'] . '</h2>
                <p><b>📅 Дата проведения:</b> ' . htmlspecialchars($request['date']) . '</p>
                <p><b>🍽️ Тип площадки:</b> ' . htmlspecialchars($request['curses']) . '</p>
                <p><b>💳 Способ оплаты:</b> ' . htmlspecialchars($request['payment']) . '</p>
                <p><b>📊 Статус:</b> <span class="' . $status_class . '">' . $status_text . '</span></p>';
            
            // Если есть отзыв, показываем его
            if(!empty($request['review'])) {
                echo '<div class="review-text"><b>⭐ Ваш отзыв:</b> ' . htmlspecialchars($request['review']) . '</div>';
            }
            
            // Если статус "Завершено" - показываем форму для отзыва
            if($request['status'] === 'Завершено') {
                echo '
                <div class="review-form">
                    <form action="" method="POST" style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap;">
                        <input type="hidden" name="request_id" value="' . $request['id'] . '">
                        <input type="text" name="review" placeholder="✍️ Оставьте отзыв о проведённом банкете..." value="' . htmlspecialchars($request['review'] ?? '') . '">
                        <button type="submit">⭐ Оставить отзыв</button>
                    </form>
                </div>';
            }
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="create.php" style="background: linear-gradient(135deg, var(--gold) 0%, var(--forest-green) 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: 500; display: inline-block;">🎉 Создать новую заявку</a>
        </div>
    </div>
</body>
</html>