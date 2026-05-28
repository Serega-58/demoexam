<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
  <title>Банкетам.Нет - выбор площадки для банкета</title>
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Oswald', sans-serif;
      background: #fffdd0;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }
    .header {
      background: #FFFDD0;
      padding: 15px 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      flex-wrap: wrap;
      gap: 15px;
    }
    .logo {
      color: #DAA520;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 2px;
      text-decoration: none;
      text-transform: uppercase;
    }
    .nav-buttons a {
      margin-left: 15px;
      padding: 10px 20px;
      border: 2px solid #DAA520;
      border-radius: 25px;
      color: #DAA520;
      text-decoration: none;
      transition: all 0.3s ease;
      font-weight: 500;
      background: #FFFDD0;
    }
    .nav-buttons a:hover {
      background-color: #DAA520;
      color: #FFFDD0;
    }
    .slideshow-container {
      max-width: 1000px;
      position: relative;
      margin: 40px auto;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }
    .mySlides { display: none; }
    .fade { animation: fadeIn 1.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0.4; } to { opacity: 1; } }
    .mySlides img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }
    @media (max-width: 768px) { .mySlides img { height: 250px; } }
    .text {
      position: absolute;
      bottom: 20px;
      left: 20px;
      background: #006400;
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 18px;
      font-weight: 600;
      color: #DAA520;
    }
    .prev, .next {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: #006400;
      color: #DAA520;
      border: none;
      cursor: pointer;
      padding: 15px 20px;
      font-size: 18px;
      border-radius: 50%;
      transition: all 0.3s ease;
    }
    .prev { left: 10px; }
    .next { right: 10px; }
    .prev:hover, .next:hover { background-color: #DAA520; color: #006400; }
    .dot-container { text-align: center; padding: 20px 0; }
    .dot {
      cursor: pointer;
      height: 15px;
      width: 15px;
      margin: 0 5px;
      background-color: #FFDAB9;
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.3s ease;
    }
    .dot.active, .dot:hover { background-color: #DAA520; }
    .features-section {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .features-title {
      text-align: center;
      color: #DAA520;
      margin-bottom: 30px;
      font-size: 32px;
      font-weight: 600;
      text-transform: uppercase;
    }
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }
    .feature-card {
      background: #FFFDD0;
      padding: 25px;
      border-radius: 15px;
      text-align: center;
      transition: all 0.3s ease;
    }
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      border: 1px solid #DAA520;
    }
    .feature-card h3 { color: #006400; font-size: 22px; margin-bottom: 15px; }
    .feature-card p { color: #006400; line-height: 1.5; }
    @media (max-width: 768px) {
      .nav { flex-direction: column; text-align: center; }
      .nav-buttons a { display: inline-block; margin: 5px; }
      .features-title { font-size: 24px; }
    }
  </style>
</head>
<body>
<header class="header">
  <div class="nav">
    <a href="index.php" class="logo">🍽️ Банкетам.Нет</a>
    <div class="nav-buttons">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php">🔐 Войти</a>
        <a href="register.php">📝 Регистрация</a>
      <?php elseif (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="admin.php">👑 Админ-панель</a>
        <a href="?logout=1">🚪 Выход</a>
      <?php else: ?>
        <a href="history.php">📋 Мои заявки</a>
        <a href="create.php">🎉 Новая заявка</a>
        <a href="?logout=1">🚪 Выход</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<section class="features-section">
  <h2 class="features-title">✨ Почему выбирают «Банкетам.Нет»?</h2>
  <div class="features-grid">
    <div class="feature-card"><h3>🏛️ Лучшие залы и рестораны</h3><p>Подберём идеальное место для вашего торжества</p></div>
    <div class="feature-card"><h3>🌿 Летние и закрытые веранды</h3><p>Организуем банкет на свежем воздухе</p></div>
    <div class="feature-card"><h3>🤝 Помощь с выбором</h3><p>Поможем выбрать помещение под любой бюджет</p></div>
  </div>
</section>

<script>
let slideIndex = 1;
showSlides(slideIndex);
function plusSlides(n) { showSlides(slideIndex += n); }
function currentSlide(n) { showSlides(slideIndex = n); }
function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) { slideIndex = 1 }
  if (n < 1) { slideIndex = slides.length }
  for (i = 0; i < slides.length; i++) { slides[i].style.display = "none"; }
  for (i = 0; i < dots.length; i++) { dots[i].className = dots[i].className.replace(" active", ""); }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}
let slideInterval = setInterval(function() { plusSlides(1); }, 3000);
const slideshowContainer = document.querySelector('.slideshow-container');
if (slideshowContainer) {
  slideshowContainer.addEventListener('mouseenter', function() { clearInterval(slideInterval); });
  slideshowContainer.addEventListener('mouseleave', function() { slideInterval = setInterval(function() { plusSlides(1); }, 3000); });
}
</script>
</body>
</html>