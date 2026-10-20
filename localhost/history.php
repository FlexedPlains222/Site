<?php 
    require_once 'db.php';
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $bought = isset($_GET['bought']);
    $user_id = (int)$_SESSION['user_id'];
    $query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC";
    $orders = [];
    $total = 0;
    $result = mysqli_query($con, $query);
    if ($result) {
        while ($order = mysqli_fetch_assoc($result)) {
            $orders[] = $order;
            $total += (float)$order['price'];
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История покупок — Game store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .history-page {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .history-page h2 {
            margin-bottom: 8px;
        }
        .user-info {
            color: #8a90b8;
            margin-bottom: 24px;
        }
        .summary {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 24px 0;
        }
        .summary-card {
            background: #1e1f29;
            border: 1px solid #2e3040;
            border-radius: 14px;
            padding: 20px 24px;
            min-width: 200px;
            flex: 1;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
        }
        .summary-card span {
            display: block;
            color: #8a90b8;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .summary-card b {
            font-size: 28px;
            color: #fff;
        }
        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #1e1f29;
            border: 1px solid #2e3040;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
        }
        .orders-table th {
            background: linear-gradient(135deg, #2a2d3d, #1a1c26);
            color: #9aa4ff;
            padding: 14px 16px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .orders-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #262836;
            color: #d6d8e6;
        }
        .orders-table tbody tr:last-child td {
            border-bottom: none;
        }
        .orders-table tbody tr:hover {
            background: rgba(74, 124, 255, 0.08);
        }
        .orders-table .price-cell {
            color: #7bffb0;
            font-weight: 700;
            white-space: nowrap;
        }
        .empty {
            text-align: center;
            color: #8a90b8;
            font-style: italic;
            padding: 40px;
            background: #1e1f29;
            border: 1px solid #2e3040;
            border-radius: 14px;
        }
        .alert-success {
            background: rgba(123, 255, 176, 0.12);
            border: 1px solid rgba(123, 255, 176, 0.4);
            color: #7bffb0;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        @media (max-width: 720px) {
            .orders-table { font-size: 12px; }
            .orders-table th,
            .orders-table td { padding: 8px 10px; }
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
                <a href="history.php" class="is-active">История покупок</a>
                <a href="admin_panel.php">Админ панель</a>
                <a href="profile.php">Профиль</a>
            </nav>
        </div>
    </header>

    <main class="history-page">
        <h2 class="section-title">Мои заказы</h2>
        <p class="user-info">Логин: <?php echo htmlspecialchars($_SESSION['login']); ?></p>

        <?php if ($bought): ?>
            <div class="alert-success">Заказ оформлен</div>
        <?php endif; ?>

        <?php if (!$result): ?>
            <div class="empty">Не удалось загрузить заказы</div>
        <?php elseif (count($orders) == 0): ?>
            <div class="empty">📭 Заказов нет</div>
        <?php else: ?>
            <div class="summary">
                <div class="summary-card">
                    <span>Всего заказов</span>
                    <b><?php echo count($orders); ?></b>
                </div>
                <div class="summary-card">
                    <span>Сумма</span>
                    <b><?php echo number_format($total, 2, '.', ' '); ?> ₽</b>
                </div>
            </div>

            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата</th>
                        <th>Игра</th>
                        <th>Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo (int)$order['id']; ?></td>
                            <td><?php echo date('d.m.y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($order['game_name']); ?></td>
                            <td class="price-cell"><?php echo number_format($order['price'], 2, '.', ' '); ?> ₽</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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