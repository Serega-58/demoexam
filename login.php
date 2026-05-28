<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    if (empty($login) || empty($password)) {
        $error = true;
        $error_message = 'Заполните все поля';
    } else {
        include('db.php');
        
        $stmt = $con->prepare("SELECT * FROM users WHERE login = ? AND password = ?");
        $stmt->bind_param("ss", $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error = true;
            $error_message = 'Неверный логин или пароль';
        } else {
            $user = $result->fetch_assoc();
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_fullname'] = $user['fullname'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            if ($user['is_admin'] == 1) {
                header('Location: admin.php');
            } else {
                header('Location: create.php');
            }
            exit;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Вход - Банкетам.Нет</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Oswald', sans-serif;
            background: #fffdd0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            width: 100%;
            background: #FFFDD0;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .logo { text-align: center; margin-bottom: 25px; }
        .logo h1 {
            font-size: 32px;
            color: #DAA520;
        }
        .form-header { text-align: center; margin-bottom: 25px; }
        .form-header h2 { color: #006400; font-size: 24px; }
        .error-message {
            background: #DC143C;
            color: #FFFDD0;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #006400;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #FFDAB9;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Oswald', sans-serif;
            background: #FFFDD0;
        }
        .form-group input:focus {
            outline: none;
            border-color: #DAA520;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #DAA520;
            color: #FFFDD0;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-login:hover { background: #006400; }
        .form-footer { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #FFDAB9; }
        .register-link { color: #DAA520; text-decoration: none; }
        .back-home { color: #006400; text-decoration: none; font-size: 14px; display: inline-block; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo"><h1>🍽️ Банкетам.Нет</h1></div>
        <div class="form-header"><h2>Вход в аккаунт</h2></div>
        
        <?php if ($error): ?>
            <div class="error-message">⚠️ <?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>🔑 Логин</label>
                <input type="text" name="login" placeholder="Введите логин" required>
            </div>
            <div class="form-group">
                <label>🔒 Пароль</label>
                <input type="password" name="password" placeholder="Введите пароль" required>
            </div>
            <button type="submit" class="btn-login">🎉 Войти</button>
        </form>
        
        <div class="form-footer">
            <p>Нет аккаунта? <a href="register.php" class="register-link">Зарегистрироваться →</a></p>
            <a href="index.php" class="back-home">← На главную</a>
        </div>
    </div>
</body>
</html>