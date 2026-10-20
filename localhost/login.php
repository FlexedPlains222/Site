<?php 
    require_once 'db.php';
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['pass'] ?? '';
        if (empty($login) || empty($password)) {
            $error = 'Заполните все поля';
        } else {
            $login = mysqli_real_escape_string($con, $login);
            $password = mysqli_real_escape_string($con, $password);
            $sql = "SELECT * FROM users WHERE login = '$login' AND password = '$password'";
            $result = $con->query($sql);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['login'] = $row['login'];
                header('Location: history.php');
                exit;
            } else {
                $error = 'Неверный логин/пароль';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход и регистрация — Game store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-page {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .auth-card {
            background: #1e1f29;
            border: 1px solid #2e3040;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
        }
        .auth-card h3 {
            margin: 0 0 20px;
            color: #fff;
            font-size: 22px;
        }
        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
            color: #cfd4ff;
            font-weight: 600;
            font-size: 14px;
        }
        .field input {
            background: #14151c;
            border: 1px solid #2e3040;
            border-radius: 8px;
            padding: 12px 14px;
            color: #fff;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .field input:focus {
            border-color: #4a7cff;
            box-shadow: 0 0 0 3px rgba(74, 124, 255, 0.2);
        }
        .btn {
            background: linear-gradient(135deg, #4a7cff, #7b5bff);
            border: none;
            border-radius: 10px;
            padding: 14px 20px;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(74, 124, 255, 0.4);
            filter: brightness(1.08);
        }
        .btn:active {
            transform: translateY(0);
        }
        .alert {
            background: rgba(255, 77, 109, 0.12);
            border: 1px solid rgba(255, 77, 109, 0.4);
            color: #ff6b8a;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        @media (max-width: 720px) {
            .auth-grid { grid-template-columns: 1fr; }
        }
    </style>
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
                <a href="profile.php">Профиль</a>
            </nav>
        </div>
    </header>

    <main class="auth-page">
        <h2 class="section-title">Вход и регистрация</h2>

        <?php if ($error): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="auth-grid">
            <!-- Вход -->
            <section class="auth-card">
                <h3>Вход</h3>
                <form action="login.php" method="POST">
                    <label class="field">
                        Логин
                        <input type="text" name="login" placeholder="Введите логин" required>
                    </label>
                    <label class="field">
                        Пароль
                        <input type="password" name="pass" placeholder="Введите пароль" required>
                    </label>
                    <button type="submit" class="btn">Войти</button>
                </form>
            </section>

            <!-- Регистрация -->
            <section class="auth-card">
                <h3>Регистрация</h3>
                <form action="register.php" method="POST">
                    <label class="field">
                        Логин
                        <input type="text" name="login" placeholder="Придумайте логин" required>
                    </label>
                    <label class="field">
                        Пароль
                        <input type="password" name="pass" placeholder="Придумайте пароль" required>
                    </label>
                    <label class="field">
                        Повтор пароля
                        <input type="password" name="repeat_pass" placeholder="Повторите пароль" required>
                    </label>
                    <label class="field">
                        Email
                        <input type="email" name="email" placeholder="you@example.com" required>
                    </label>
                    <button type="submit" class="btn">Зарегистрироваться</button>
                </form>
            </section>
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