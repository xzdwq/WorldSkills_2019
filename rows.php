<?php
// Подгрузка квитанций пользователя
$host		= "host=server.skills.st";
$port		= "port=5432";
$dbname		= "dbname=postgres";
$password	= "password=1";

$id_admission 	= $_POST['id_admission'];
$type_admission = $_POST['type_admission'];

// Определение админ ли ты или обычный юзер? :)
if ($type_admission==1) {$username = "user=testuser1";}
if ($type_admission==2) {$username = "user=testuser2";}

$array = array();

$db = @pg_connect("$host $port $dbname $username $password");     //Подключаемся к БД

// Запрос к последовательности для вывода списка квитанций определенного помещения
$sql = "SELECT id_status,mm24,state_gaz,state_hvs1,state_hvs2,state_electric,summ,isumm
		FROM testschema.main_rows
		WHERE id_admission=$id_admission and mode_status=$type_admission";
$query = @pg_query($db,$sql);
$res = pg_fetch_all($query);

for ($i=0; $i<count($res); $i++){
	$array[$i] = array ('id_status' => $res[$i]['id_status'],
						'mm24' => $res[$i]['mm24'],
						'state_gaz' => $res[$i]['state_gaz'],
						'state_hvs1' => $res[$i]['state_hvs1'],
						'state_hvs2' => $res[$i]['state_hvs2'],
						'state_electric' => $res[$i]['state_electric'],
						'summ' => number_format($res[$i]['summ'],2,',',' '),
						'isumm' => number_format($res[$i]['isumm'],2,',',' '));
}

echo json_encode(array('items'=>$array,'totalCount'=>count($res)));

pg_close($db);      //Закрываем соединение с БД
?>