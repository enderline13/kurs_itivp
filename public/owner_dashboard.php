<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>BookTable — Владелец</title>
    <link rel="stylesheet" href="css/styles.css">
    <script defer src="js/main.js"></script>
</head>
<body>
<header class="site-header">
    <div class="site-wrap">
        <h1 class="brand">BookTable — Владелец</h1>
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
    <h2>Панель владельца</h2>

    <section>
        <h3>Мои рестораны</h3>
        <div id="owner-restaurants">
            <div class="restaurant-card placeholder">Загрузка...</div>
        </div>
        <div class="form-row" style="margin-top:12px">
            <a class="button" href="restaurant_form.php">Добавить ресторан</a>
        </div>
    </section>

    <section style="margin-top:18px">
        <h3>Бронирования</h3>
        <div id="owner-bookings">
            <div class="restaurant-card placeholder">Загрузка...</div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="site-wrap">
        <p>© 2025 BookTable</p>
    </div>
</footer>
</body>
</html>