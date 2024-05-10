<?php

header('Content-Type: text/html; charset=UTF-8');

$session_started = false;
if (isset($_COOKIE[session_name()]) && $_COOKIE[session_name()] && session_start()) {
  $session_started = true;
  if (!empty($_SESSION['login'])) {
    header('Location: ./');
    exit();
  }
}

include("db_data.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include("./login_page.php");
}
else {
    $user_login = empty($_POST['login']) ? '' : $_POST['login'];
    $user_pasword = empty($_POST['password']) ? '' : $_POST['password'];
    $user_password_hash = md5($user_pasword);

    $db = new PDO('mysql:host=localhost;dbname=u67353', $user, $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $db->prepare("SELECT id FROM client WHERE login = :user_login AND pass = :user_password;");
    $stmt->bindParam(":user_login", $user_login);
    $stmt->bindParam(":user_password", $user_password_hash);
    $stmt->execute();
    $results = $stmt->fetchAll();
    $id_client = count($results) == 1 ? $results[0]['id'] : -1;

    if ($id_client == -1) {
        header("Location: ./login.php");
        exit();
    }

    if (!$session_started) {
        session_start();
    }
    $_SESSION['login'] = $user_login;
    $_SESSION['id_client'] = $id_client;

    // Делаем перенаправление.
    header('Location: ./');
}
    