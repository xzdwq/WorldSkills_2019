<?php
// Проверка доступа: аутентификация и авторизация пользователя
$host		= "host=server.skills.st";
$port		= "port=5432";
$dbname		= "dbname=postgres";
$username	= "user=testuser1";
$password	= "password=1";

$login	= $_POST['login'];
$pass	= $_POST['password'];

$db = @pg_connect("$host $port $dbname $username $password");     //Подключаемся к БД
$query = @pg_query($db,"SELECT id_admission, type_admission FROM testschema.admission WHERE ilogin='$login' AND ipassword='$pass'");  //Запрос на проверку в БД отправленных пользователем Логина и Пароля

if(@pg_num_rows($query)!=0){                                    //Если пользователь с данным паролем существует, то передаем параметры и даем ответ
    $res_1=@pg_fetch_all($query);
	// Наделяем пользователя правами, админ ты или простой челик? :)
    $id_admisson = $res_1[0]['id_admission'];
    $type_admisson = $res_1[0]['type_admission'];

	// Подтягиваем ФИО и адрес квартиры для отображания на главной форме
    $query_d = @pg_query($db,"SELECT fio, address FROM testschema.room WHERE id_admission=$id_admisson");
    $res_d = @pg_fetch_all($query_d);
    $fio = $res_d[0]['fio'];
    $address = $res_d[0]['address'];

	@pg_close($db);      //Закрываем соединение с БД

    die('{"success":true,"message":"Добро пожаловать, '.$login.'","id_admission":'.$id_admisson.',"type_admission":'.$type_admisson.',"fio":"'.$fio.'","address":"'.$address.'"}');
}
else{die('{"success":false,"message":"Неправильный логин или пароль"}');}  //Иначе выдаем ошибку
?>