<?php
// Изменение пароля пользователю из под администратора
$host		= "host=server.skills.st";
$port		= "port=5432";
$dbname		= "dbname=postgres";
$password	= "password=1";

$user_login         = $_POST['user_login'];
$pass_user_new      = $_POST['pass_user_new'];
$pass_user_new_2    = $_POST['pass_user_new_2'];
$type_admission     = $_POST['type_admission'];

if ($pass_user_new!=$pass_user_new_2) {die(json_encode('2'));}

if ($type_admission==1) {$username = "user=testuser1";}
if ($type_admission==2) {$username = "user=testuser2";}

$db = @pg_connect("$host $port $dbname $username $password");     //Подключаемся к БД

// Проверка: действительно ли ты тот за кого себя выдаешь? :)
$sql = "SELECT COUNT(*) si FROM testschema.admission WHERE ilogin='$user_login'";
$query = @pg_query($db,$sql);
if (!$query) {die(json_encode('0'));}		// В случае ошибки в запросе выкидываем ошибку
$res   = @pg_fetch_all($query);
$count = $res[0]['si'];
if ($count==0) {die(json_encode('3'));}

// Собственно само изменение пароля
$sql = "UPDATE testschema.admission SET ipassword='$pass_user_new' WHERE ilogin='$user_login'";
$res = @pg_query($db,$sql);

@pg_close($db);      //Закрываем соединение с БД

// В случае ошибки в запросе выкидываем ошибку
if (!$res) {die(json_encode('0'));}
else {die(json_encode('1'));}
?>