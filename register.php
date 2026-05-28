<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = false;
$error_message = '';
$success = false;
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    
    $form_data = compact('login', 'fullname', 'phone', 'email');
    
    $errors = [];
    
    if (empty($login)) {
        $errors[] = 'Логин обязателен для заполнения';
    } elseif (!preg_match('/^[a-zA-Z0-9]{6,}$/', $login)) {
        $errors[] = 'Логин должен содержать только латиницу и цифры, минимум 6 символов';
    }
    
    if (empty($password)) {
        $errors[] = 'Пароль обязателен для заполнения';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Пароль должен содержать минимум 8 символов';
    }
    
    if (empty($fullname)) {
        $errors[] = 'ФИО обязательно для заполнения';
    }
    
    if (empty($phone)) {
        $errors[] = 'Телефон обязателен для заполнения';
    }
    
    if (empty($email)) {
        $errors[] = 'Email обязателен для заполнения';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }
    
    if (empty($errors)) {
        include('db.php');
        
        $stmt = $con->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = true;
            $error_message = 'Пользователь с таким логином уже существует';
        } else {
            $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = true;
                $error_message = 'Пользователь с таким email уже существует';
            } else {
                $stmt = $con->prepare("INSERT INTO users (login, password, fullname, phone, email, is_admin) VALUES (?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("sssss", $login, $password, $fullname, $phone, $email);
                
                if ($stmt->execute()) {
                    $success = true;
                    header('refresh:2;url=login.php');
                } else {
                    $error = true;
                    $error_message = 'Ошибка при регистрации';
                }
                $stmt->close();
            }
        }
        $stmt->close();
    } else {
        $error = true;
        $error_message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Регистрация - Банкетам.Нет</title>
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
            max-width: 500px;
            margin: 0 auto;
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
        .success-message {
            background: #DAA520;
            color: #FFFDD0;
            padding: 15px;
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
        .hint {
            font-size: 11px;
            color: #006400;
            margin-top: 4px;
            display: block;
        }
        .btn-register {
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
            margin-top: 10px;
        }
        .btn-register:hover { background: #006400; }
        .form-footer { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #FFDAB9; }
        .login-link { color: #DAA520; text-decoration: none; }
        .back-home { color: #006400; text-decoration: none; font-size: 14px; display: inline-block; margin-top: 10px; }
        @media (max-width: 550px) { .container { padding: 20px; } .logo h1 { font-size: 26px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo"><h1>🍽️ Банкетам.Нет</h1></div>
        <div class="form-header"><h2>Регистрация</h2></div>
        
        <?php if ($error): ?>
            <div class="error-message">⚠️ <?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">✅ Регистрация успешна! Перенаправление...</div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>👤 ФИО</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($form_data['fullname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>📱 Телефон</label>
                <input type="tel" name="phone" placeholder="+7(XXX)XXX-XX-XX" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required>
                <span class="hint">Формат: +7(XXX)XXX-XX-XX</span>
            </div>
            <div class="form-group">
                <label>📧 Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>🔑 Логин (латиница+цифры, ≥6 символов)</label>
                <input type="text" name="login" value="<?php echo htmlspecialchars($form_data['login'] ?? ''); ?>" pattern="[a-zA-Z0-9]{6,}" required>
            </div>
            <div class="form-group">
                <label>🔒 Пароль (≥8 символов)</label>
                <input type="password" name="password" minlength="8" required>
            </div>
            <button type="submit" class="btn-register">🎉 Зарегистрироваться</button>
        </form>
        <?php endif; ?>
        
        <div class="form-footer">
            <p>Уже есть аккаунт? <a href="login.php" class="login-link">Войти →</a></p>
            <a href="index.php" class="back-home">← На главную</a>
        </div>
    </div>
</body>
</html>