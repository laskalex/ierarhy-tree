<?php
//заголовок с кодировкой
header ("Content-Type: text/html; charset=UTF8");
?><link href="styles.css" type="text/css" rel="stylesheet"/><?php 
echo "<a href>Перегрузить страницу</a>";
//подключаем файл с функциями и файл конфигурации
include 'config.php';
include 'functions/functions.php';

db (HOST, USER, PASS, DB);	//соединение с базой данных

$result = get_cat();
//print_r($result);

//выводим каталог на экран с помощью рекурсивной функции
echo "<div style ='width:450px;padding:10px;border:1px solid #7476'>";
view_cat($result);
echo "</div>";

?><form name='form1' method='POST' action="<?=$_SERVER['PHP_SELF']?>">
	<b>Новый узел: </b><br>
	<input name='newtitle' type='text' size='30' maxlength='50' value="Значение №10"> 
	<select name="titles_id">
          <option value="0">ГЛАВНАЯ категория 
          <?php fill_select($result); ?>
	</select>
	<select name="titles2id">
          <option value="0">в Категорию: 
          <?php fill_select($result); ?>
	</select><br>
      <b>Описание:</b><br>
      <textarea name="description" rows="3" cols="43" >Описание №10</textarea>
      <input type='submit' name='submitmove' value='Переместить'><br>
      <br>
      <input type='submit' name='submitadd' value='Добавить'>
      	<input type='submit' name='submitfill' value='Заполнить'>
	<input type='submit' name='submitsave' value='Сохранить'><br>
      <input type='submit' name='submitdel' value='Удалить'>
      <input type="checkbox" name="delmaincat" value="yes">Удалять ГЛАВНЫЕ категории?<br>
</form><?php

if ($_SERVER['REQUEST_METHOD'] == 'POST')	{
	if ($_POST['submitadd'] == 'Добавить') managebd('add');
	else 	if ($_POST['submitdel'] == 'Удалить') managebd('delete', $result);
		else echo "Выбрано редактирование. <br>";
	if ($_POST['submitmove'] == 'Переместить') managebd('move');
	else 	if ($_POST['submitfill'] == 'Заполнить') managebd('fill');
	if ($_POST['submitsave'] == 'Сохранить') managebd('save');
}

//var_dump($result);
//test($result); 
?>
<?
//phpinfo(32);
?>