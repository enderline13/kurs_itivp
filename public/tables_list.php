<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>BookTable — Управление столиками</title>
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
    <h2 id="tables-title">Управление столиками</h2>
    <p>Ресторан: <strong id="r-name">...</strong></p>

    <div class="form-row" style="margin-bottom:12px">
         <a class="button" id="add-table-btn" href="table_form.php">Добавить столик</a>
    </div>

    <section>
        <div id="tables-list-admin">
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