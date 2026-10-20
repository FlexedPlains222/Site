<?php 
    require_once 'db.php';
    $query = "SELECT * FROM games ORDER BY id DESC";
    $result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин игр</title>
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
                <a href="index.php" class="is-active">Главная</a>
                <a href="history.php">История покупок</a>
                <a href="admin_panel.php">Админ панель</a>
                <a href="profile.php">Профиль</a>
            </nav>
        </div>
    </header>

    <main>
        <h2 class="section-title">Каталог игр</h2>

        <div class="products"> 
            <?php if(mysqli_num_rows($result) > 0):?>
                <?php while($game = mysqli_fetch_assoc($result)):?>
                    <a href="product.php?id=<?php echo $game['id'];?>">
                        <article class="product-card">
                            <img src="<?php echo $game['image'];?>" alt="<?php echo $game['name'];?>">
                            <div class="product-card__body">
                                <h3 class="name"><?php echo $game['name'];?></h3>
                                <p class="price"><?php echo $game['price'];?> ₽</p>
                                <button class="buy">Купить</button>
                            </div>
                        </article>
                    </a>
                <?php endwhile;?>
            <?php else:?>
            <p>Нет товаров!</p>
            <?php endif; ?>
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