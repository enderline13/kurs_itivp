<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>BookTable — Вход</title>
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
    <h2>Вход</h2>
    <form id="login-form">
        <label>
            Email
            <input type="email" name="email" required placeholder="example@mail.com">
        </label>
        <label>
            Пароль
            <input type="password" name="password" required placeholder="Пароль">
        </label>
        <div class="form-row">
            <button type="submit" class="button">Войти</button>
            <a class="link" href="register.php">Нет аккаунта?</a>
        </div>
        <div id="login-msg" class="form-msg" role="status"></div>
    </form>
</main>

<footer class="site-footer">
    <div class="site-wrap">
        <p>© 2025 BookTable</p>
    </div>
</footer>
</body>
</html>