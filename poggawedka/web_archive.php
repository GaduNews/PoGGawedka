<?
require_once('config.php');
require_once('../functions.php');
?><html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="web_style.css" type="text/css" media="screen" />

<? if($_GET['d']!=1){?>
<meta http-equiv="refresh" content="30;url=web_archive.php?d=0&time=<? echo time();?>">
<? } ?>
</head><body>
<?
/*strony archiwum numerowane są od zera, parametr "p" w zapytaniu
parametr d = 0 -> bez daty i możliwości przewinięcia, dorzuca odświeżanie co 30 sekund 
*/

require_once('db_connect.php');
define('ILE_NA_STRONE',50);

$page=$_GET['p'];
$startPage=$page*ILE_NA_STRONE;


if($page!=0 && $_GET['d']==1){
?>
<form method="GET">
<input type="submit" value="Nowsze"/>
<input type="hidden" value="<? echo ($page-1);?>" name="p"/>
<input type="hidden" value="<? echo $_GET['d'];?>" name="d"/>
</form>
<? } ?>
<img src="" style="display:none;" id="avatarBig">
<script type='text/javascript' src='web_js.js'></script>




<?
function mini($row)
{
	global $ggid_status;
	$nickname=$row['nickname'];
	//if($row['active_channel']>0 && getGGNetworkStatus($row['ggid'])!=GG_STATUS_NIEDOSTEPNY)
	if($ggid_status[$row['ggid']])
	{
		$nickname="<span title='On-Line'><b>".$nickname."</b></span>";
	}
	else
	{
		$nickname="<span title='Off-Line'><b>".$nickname."</b></span>";
	}
		
	?>			
		<nobr><div onmouseover="onMouseOver(this);" onmouseout="onMouseOut(this);" src="./web_avatars.php?nick=<? echo $row['nickname']; ?>"><? echo ($nickname);?>:</div></nobr>
		
		<span title="<? echo $row['timestamp'];?>"><? echo htmlentities($row['message'],ENT_QUOTES, "UTF-8");?></span>

	<?	
}

function full($row)
{
	global $ggid_status;
	$nickname=$row['nickname'];
	//if(/*$row['active_channel']>0 && */getGGNetworkStatus($row['ggid'])!=GG_STATUS_NIEDOSTEPNY)
	if($ggid_status[$row['ggid']])
	{
		$nickname="<span title='On-Line'><b>".$nickname."</b></span>";
	}
	else
	{
		$nickname="<span title='Off-Line'><b>".$nickname."</b></span>";
	}
		
	?>
	<tr>
	
		
		<td class="poggawedka_datetime"><i><nobr><? echo $row['timestamp'];?></nobr></i></td>
	
		
		<td><nobr>
		
		<img class="poggawedka_avatar" onmouseover="onMouseOver(this);" onmouseout="onMouseOut(this);" src="./web_avatars.php?nick=<? echo $row['nickname']; ?>"> <? echo ($nickname);?>:
		</nobr>
		</td>
		<td class="poggawedka_message"><span title="<? echo $row['timestamp'];?>"><? echo htmlentities($row['message'],ENT_QUOTES, "UTF-8");?></span></td>
	</tr>
	<?	
}










	$sql="SELECT messages.ggid as ggid,timestamp,nickname,message,active_channel FROM `messages` JOIN `users` ON messages.ggid=users.ggid ORDER BY `messages`.`timestamp`  DESC LIMIT ".$startPage.",".ILE_NA_STRONE;
	$msgs_i=0;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		
		while($row = $result->fetch_array())
		{
			$msgs[$msgs_i]=$row;
			$msgs_i++;
				//if($_GET['d']==1){full($row);}else{mini($row);}	
		}
		$result->close();
		
	}
	
$mysqli->close();	

foreach($msgs as $row)//ustala online/offline
{
	if(isset($ggid_status[$row['ggid']])){continue;}
	
	if(getGGNetworkStatus($row['ggid'])!=GG_STATUS_NIEDOSTEPNY && $row['active_channel']>0)
	{
		$ggid_status[$row['ggid']]=true;
	}
	else
	{
		$ggid_status[$row['ggid']]=false;
	}

}


if($_GET['d']==1){
	echo "<table>";
	foreach($msgs as $row)
	{
			
		full($row);
	}
	echo "</table>";
}
else
{
	for($i=($msgs_i-1);$i>=0;$i--)
	{
		mini($msgs[$i]);
	}
}

?>


	
	
<?
if($_GET['d']==1){?>
<form method="GET">
<input type="submit" value="Starsze"/>
<input type="hidden" value="<? echo ($page+1);?>" name="p"/>
<input type="hidden" value="<? echo $_GET['d'];?>" name="d"/>
</form>
<? } ?><div name="down" id="down"></div>


<? if($_GET['d']!=1){?>
<script>
function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) 
	{
			do {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
			} while (obj = obj.offsetParent);
		
			return [curleft,curtop];
	}
}

  	var pos=findPos(document.getElementById('down'));
   
	window.scrollTo(pos[0],pos[1]);

</script>
<? }  ?>

</body></html>