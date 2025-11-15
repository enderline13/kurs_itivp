<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>BookTable — Бронирование</title>
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
    <h2>Бронирование столика</h2>

    <form id="reservation-form">
        <label>
            Ресторан
            <select name="restaurant_id" id="reservation-restaurant" required>
                <option value="">Выберите ресторан</option>
            </select>
        </label>

        <label>
            Столик
            <select name="table_id" id="reservation-table" required>
                <option value="">Выберите столик</option>
            </select>
        </label>

        <label>
            Дата
            <input type="date" name="date" id="reservation-date" required>
        </label>

        <label>
            Время
            <input type="time" name="time" id="reservation-time" required>
        </label>

        <label>
            Количество гостей
            <select name="guests" id="reservation-guests" required>
                <option value="1">1</option>
                <option value="2" selected>2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </label>

        <label>
            Комментарий
            <input type="text" name="notes" id="reservation-notes" placeholder="Дополнительная информация">
        </label>

        <div class="form-row">
            <button type="submit" class="button">Забронировать</button>
            <a class="button outline" href="restaurants.php">Отмена</a>
        </div>
        <div id="reservation-msg" class="form-msg" role="status"></div>
    </form>
</main>

<footer class="site-footer">
    <div class="site-wrap">
        <p>© 2025 BookTable</p>
    </div>
</footer>
</body>
</html>