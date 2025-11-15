<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>BookTable — Профиль</title>
    <link rel="stylesheet" href="css/styles.css">
    <script defer src="js/main.js"></script>
</head>
<body>
<header class="site-header">
    <div class="site-wrap">
        <h1 class="brand">BookTable</h1>
       <nav class="main-nav">
            <a href="index.php">Главная</a>
            <a href="restaurants.php">Рестораны</a>
            
            <a href="admin_dashboard.php" id="nav-admin" style="display:none">Админ</a>
            <a href="owner_dashboard.php" id="nav-owner" style="display:none">Панель</a>
            
            <a href="login.php" id="nav-login">Вход</a>
            <a href="register.php" id="nav-register">Регистрация</a>
            <a href="profile.php" id="nav-profile" style="display:none">Профиль</a>
            <a href="#" id="nav-logout" style="display:none">Выйти</a>
        </nav>
    </div>
</header>

<main class="container">
    <h2>Профиль</h2>

    <div id="profile-data" class="profile-block">
        <p><strong>Имя:</strong> —</p>
        <p><strong>Email:</strong> —</p>
        <p><strong>Телефон:</strong> —</p>
    </div>

    <h3>Мои бронирования</h3>
    <div id="my-bookings">
        <div class="restaurant-card placeholder">
            <p>Загрузка бронирований...</p>
        </div>
    </div>

    <div id="profile-msg" class="form-msg" role="status"></div>
</main>

<footer class="site-footer">
    <div class="site-wrap">
        <p>© 2025 BookTable</p>
    </div>
</footer>
</body>
</html>