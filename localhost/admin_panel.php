<?php require_once 'db.php';
    $message = '';
    if(isset($_GET['delete'] )){
        $delete_id = (int)$_GET['delete'];
        $query = "SELECT image FROM games WHERE id = $delete_id";
        $res = mysqli_query($con, $query);
        if($res && mysqli_num_rows($res) > 0){
            $game = mysqli_fetch_assoc($res);
            $img_path = $game['image'];
            $delete_query = "DELETE FROM games WHERE id = $delete_id";
            if(mysqli_query($con, $delete_query)){
                if(file_exists($img_path)){
                    unlink($img_path);
                    $message='Товар удалён';
                }
                else{
                    $message='Ошибка удаления' . mysqli_error($con);
                }
            }
            else{
                $message='Товар не найден';
            }
        }
    }
    if($_SERVER['REQUEST_METHOD']==='POST'){
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $image_path = "";
        if(isset($_FILES['image']) && $_FILES['image']['error']===UPLOAD_ERR_OK){
            $upload_dir = 'media/';
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
                $image_path = $target_file;
            }
        }
        if($image_path && !empty($name) && $price >= 0){
            $name = mysqli_real_escape_string($con, $name);
            $description = mysqli_real_escape_string($con, $description);
            $image_path = mysqli_real_escape_string($con, $image_path);
            $query = "INSERT INTO games(name, description, price, image) 
            VALUES ('$name', '$description', '$price', '$image_path')";
            mysqli_query($con, $query);
        }
        
    }
    $games_query = "SELECT * FROM games ORDER BY id DESC";
    $games_result = mysqli_query($con, $games_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    main{
        text-align: center;
    }

    main h2{
        font-style: oblique;
        font-size: 40px;
    }

    /* ==================== ПЛИТОЧНАЯ ФОРМА ==================== */
    .tile-form{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        max-width: 1100px;
        margin: 30px auto;
    }

    .tile{
        background: #1e1f29;
        border: 1px solid #2e3040;
        border-radius: 14px;
        padding: 20px 22px;
        text-align: left;
        color: #fff;
        box-shadow: 0 6px 18px rgba(0,0,0,0.35);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }

    .tile:hover{
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.5);
        border-color: #4a7cff;
    }

    .tile label{
        display: block;
        font-weight: 600;
        font-size: 15px;
        letter-spacing: .4px;
        margin-bottom: 10px;
        color: #cfd4ff;
    }

    .tile input[type="text"],
    .tile input[type="number"],
    .tile textarea{
        width: 100%;
        background: #14151c;
        border: 1px solid #2e3040;
        border-radius: 8px;
        padding: 10px 12px;
        color: #fff;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .tile input:focus,
    .tile textarea:focus{
        border-color: #4a7cff;
        box-shadow: 0 0 0 3px rgba(74,124,255,0.2);
    }

    .tile textarea{
        resize: vertical;
        min-height: 110px;
    }

    .tile--name{ width: 320px; }
    .tile--price{ width: 200px; }
    .tile--image{ width: 320px; }
    .tile--desc{ width: 100%; max-width: 860px; }

    .tile--submit{
        width: 100%;
        max-width: 860px;
        align-items: center;
        background: linear-gradient(135deg, #4a7cff, #7b5bff);
        border: none;
        cursor: pointer;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 18px;
        text-transform: uppercase;
        transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
    }

    .tile--submit:hover{
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 12px 26px rgba(74,124,255,0.45);
        filter: brightness(1.08);
    }

    .tile--submit:active{
        transform: translateY(0) scale(1);
    }

    .tile input[type="file"]{
        width: 100%;
        background: #14151c;
        border: 1px dashed #4a7cff;
        border-radius: 8px;
        padding: 10px;
        color: #aaa;
        cursor: pointer;
        font-size: 13px;
        box-sizing: border-box;
    }

    .tile input[type="file"]::file-selector-button{
        background: #4a7cff;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        margin-right: 10px;
        cursor: pointer;
        font-weight: 600;
    }

    .form-message{
        font-weight: 500;
        color: #7bffb0;
        min-height: 22px;
    }

    /* ==================== ТАБЛИЦА ==================== */
    .games-table{
        margin: 30px auto;
        border-collapse: separate;
        border-spacing: 0;
        width: 90%;
        max-width: 1100px;
        background: #1e1f29;
        border: 1px solid #2e3040;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0,0,0,0.35);
        color: #e6e6f0;
        font-size: 14px;
    }

    .games-table thead{
        background: linear-gradient(135deg, #2a2d3d, #1a1c26);
    }

    .games-table th{
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #9aa4ff;
        border-bottom: 1px solid #2e3040;
        white-space: nowrap;
    }

    .games-table td{
        padding: 12px 16px;
        border-bottom: 1px solid #262836;
        vertical-align: middle;
        text-align: left;
        color: #d6d8e6;
    }

    .games-table tbody tr{
        transition: background .2s ease, transform .2s ease;
    }

    .games-table tbody tr:nth-child(even){
        background: rgba(255,255,255,0.02);
    }

    .games-table tbody tr:hover{
        background: rgba(74,124,255,0.08);
    }

    .games-table tbody tr:last-child td{
        border-bottom: none;
    }

    .games-table img{
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #2e3040;
        display: block;
        transition: transform .25s ease, border-color .25s ease;
    }

    .games-table img:hover{
        transform: scale(1.15);
        border-color: #4a7cff;
        box-shadow: 0 6px 16px rgba(74,124,255,0.4);
    }

    .games-table .id-cell{
        color: #8a90b8;
        font-weight: 700;
    }

    .games-table .name-cell{
        font-weight: 600;
        color: #fff;
    }

    .games-table .desc-cell{
        max-width: 320px;
        color: #a9adc4;
        line-height: 1.4;
    }

    .games-table .price-cell{
        color: #7bffb0;
        font-weight: 700;
        white-space: nowrap;
    }

    .games-table .delete-link{
        display: inline-block;
        padding: 6px 14px;
        border-radius: 8px;
        background: rgba(255, 77, 109, 0.12);
        border: 1px solid rgba(255, 77, 109, 0.4);
        color: #ff6b8a;
        font-weight: 600;
        text-decoration: none;
        font-size: 13px;
        transition: background .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .games-table .delete-link:hover{
        background: #ff4d6d;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(255,77,109,0.4);
    }

    .empty-message{
        margin: 40px auto;
        color: #8a90b8;
        font-style: italic;
        font-size: 16px;
    }

    @media (max-width: 720px){
        .tile--name,
        .tile--price,
        .tile--image,
        .tile--desc,
        .tile--submit{
            width: 100%;
            max-width: 100%;
        }

        .games-table{
            width: 100%;
            font-size: 12px;
        }

        .games-table th,
        .games-table td{
            padding: 8px 10px;
        }

        .games-table .desc-cell{
            display: none;
        }
    }
</style>
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
                <a href="#" class="is-active">Админ панель</a>
                <a href="profile.php">Профиль</a>
            </nav>
        </div>
    </header>
    <main>
        <h2>Панель управления</h2>
        <h3>Добавить новую игру</h3>
        <h4 class="form-message"><?php echo $message;?></h4>

        <form method="post" enctype="multipart/form-data" class="tile-form">

            <div class="tile tile--name">
                <label>Название игры</label>
                <input type="text" name="name" placeholder="" required>
            </div>

            <div class="tile tile--price">
                <label>Цена</label>
                <input type="number" step="0.01" name="price" placeholder="0.00" required>
            </div>

            <div class="tile tile--image">
                <label>Изображение</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <div class="tile tile--desc">
                <label>Описание</label>
                <textarea name="description" placeholder="Краткое описание игры..." required rows="5"></textarea>
            </div>

            <button type="submit" class="tile tile--submit">
                Добавить игру
            </button>

        </form>

        <h3>Список всех товаров</h3>
        <?php if(mysqli_num_rows($games_result)>0):?>
        <table class="games-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Изображение</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php while($game = mysqli_fetch_assoc($games_result)):?>
                <tr>
                    <td class="id-cell">#<?php echo $game['id'];?></td>
                    <td>
                        <img src="<?php echo $game['image'];?>" alt="<?php echo $game['name'];?>">
                    </td>
                    <td class="name-cell"><?php echo $game['name'];?></td>
                    <td class="desc-cell"><?php echo $game['description'];?></td>
                    <td class="price-cell"><?php echo number_format($game['price'], 2, '.', ' ');?> ₽</td>
                    <td>
                        <a href="?delete=<?php echo $game['id'];?>" 
                           class="delete-link"
                           onclick="return confirm('Вы уверены, что хотите удалить игру?')">
                           Удалить
                        </a>
                    </td>
                </tr>
                <?php endwhile;?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="empty-message">📭 Нет товаров</p>
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