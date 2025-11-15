<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>BookTable — Управление рестораном</title>
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
    <h2 id="form-title">Добавить ресторан</h2>
    
    <form id="restaurant-form">
        <input type="hidden" name="id" id="r-id" value="">

        <label>
            Название
            <input type="text" name="name" id="r-name" required placeholder="Название ресторана">
        </label>
        <label>
            Описание
            <textarea name="description" id="r-description" placeholder="Описание..."></textarea>
        </label>
        <label>
            Адрес
            <input type="text" name="address" id="r-address" placeholder="ул. Примерная, 10">
        </label>
        <label>
            Город
            <input type="text" name="city" id="r-city" placeholder="Москва">
        </label>

        <div class="form-row">
            <button type="submit" class="button">Сохранить</button>
            <a class="button outline" href="owner_dashboard.php">Отмена</a>
        </div>
        <div id="form-msg" class="form-msg" role="status"></div>
    </form>
</main>

<footer class="site-footer">
    <div class="site-wrap">
        <p>© 2025 BookTable</p>
    </div>
</footer>
</body>
</html>