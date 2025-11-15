<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>BookTable — Ресторан</title>
    <link rel="stylesheet" href="css/styles.css">
    <script defer src="js/main.js"></script>
</head>
<body>
<header class="site-header">
    <div class="site-wrap">
        <h1 class="brand">BookTable</h1>
        </div>
</header>

<main class="container">
    <h2 id="r-name">Название ресторана</h2>
    <div id="r-desc" class="restaurant-info">
        <p id="r-address">Адрес: —</p>
        <p id="r-city">Город: —</p>
        <p id="r-text">Описание —</p>
    </div>

    <section class="tables">
        <h3>Часы работы</h3>
        <div id="r-hours">
            <div class="restaurant-card placeholder"><p>Загрузка...</p></div>
        </div>
    </section>

    <section class="tables">
        <h3>Столики</h3>
        <div id="tables-list">
            <div class="restaurant-card placeholder">
                <p>Загрузка столиков...</p>
            </div>
        </div>
    </section>

    <div id="restaurant-msg" class="form-msg" role="status"></div>
</main>
</body>
</html>