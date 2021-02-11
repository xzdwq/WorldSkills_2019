<?php
// Печать извещения (квитанции)
$host		= "host=server.skills.st";
$port		= "port=5432";
$dbname		= "dbname=postgres";
$password	= "password=1";

$type_admission = $_GET['type_admission'];
$id_status 		= $_GET['id_status'];

// Определение админ ли ты или обычный юзер? :)
if ($type_admission==1) {$username = "user=testuser1";}
if ($type_admission==2) {$username = "user=testuser2";}

// Функция для получения месяца прописью по номеру
function get_mes($num){
	switch($num){
		case 1: $mes = 'январь'; break;
		case 2: $mes = 'февраль'; break;
		case 3: $mes = 'март'; break;
		case 4: $mes = 'апрель'; break;
		case 5: $mes = 'май'; break;
		case 6: $mes = 'июнь'; break;
		case 7: $mes = 'июль'; break;
		case 8: $mes = 'август'; break;
		case 9: $mes = 'сентябрь'; break;
		case 10: $mes = 'октябрь'; break;
		case 11: $mes = 'ноябрь'; break;
		case 12: $mes = 'декабрь'; break;
		default: $mes = ''; break;
	}
	
	return $mes;	
}

$db = @pg_connect("$host $port $dbname $username $password");     //Подключаемся к БД

// Поиск площади дома
$sql 	 = "SELECT s_room FROM testschema.room WHERE type_room=2";
$query 	 = @pg_query($db,$sql);
$res 	 = @pg_fetch_all($query);
$s_house = $res[0]['s_room'];

//Запрос формирования данных об извещении
$sql = "SELECT id_status,to_char(mm24,'mm') mes,to_char(mm24,'yyyy') god,address,fio,s_room,people,state_gaz,state_hvs1,state_hvs2,state_electric
		FROM testschema.room a JOIN testschema.status b 
		ON a.id_room=b.id_room
		WHERE a.type_room=$type_admission and id_status=$id_status";
		
$query = @pg_query($db,$sql);
$res   = @pg_fetch_all($query);
@pg_close($db);      //Закрываем соединение с БД

// Данные из запроса
$mes			= $res[0]['mes'];
$god			= $res[0]['god'];
$address		= $res[0]['address'];
$fio			= $res[0]['fio'];
$s_room			= $res[0]['s_room'];
$people			= $res[0]['people'];
$state_gaz		= $res[0]['state_gaz'];
$state_hvs1		= $res[0]['state_hvs1'];
$state_hvs2		= $res[0]['state_hvs2'];
$state_hvs_all	= $state_hvs1+$state_hvs2;
$state_electric	= $res[0]['state_electric'];

// Тариф за услуги
$tarif_kons		= 1000;
$tarif_shlag	= 100;
$tarif_tbo		= 2.5;
$tarif_domofon	= 55;
$tarif_paking	= 600;
$tarif_tehob	= 9;
$tarif_lift		= 100;
$tarif_electo	= 3.5;
$tarif_gaz		= 5.6;
$tarif_voda_hol	= 40.2;

// Начисления по каждой услуге
$nach_hvs		= $state_hvs_all*$tarif_voda_hol;
$nach_electro	= $state_electric*$tarif_electo;
$nach_gaz		= $state_gaz*$tarif_gaz;

//Итого начислений по услугам
$itogo = $nach_hvs+$nach_electro+$nach_gaz;

//"Тело" печати "жировки"
echo ('------------------------------------------------------------------------------------------------------------------------------<br>');
echo ('<div style=float:left;width:390px;font-weight:bold;>УК "Умный дом"</div>Извещение № '.$id_status.' за '.get_mes($mes).' '.$god.' года<br>');
echo ('о размере платы за жилищно-коммунальные услуги и платы за пользование жилым помещением<br>');
echo ('------------------------------------------------------------------------------------------------------------------------------<br>');
echo('адрес объекта: '.$address.'<br>');
echo('Плательщик: '.$fio.'<br>');
echo('площадь помещения / общего имущества здания: '.$s_room.' кв.м / кв.м: '.$s_house.'<br>');
echo('Количество зарегистрированных: '.$people.' чел.<br>');
echo ('------------------------------------------------------------------------------------------------------------------------------<br>');
echo ('<table border="1" style="width:670px;"><tbody><tr style="background-color:grey;">');
echo ('<td style="font-weight:bold;">Название услуги</td><td style="font-weight:bold;">ед.изм.</td><td style="font-weight:bold;">кол-во</td><td style="font-weight:bold;">тариф</td><td style="font-weight:bold;">начисления</td><tr>');
echo ('<td>Коммунальные услуги</td><td></td><td></td><td></td><td></td><tr>');
echo ('<td>-техобслуживание</td><td></td><td></td><td style="text-align: right;">'.$tarif_tehob.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>-расход ХВС по показаниям ИПУ</td><td>куб.м.</td><td style="text-align: right;">'.number_format($state_hvs_all,3,'.',' ').'</td><td style="text-align: right;">'.$tarif_voda_hol.'</td><td style="text-align: right;">'.number_format($nach_hvs,2,',',' ').'</td><tr>');
echo ('<td>-расход электроэнергии по показаниям ИПУ</td><td>кВт*ч</td><td style="text-align: right;">'.number_format($state_electric,3,'.',' ').'</td><td style="text-align: right;">'.$tarif_electo.'</td><td style="text-align: right;">'.number_format($nach_electro,2,',',' ').'</td><tr>');
echo ('<td>-расход газа по показаниям ИПУ</td><td>куб.м.</td><td style="text-align: right;">'.number_format($state_gaz,3,'.',' ').'</td><td style="text-align: right;">'.$tarif_gaz.'</td><td style="text-align: right;">'.number_format($nach_gaz,2,',',' ').'</td><tr>');
echo ('<td>-вывоз ТБО</td><td></td><td></td><td style="text-align: right;">'.$tarif_tbo.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>Дополнительные услуги:</td><td></td><td></td><td></td><td></td><tr>');
echo ('<td>-шлагбаум</td><td></td><td></td><td style="text-align: right;">'.$tarif_shlag.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>-домофон</td><td></td><td></td><td style="text-align: right;">'.$tarif_domofon.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>-консьерж</td><td></td><td></td><td style="text-align: right;">'.$tarif_kons.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>-паркинг</td><td>маш/место</td><td></td><td style="text-align: right;">'.$tarif_paking.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>-лифт</td><td></td><td></td><td style="text-align: right;">'.$tarif_lift.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>Общедомовые нужды (доля ОДН):</td><td></td><td></td><td></td><td></td><tr>');
echo ('<td>-электроэнергия</td><td>кВт*ч</td><td></td><td style="text-align: right;">'.$tarif_electo.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>-расход ХВС</td><td>куб.м.</td><td></td><td style="text-align: right;">'.$tarif_voda_hol.'</td><td style="text-align: right;"></td><tr>');
echo ('<td>ИТОГО</td><td></td><td></td><td></td><td style="text-align: right;">'.number_format($itogo,2,',',' ').'</td><tr>');
echo ('</tr></tbody></table>');
?>