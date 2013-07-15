<?
/* Plik odpowiada za wyświetlanie statystyk*/
require_once('db_connect.php');
require_once('../functions.php');
require_once('functions.php');
require_once('config.php');
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="web_style.css" type="text/css" media="screen" />
	<style>
		td
		{
			text-align:center;
		}	
	</style>
</head>
<body>
<img src="" style="display:none;" id="avatarBig">
<script type='text/javascript' src='web_js.js'></script>


<center>Rankingi [Top 20]</center>
	<div style="display:inline; float:left;">
	<center>Dzień <? echo date("d.m.Y",time()-(60*60*24));?></center>
	<table>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Nick</td>
			<td>Ilość<br>wiadomości</td>
		</tr>
	<?

	$sql="SELECT nickname, count( message ) AS ile FROM `messages` JOIN users ON messages.ggid = users.ggid WHERE `timestamp` >= CURDATE( ) -1 AND `timestamp` < CURDATE( ) GROUP BY nickname ORDER BY ile DESC LIMIT 0 , 20";
	$i=1;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{		
		while($row = $result->fetch_array())
		{
			?>
		<tr>
			<td><? echo $i; $i++;?></td>
			<td><img class="poggawedka_avatar" onmouseover="onMouseOver(this);" onmouseout="onMouseOut(this);" src="./web_avatars.php?nick=<? echo $row['nickname']; ?>"></td>
			<td><? echo $row['nickname'];?></td>
			<td><? echo $row['ile'];?></td>
		</tr><?
			
			
		}
		$result->close();
	}
	?></table></div><?

	
	############# całokształt
	
	?>
	
<div style="display:inline;float:right;">
<center>Całokształt</center>
	<table>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Nick</td>
			<td>Ilość<br>wiadomości</td>
		</tr>
	<?

	$sql="SELECT nickname, count( message ) AS ile FROM `messages` JOIN users ON messages.ggid = users.ggid GROUP BY nickname ORDER BY ile DESC LIMIT 0 , 20";
	$i=1;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{		
		while($row = $result->fetch_array())
		{
			?>
		<tr>
			<td><? echo $i; $i++;?></td>
			<td><img class="poggawedka_avatar" onmouseover="onMouseOver(this);" onmouseout="onMouseOut(this);" src="./web_avatars.php?nick=<? echo $row['nickname']; ?>"></td>
			<td><? echo $row['nickname'];?></td>
			<td><? echo $row['ile'];?></td>
		</tr><?
			
			
		}
		$result->close();
	}
	?></table></div><?
	
	
	
	

$mysqli->close();
?>
</body></html>