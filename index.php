<?php
session_start();

// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Проверяем, установлен ли ключ admin в сессии
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Банкетам.Нет - выбор площадки для банкета</title>
  <!-- Подключение шрифта Oswald -->
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
  
  <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
<!-- Шапка сайта -->
<header class="header">
  <div class="nav">
    <a href="index.php" class="logo">🍽️ Банкетам.Нет</a>

    <!-- Кнопки навигации -->
    <div class="nav-buttons">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php" class="btn-login">🔐 Войти</a>
        <a href="register.php" class="btn-register">📝 Регистрация</a>
      <?php elseif ($is_admin): ?>
        <a href="admin.php" class="btn-admin">👑 Панель администратора</a>
        <a href="?logout=1" class="btn-exit">🚪 Выход</a>
      <?php elseif (isset($_SESSION['user_id'])): ?>
        <a href="history.php" class="btn-lk">📋 Мои заявки</a>
        <a href="create.php" class="btn-create">🎉 Новая заявка</a>
        <a href="?logout=1" class="btn-exit">🚪 Выход</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Слайдер с картинками для банкетных площадок -->
<div class="slideshow-container">
  <div class="mySlides fade">
    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200" alt="Банкетный зал">
    <div class="text">🏛️ Просторный банкетный зал</div>
  </div>

  <div class="mySlides fade">
    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1200" alt="Ресторан для банкета">
    <div class="text">🍷 Изысканный ресторан</div>
  </div>

  <div class="mySlides fade">
    <img src="https://images.unsplash.com/photo-1604014237800-1c9102c219da?w=1200" alt="Летняя веранда">
    <div class="text">🌞 Уютная летняя веранда</div>
  </div>

  <div class="mySlides fade">
    <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?w=1200" alt="Закрытая веранда">
    <div class="text">🏠 Тёплая закрытая веранда</div>
  </div>

  <a class="prev" onclick="plusSlides(-1)">❮</a>
  <a class="next" onclick="plusSlides(1)">❯</a>
</div>

<!-- Точки навигации -->
<div class="dot-container">
  <span class="dot" onclick="currentSlide(1)"></span>
  <span class="dot" onclick="currentSlide(2)"></span>
  <span class="dot" onclick="currentSlide(3)"></span>
  <span class="dot" onclick="currentSlide(4)"></span>
</div>

<!-- Основной контент -->
<section class="features-section">
  <h2 class="features-title">✨ Почему выбирают «Банкетам.Нет»?</h2>
  
  <div class="features-grid">
    <div class="feature-card">
      <h3>🏛️ Лучшие залы и рестораны</h3>
      <p>Подберём идеальное место для вашего торжества — от камерных залов до больших ресторанов.</p>
    </div>
    
    <div class="feature-card">
      <h3>🌿 Летние и закрытые веранды</h3>
      <p>Организуем банкет на свежем воздухе или в уютной закрытой веранде в любое время года.</p>
    </div>
    
    <div class="feature-card">
      <h3>🤝 Помощь с выбором</h3>
      <p>Наши менеджеры помогут выбрать помещение под любой бюджет и количество гостей.</p>
    </div>
  </div>
</section>

<script>
// JavaScript для управления слайдером
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");

  if (n > slides.length) { slideIndex = 1 }
  if (n < 1) { slideIndex = slides.length }

  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}

// Автоматическое переключение слайдов каждые 3 секунды
let slideInterval = setInterval(function() {
  plusSlides(1);
}, 3000);

// Останавливаем автоматическое переключение при наведении на слайдер
const slideshowContainer = document.querySelector('.slideshow-container');
if (slideshowContainer) {
  slideshowContainer.addEventListener('mouseenter', function() {
    clearInterval(slideInterval);
  });
  
  slideshowContainer.addEventListener('mouseleave', function() {
    slideInterval = setInterval(function() {
      plusSlides(1);
    }, 3000);
  });
}
</script>
</body>
</html>