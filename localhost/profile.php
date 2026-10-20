<?php
require_once 'db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($con, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    header('Location: login.php');
    exit;
}

$orders_count_query = "SELECT COUNT(*) AS cnt FROM orders WHERE user_id = $user_id";
$orders_count_result = mysqli_query($con, $orders_count_query);
$orders_count = 0;

if ($orders_count_result) {
    $orders_count_row = mysqli_fetch_assoc($orders_count_result);
    $orders_count = (int)$orders_count_row['cnt'];
}

$name  = $user['name'] ?? $user['login'];
$email = $user['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль — Game store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header__inner">
            <div class="brand">
                <h1>Game store</h1>
                <h2>Все игры по выгодной цене</h2>
            </div>
            <nav class="nav">
                <a href="index.php">Главная</a>
                <a href="history.php">История покупок</a>
                <a href="admin_panel.php">Админ панель</a>
                <a href="profile.php" class="is-active">Профиль</a>
            </nav>
        </div>
    </header>

    <main>
        <h2 class="section-title">Профиль</h2>

        <div class="profile">
            <aside class="profile-card">
                <h3><?php echo htmlspecialchars($name); ?></h3>
                <p class="profile-email"><?php echo htmlspecialchars($email); ?></p>

                <ul class="profile-meta">
                    <li>
                        <span>Куплено игр</span>
                        <b><?php echo $orders_count; ?></b>
                    </li>
                </ul>

                <form action="logout.php" method="post">
                    <button class="buy" type="submit">Выйти из аккаунта</button>
                </form>
            </aside>

            <div class="profile-main">
                <section class="form-card">
                    <h3 class="card-title">Личные данные</h3>
                    <form action="profile.php" method="post">
                        <div class="form-grid">
                            <label class="field">
                                Имя
                                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                            </label>
                            <label class="field">
                                Email
                                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </label>
                        </div>
                        <div class="form-actions" style="margin-top: 18px;">
                            <button class="buy" type="submit">Сохранить</button>
                            <button class="btn-ghost" type="reset">Отмена</button>
                        </div>
                    </form>
                </section>

                <section class="form-card">
                    <h3 class="card-title">Смена пароля</h3>
                    <form action="profile.php" method="post">
                        <div class="form-grid">
                            <label class="field">
                                Текущий пароль
                                <input type="password" name="current_password" required>
                            </label>
                            <label class="field">
                                Новый пароль
                                <input type="password" name="new_password" required>
                            </label>
                            <label class="field">
                                Подтвердите пароль
                                <input type="password" name="confirm_password" required>
                            </label>
                        </div>
                        <div class="form-actions" style="margin-top: 18px;">
                            <button class="buy" type="submit">Изменить пароль</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer__inner">
            <span>© 2026 Game store. Все права защищены.</span>
            <div class="footer__links">
                <a href="#">О нас</a>
                <a href="#">Поддержка</a>
                <a href="#">Контакты</a>
            </div>
        </div>
    </footer>
</body>
</html>