<?php 
    require_once 'db.php';
    if ($_SERVER['REQUEST_METHOD']== 'POST'){
        $login = $_POST['login'];
        $password = $_POST['pass'];
        $repeat_pass = $_POST['repeat_pass'];
        $email = $_POST['email'];
        if(empty($login) || empty($password) || empty($repeat_pass) || empty($email)){
            echo 'Заполните все поля';
        }
        else{
            if($password != $repeat_pass){
                echo 'пароли не совпадают';
            }
            else{
                $sql = "INSERT INTO users(login, password, email) VALUES('$login', '$password', '$email')";
                if($con->query($sql)==true){
                    $_SESSION['user_id'] = mysqli_insert_id($con);
                    $_SESSION['login'] = $login;
                    header('Location: index.php');
                    exit;
                }
                else{
                    echo 'ошибка регистрации';
                }
            }
        }
    }
?>