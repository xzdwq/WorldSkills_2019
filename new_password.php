<?php
// Изменение пароля пользователем
$host		= "host=server.skills.st";
$port		= "port=5432";
$dbname		= "dbname=postgres";
$password	= "password=1";

$login 			= $_POST['login_enter'];
$old_pass		= $_POST['old_pass'];
$new_pass		= $_POST['new_pass'];
$new_pass_2		= $_POST['new_pass_2'];
$type_admission	= $_POST['type_admission'];

if ($new_pass!=$new_pass_2) {die(json_encode('2'));}

if ($type_admission==1) {$username = "user=testuser1";}
if ($type_admission==2) {$username = "user=testuser2";}

$db = @pg_connect("$host $port $dbname $username $password");     //Подключаемся к БД

// Проверка: действительно ли ты тот за кого себя выдаешь? :)
$sql = "SELECT COUNT(*) si FROM testschema.admission WHERE ilogin='$login' and ipassword='$old_pass'";
$query = @pg_query($db,$sql);
if (!$query) {die(json_encode('0'));}		// В случае ошибки в запросе выкидываем ошибку
$res   = @pg_fetch_all($query);
$count = $res[0]['si'];
if ($count==0) {die(json_encode('3'));}

// Собственно само изменение пароля
$sql = "UPDATE testschema.admission SET ipassword='$new_pass' WHERE ilogin='$login' and ipassword='$old_pass'";
$res = @pg_query($db,$sql);

@pg_close($db);      //Закрываем соединение с БД

// В случае ошибки в запросе выкидываем ошибку
if (!$res) {die(json_encode('0'));}
else {die(json_encode('1'));}
?>