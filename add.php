<?php
// Добавление нового извещения (квитанции)
$host		= "host=server.skills.st";
$port		= "port=5432";
$dbname		= "dbname=postgres";
$username	= "user=testuser1";
$password	= "password=1";

$id_admission	= $_POST['id_admission'];
$type_admission	= $_POST['type_admission'];
$gaz			= $_POST['gaz'];
$hvs_1			= $_POST['hvs_1'];
$hvs_2			= $_POST['hvs_2'];
$electro		= $_POST['electro'];

$db = @pg_connect("$host $port $dbname $username $password");     //Подключаемся к БД

// Поиск идентификатора помещения (поиск квартиры)
$sql = "SELECT id_room FROM testschema.room a WHERE id_admission=$id_admission";
$query_d = @pg_query($db,$sql);
$res_d = @pg_fetch_all($query_d);
$id_room = $res_d[0]['id_room'];

// Запрос добавления новой квитанции/извешения
$sql = "INSERT INTO testschema.status(id_room,mm24,mode_status,summ,x364,state_gaz,state_hvs1,state_hvs2,state_electric)
		VALUES ($id_room,now(),$type_admission,0,now(),$gaz,$hvs_1,$hvs_2,$electro)";
$res = @pg_query($db,$sql);

@pg_close($db);      //Закрываем соединение с БД

if (!$res) {$array = array(array('success'=>true));}
else {$array = array(array('failure'=>false));}

echo json_encode($array);
?>