<?php

header('Content-Type: text/html; charset=UTF-8');

include("db_data.php");


if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $messages = array();
    if(!empty($_COOKIE['save'])){
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        $messages[] = 'Thanks, results are saved.';

        if(!empty($_COOKIE['pass'])){
            $messages[] = sprintf('You can <a href="login.php">enter</a> with login <strong>%s</strong>
            and pass <strong>%s</strong> for change date.', 
            strip_tags($_COOKIE['login']),
            strip_tags($_COOKIE['pass']));
        }
    }

    $errors = array();
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['tel'] = !empty($_COOKIE['tel_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['bday'] = !empty($_COOKIE['bday_error']);
    $errors['sex'] = !empty($_COOKIE['sex_error']);
    $errors['pl'] = !empty($_COOKIE['prog_lang_error']);
    $errors['acception'] = !empty($_COOKIE['acception_error']);

    if($errors['name']){
        setcookie('name_error', '', 100000);
        $messages[] = '<div class="error">Fill the name.</div>';
    }
    if($errors['tel']){
        setcookie('tel_error', '', 100000);
        $messages[] = '<div class="error">Fill the phone.</div>';
    }
    if($errors['email']){
        setcookie('email_error', '', 100000);
        $messages[] = '<div class="error">Fill the email.</div>';
    }
    if($errors['bday']){
        setcookie('bday_error', '', 100000);
        $messages[] = '<div class="error">Fill the bday.</div>';
    }
    if($errors['sex']){
        setcookie('sex_error', '', 100000);
        $messages[] = '<div class="error">Fill the sex.</div>';
    }
    if($errors['pl']){
        setcookie('prog_lang_error', '', 100000);
        $messages[] = '<div class="error">Choose the programming lang.</div>';
    }
    if($errors['acception']){
        setcookie('acception_error', '', 100000);
        $messages[] = '<div class="error">Fill the acception.</div>';
    }

    $values = array();
    $values['name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
    $values['tel'] = empty($_COOKIE['tel_value']) ? '' : strip_tags($_COOKIE['tel_value']);
    $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
    $values['bday'] = empty($_COOKIE['bday_value']) ? '' : strip_tags($_COOKIE['bday_value']);
    $values['sex'] = empty($_COOKIE['sex_value']) ? '' : strip_tags($_COOKIE['sex_value']);
    $values['pl'] = empty($_COOKIE['prog_lang_value']) ? [] : explode(';', strip_tags($_COOKIE['prog_lang_value']));
    $values['bio'] = empty($_COOKIE['bio']) ? '' : strip_tags($_COOKIE['bio']);
    $values['acception'] = empty($_COOKIE['acception_value']) ? '' : strip_tags($_COOKIE['acception_value']);

    if(
        !empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])
    ){

        $db = new PDO('mysql:host=localhost;dbname=u67353', $user, $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        $stmt = $db->prepare("SELECT id, name, tel, email, bday, sex, bio FROM application WHERE id_client = :id_client;");
        $stmt->bindParam(":id_client", $_SESSION['id_client']);
        $stmt->execute();
        $submission = $stmt->fetchAll()[0];
        $values['name'] = strip_tags($submission['name']);
        $values['tel'] = strip_tags($submission['tel']);
        $values['email'] = strip_tags($submission['email']);
        $values['bday'] = strip_tags($submission['bday']);
        $values['sex'] = $submission['sex'] == 1 ? "on" : "off";
        $values['bio'] = strip_tags($submission['bio']);
        $parent_id = strip_tags($submission['id']);

        $stmt = $db->prepare("SELECT pl from favoritelang WHERE parent_id = :parent_id");
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->execute();
        $values['pl'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $messages[] = sprintf('<div>Entered with login %s, user id %d</div>', $_SESSION['login'], $_SESSION['id_client']);
    }

    include('./form.php');
}

else{
    $errors = FALSE;
    if(empty($_POST['name']) || strlen($_POST['name'])>150 || !preg_match("/^[\p{Cyrillic}a-zA-Z-' ]*$/u", $_POST['name'] )){
        setcookie('name_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
    }

    if(empty($_POST['tel']) || !preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $_POST['tel'])){
        setcookie('tel_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('tel_value', $_POST['tel'], time() + 30 * 24 * 60 * 60);
    }

    if(empty($_POST['email']) || !preg_match('/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/', $_POST['email'])){
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
    }

    if(empty($_POST['bday'])){
        setcookie('bday_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('bday_value', $_POST['bday'], time() + 30 * 24 * 60 * 60);
    }

    if(empty($_POST['sex'])){
        setcookie('sex_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('sex_value', $_POST['sex'], time() + 30 * 24 * 60 * 60);
    }

    if(empty($_POST['pl'])){
        setcookie('prog_lang_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('prog_lang_value', implode(';', $_POST['pl']), time() + 30 * 24 * 60 * 60);
    }

    if(empty($_POST['acception'])){
        setcookie('acception_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('acception_value', $_POST['acception'], time() + 30 * 24 * 60 * 60);
    }

    setcookie('bio', $_POST['bio'], time() + 30 * 24 * 60 * 60);


    if($errors){
        header('Location: ./index.php');
        exit();
    }
    else{
        setcookie('name_error', '', 100000);
        setcookie('tel_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('bday_error', '', 100000);
        setcookie('sex_error', '', 100000);
        setcookie('prog_lang_error', '', 100000);
        setcookie('acception_error', '', 100000);
    }
    
    if(!empty($_COOKIE[session_name()]) && 
    session_start() && !empty($_SESSION['login'])){
        $db = new PDO('mysql:host=localhost;dbname=u67353', $user, $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        $stmt = $db->prepare("UPDATE application 
        SET name = :name, tel = :tel, email = :email, bday = :bday, sex = :sex, bio = :bio
        WHERE id_client = :id_client;");
        $stmt->bindParam(':id_client', $_SESSION['id_client']);
        $name = $_POST['name'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $bday = $_POST['bday'];
        $sex = $_POST['sex'] == "on" ? "1" : "0";
        $bio = $_POST['bio'];
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':tel', $tel);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':bday', $bday);
        $stmt->bindParam(':sex', $sex);
        $stmt->bindParam(':bio', $bio);
        $stmt->execute();

        $stmt = $db->prepare("SELECT id from application WHERE id_client = :id_client;");
        $stmt->bindParam(":id_client", $_SESSION['id_client']);
        $stmt->execute();
        $parent_id = $stmt->fetchAll(PDO::FETCH_COLUMN)[0];

        $stmt = $db->prepare("DELETE FROM favoritelang WHERE parent_id = :parent_id;");
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->execute();

        $stmt = $db->prepare("INSERT INTO favoritelang (parent_id, pl) VALUES(:parent_id, :pl)");
        $stmt->bindParam(':parent_id',$parent_id);
        foreach ($_POST['pl'] as $pl){
            $stmt->bindParam(':pl', $pl);
            $stmt->execute();
        }
        unset($pl);
    }
    else{
     
        $login = rand(100,9000);
        $password = rand(100,9000);
        $pass_hash = md5($password);
        setcookie('login', $login);
        setcookie('pass', $password);
        $db = new PDO('mysql:host=localhost;dbname=u67353', $user, $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $stmt = $db->prepare("INSERT INTO client (login, pass) VALUES(:login, :pass_hash)");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':pass_hash', $pass_hash);
        $stmt->execute();

        $id = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO application (id_client, name, tel, email, bday, sex, bio) VALUES (:id, :name, :tel, :email, :bday, :sex, :bio)");
        $stmt->bindParam(':id', $id);
        $name = $_POST['name'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $bday = $_POST['bday'];
        $sex = $_POST['sex'] == "on" ? "1" : "0";
        $bio = $_POST['bio'];
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':tel', $tel);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':bday', $bday);
        $stmt->bindParam(':sex', $sex);
        $stmt->bindParam(':bio', $bio);
        $stmt->execute();

        $parent_id = $db->lastInsertId();
        $stmt = $db->prepare("INSERT INTO favoritelang (parent_id, pl) VALUES(:parent_id, :pl)");
        $stmt->bindParam(':parent_id',$parent_id);
        foreach ($_POST['pl'] as $pl){
            $stmt->bindParam(':pl', $pl);
            $stmt->execute();
        }
    }
   
    setcookie('save', '1');

    header('Location: ./index.php');

}
