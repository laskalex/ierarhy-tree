<?php
//соединение с базой данных
function db($host,$user,$pass,$database) {
	$db = mysql_connect($host,$user,$pass);
	if(!$db) {
		exit('Ошибка соединения c сервером БД: ' . mysql_error());
	}
	if(!mysql_select_db($database,$db)) {
		exit('Ошибка выбора БД: ' . mysql_error());
	}
	mysql_query("SET NAMES UTF8");
}

//функция получения массива каталога
function get_cat() {
	//запрос к базе данных
	$sql = "SELECT id, title, description, parent_id FROM categories";
	$result = mysql_query($sql);
	if(!$result) {
		return NULL;
	}
	$arr_cat = array();
	if(mysql_num_rows($result)!=0) {
		//в цикле формируем массив
		for ($i = 0; $i< mysql_num_rows($result); $i++) {
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			if (empty($arr_cat[$row['parent_id']])) {
				$arr_cat[$row['parent_id']] = array();
			}
			$arr_cat[$row['parent_id']][] = $row;
		}
    	}
	return $arr_cat;
}

//вывод кататлога с помощью рекурсии
function view_cat($arr, $parent_id = 0) {
	if (empty($arr[$parent_id])){
		return;
	}
	echo "<ul>";
	//перебираем в цикле массив  и выводим на экран
	for ($i = 0; $i < count($arr[$parent_id]); $i++) {
		echo "<li><a href='?category_id=".$arr[$parent_id][$i]['id'].
 			"&parent_id=".$parent_id."'>".$arr[$parent_id][$i]['title']."</a> ".
 									 $arr[$parent_id][$i]['description'];
		//рекурсия проверяем нет ли дочерних категорий
		view_cat($arr,$arr[$parent_id][$i]['id']);
		echo "</li>";
	}
	echo '</ul>';
}

function fill_select($arr, $parent_id = 0) {
	if (empty($arr[$parent_id])) {
		return;
	}
	for ($i = 0; $i < count($arr[$parent_id]); $i++) {
		//echo "<option value=\"0\">This is my first time";
		echo "<option value=\"". $arr[$parent_id][$i]['id'] ."\">".$arr[$parent_id][$i]['title'];
		//проверяем дочерние категории
		fill_select($arr,$arr[$parent_id][$i]['id']);
	}
}

function findchpids($arr, $parent_id = 0, &$pidset) {
	//static $pidset = array(0=> 0);
	if (empty($arr[$parent_id])) {	//print_r($pidset);
		//echo "<br>Return. <br>"; 
		return;
	}
	for ($i = 0; $i < count($arr[$parent_id]); $i++) {
		//echo "<br>FindCh.pid=$parent_id, arr[$parent_id][$i]['id']=" . $arr[$parent_id][$i]['id'] ."<br>" ;
		$pidset[] += $arr[$parent_id][$i]['parent_id'];
		findchpids($arr, $arr[$parent_id][$i]['id'], $pidset);
	}
}

function managebd($act, $arr = array()) {
	if ($act == 'add') {
		$sql = "INSERT INTO " .DB. "." .TABLE1. " (title, description, parent_id) VALUES ('" .
			$_POST['newtitle'] . "', '" . $_POST['description'] . "', '" . $_POST['titles_id'] . "');";
		$query = mysql_query($sql);
	} 
	if ($act == 'delete') {
		if ($_POST['delmaincat'] != 'yes') {	 			//Ищем полученный ID в списке ГЛАВНЫХ категорий
			$ismaincat = false;							//print_r($arr[0]);
			if ($_POST['titles_id'] != 0)
				for ($i = 0; $i < count($arr[0]); $i++) {	//echo "$i, ";
					if ($arr[0][$i]['id'] == $_POST['titles_id']) {
						$ismaincat = true;
						break;
					}
				} 
			else $ismaincat = true;						//var_dump($ismaincat);	
			if ($ismaincat) echo "<br><br>Удаление ГЛАВНЫХ категорий не разрешено.<br>";
			else echo "Удаляем подчиненную категорию и всех её потомков.";
		} 
		if  ((($ismaincat) && ($_POST['delmaincat'] == 'yes')) || !($ismaincat))	{
			$sql = "DELETE FROM `" .DB. "`.`" .TABLE1. "` WHERE `" .TABLE1. "`.`id` = " . $_POST['titles_id'] ;
			static $pidset = array(0=> 0);				//Ищем и Удаляем потомков...
			findchpids($arr, $_POST['titles_id'], $pidset);
			$pidset = array_unique($pidset);
			sort($pidset);								//print_r($pidset);
			for ($i = 1; $i < count($pidset); ++$i)	{		//echo " $i .";
				$sql .= " OR `"  .TABLE1. "`.`parent_id` = " . $pidset[$i] ;
			}										echo "<br>SQL:<br>"; 
			$query = mysql_query($sql);
		}
	}
	if ($act == 'move') {
		echo "move button";
	}
	if ($act == 'fill') {
		echo "fill button";
	}
	if ($act == 'save') {
		echo "save button";
	}
	echo "$sql <br>";
	if (!$query && !$ismaincat) echo  "Ошибка SQL <br>";
}


function test($arr, $parent_id = 0) {
	if (empty($arr[$parent_id])) {
		return;
	}
	for ($i = 0; $i < count($arr[$parent_id]); $i++) {
		echo "<br>id=".$arr[$parent_id][$i]['id']." i=".$i." title=".$arr[$parent_id][$i]['title'];
		test($arr,$arr[$parent_id][$i]['id']);
	}
}
?>