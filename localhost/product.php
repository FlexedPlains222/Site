<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die('Не указан id');
}

$query = "SELECT * FROM games WHERE id = $id";
$result = mysqli_query($con, $query);
$game = mysqli_fetch_assoc($result);

if (!$game) {
    die('Игра не найдена');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy'])) {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $gameName  = htmlspecialchars($game['name']);
    $gamePrice = (float)$game['price'];
    $userId    = (int)$_SESSION['user_id'];
    $insert = "INSERT INTO orders (game_id, game_name, price, user_id) VALUES
    ($id, '$gameName', $gamePrice, $userId)";
    if (mysqli_query($con, $insert)) {
        header('Location: history.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['name']); ?> — Game store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Модальное окно ===== */
        .model{
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            z-index: 1000;
            opacity: 1;
            visibility: visible;
            transition: opacity .25s ease, visibility .25s ease;
        }

        .model[hidden]{
            display: flex; /* переопределяем атрибут hidden, управляем через класс */
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .model__dialog{
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px 26px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55);
            display: flex;
            flex-direction: column;
            gap: 18px;
            transform: translateY(0) scale(1);
            transition: transform .25s ease;
        }

        .model[hidden] .model__dialog{
            transform: translateY(12px) scale(0.97);
        }

        .model__title{
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            text-align: center;
        }

        .model__game{
            font-size: 15px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.5;
        }

        .model__game b{
            color: var(--accent);
            font-size: 18px;
            display: block;
            margin-top: 6px;
        }

        .model__payment{
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-top: 1px solid var(--border);
            padding-top: 16px;
        }

        .model__payment p{
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .model__payment label{
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #fff;
            transition: border-color .2s, background .2s;
        }

        .model__payment label:hover{
            border-color: var(--accent);
        }

        .model__payment input[type="radio"]{
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .model__actions{
            display: flex;
            gap: 12px;
            margin-top: 6px;
        }

        .model__actions .buy{
            flex: 1;
            padding: 12px 16px;
            font-size: 15px;
        }

        .model__close{
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 18px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: background .2s, color .2s, border-color .2s;
        }

        .model__close:hover{
            background: var(--bg);
            color: #fff;
            border-color: var(--accent);
        }

        .model__close:active{
            transform: scale(.97);
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

    <main>
        <div class="breadcrumbs">
            <a href="index.php">Главная</a> / <?php echo htmlspecialchars($game['name']); ?>
        </div>

        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>">
            </div>

            <div class="product-info">
                <h2><?php echo htmlspecialchars($game['name']); ?></h2>
                <div class="desc">
                    <?php echo $game['description']; ?>
                </div>
            </div>

            <div class="product-buy">
                <h3 class="price"><?php echo number_format($game['price'], 0, '', ' '); ?> ₽</h3>
                <button type="button" class="buy" id="open-buy-modal">Купить</button>
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

    <!-- ===== Модальное окно подтверждения покупки ===== -->
    <div class="model" id="buy-modal" hidden>
        <div class="model__dialog">
            <h3 class="model__title">Подтверждение покупки</h3>

            <p class="model__game">
                <?php echo htmlspecialchars($game['name']); ?>
                <b><?php echo number_format($game['price'], 2, '.', ' '); ?> ₽</b>
            </p>

            <form method="POST" id="buy-form">
                <div class="model__payment">
                    <p>Способ оплаты</p>
                    <label>
                        <input type="radio" value="sbp" name="payment" checked> СБП
                    </label>
                    <label>
                        <input type="radio" value="card" name="payment"> Банковской картой
                    </label>
                    <label>
                        <input type="radio" value="store" name="payment"> Store балансом
                    </label>
                </div>

                <div class="model__actions">
                    <button type="button" class="model__close" id="close-buy-modal">Отмена</button>
                    <button type="submit" class="buy" name="buy">Купить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal    = document.getElementById('buy-modal');
        const openBtn  = document.getElementById('open-buy-modal');
        const closeBtn = document.getElementById('close-buy-modal');
        const buyForm  = document.getElementById('buy-form');

        // По умолчанию — скрыта
        modal.hidden = true;

        // Открыть модалку
        openBtn.addEventListener('click', () => {
            modal.hidden = false;
        });

        // Закрыть по кнопке "Отмена"
        closeBtn.addEventListener('click', () => {
            modal.hidden = true;
        });

        // Закрыть по клику на фон (вне диалога)
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.hidden = true;
            }
        });

        // Закрыть по Esc
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.hidden) {
                modal.hidden = true;
            }
        });

        // При отправке формы (нажали "Купить" в модалке) — скрываем модалку
        buyForm.addEventListener('submit', () => {
            modal.hidden = true;
        });
    </script>
</body>
</html>