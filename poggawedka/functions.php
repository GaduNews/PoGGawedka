<?
function helpMessage($ggid)
{
	global $M;
	
	
	$gender=getGender($ggid);
	$sufix="y"; if($gender==PLEC_KOBIETA){$sufix="a";}
	
	$M->addBBcode("Wpisz:\n[b]!start[/b] jeśli chcesz dołączyć do rozmowy\n[b]!stop[/b] jeśli chcesz opuścić rozmowę\n[b]!nick [i]nowy_nick[/i][/b] jeśli chcesz zmienić swój pseudonim na [i]nowy_nick[/i]. Jeśli nie wpiszesz nowego pseudonimu, wyświetlimy Ci Twój aktualny.\n[b]!avatar [i]nick_użytkownika[/i][/b] jeśli chcesz zobaczyć awatar danej osoby jaki ma ustawiony w sieci GG\n[b]!oczekuj[/b] jeśli chcesz zmienić tryb dostarczania wiadomości offline z czatu. Jeśli będziesz niedostępn".$sufix." to w zależności od ustawienia wiadomości z aktywnego czatu będą lub nie będą do Ciebie wysyłane\n[b]!online[/b] wyświetla listę użytkowników, którzy widzą aktualną konwersację na czacie (użyli opcji !start)\n[b]!ranking[/b] gdy chcesz zobaczyć ranking(TOP 20)\n[b]!raportuj [i]wiadomość[/i][/b] jeśli chcesz zgłosić jakieś nadużycie. Treść [i]wiadomości[/i] zostanie wysłana do wszystkich moderatorów, również nieaktywnych\n\n\nKomendy działające tylko jeśli jesteś aktywny na czacie (komenda [i]!start[/i])\n[b]>nick_użytkownika [i]wiadomość[/i][/b] jeśli chcesz wysłać wiadomość do danej osoby (wiadomości dochodzą nawet jeśli osoba jest nieaktywna).\n[b]@nick_użytkownika[/b] jeśli chcesz wspomnieć o kimś w ramach rozmowy. Jeśli osoba nie jest aktywna na czacie, dostanie powiadomienie o odniesieniu się niej.\n\nWięcej informacji (w tym archiwum rozmów): http://gadunews.pl/?p=1462");
}

function newUserAddedToDataBase($username)
{
	global $M;
	global $_GET;
	$M->addBBcode("[b]Witaj ".$username."![/b]\nJesteś pierwszy raz na naszym czacie. Przed skorzystaniem z usługi zapoznaj się z [u]regulaminem[/u]: http://xn--2da.tk/PoGGawedka-Regulamin?user=".$_GET['from']."\nZnajdź nas na Facebooku: http://www.facebook.com/pages/PoGGawedka/411280128907312\nWięcej informacji: http://gadunews.pl/?p=1462\n\n");
	helpMessage($_GET['from']);
}

/*
function getLastMessages($count) //używane przy !start w pierwszych wersjach PoGGawędki, aktualnie nieużywane
{
	$sql="SELECT nickname, timestamp, message FROM `messages` LEFT JOIN users ON messages.ggid=users.ggid ORDER BY `messages`.`timestamp`  DESC LIMIT 0,".$count;
	global $mysqli;
	global $M;
	
	$i=0;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		while($row = $result->fetch_array())
		{
			$msg[$i]=$row;	
			$i++;			
		}
		$result->close();
	}
	
	
	for($j=$i;$j>=0;$j--)
	{
		$M->addBBcode("[i]".$msg[$j]['timestamp']."[/i] [b]".$msg[$j]['nickname']."[/b]:\t".$msg[$j]['message']."[br]");
	}
	
	
}*/

function commandTop(){
	global $mysqli;
	global $M;
	$date_yesterday = date("d.m.Y",time()-(60*60*24));
	$sql="SELECT nickname, count( message ) AS ile FROM `messages` JOIN users ON messages.ggid = users.ggid WHERE `timestamp` >= CURDATE( ) -1 AND `timestamp` < CURDATE( ) GROUP BY nickname ORDER BY ile DESC LIMIT 0 , 20";
	$i=1;
	$message_top = "TOP 20\n\nDzień ".$date_yesterday."\n";
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{		
		while($row = $result->fetch_array())
		{
			$message_top .= $i.". ";
			$i++;
			$message_top .= $row['nickname']." ";
			$message_top .= $row['ile'];
			$message_top .= "\n";
		}
	}
	$result->close();
	$sql="SELECT nickname, count( message ) AS ile FROM `messages` JOIN users ON messages.ggid = users.ggid GROUP BY nickname ORDER BY ile DESC LIMIT 0 , 20";
	$i=1;
	$message_top .= "\nCałokształt\n";
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{		
		while($row = $result->fetch_array())
		{
			$message_top .= $i.". ";
			$i++;
			$message_top .= $row['nickname']." ";
			$message_top .= $row['ile'];
			$message_top .= "\n";
		}
	}
	$result->close();
	$M=createSystemMessage(HINT_MESSAGE,$message_top,"");
}

function commandRaport($ggid_from,$raport, &$M)
{

	$modList=getValueSetting("admins");
	$modList.=",".getValueSetting("moderators");
	$modList=explode(",", $modList);

	require_once('push.php');
	$nickname=getNick($ggid_from);
	sendSystemMessageToUsers($nickname,$raport,$modList,RULE_ABUSE);
	
	if(file_exists('../XMPPHP/XMPP.php')){
		$reports_xmpp=getValueSetting('reports_xmpp');
		$reports_xmpp=explode(",", $reports_xmpp);	
		if(count($reports_xmpp)>0){
			include('../XMPPHP/XMPP.php');
			global $xmpp_config;
			$conn = new XMPPHP_XMPP($xmpp_config['host'], $xmpp_config['port'], $xmpp_config['username'], $xmpp_config['password'], 'PoGGawędka', $xmpp_config['server'], $printlog=False, $loglevel=LOGGING_INFO);
			$conn->connect();
			$conn->processUntil('session_start');
			foreach($reports_xmpp as $jid)
			{
				$conn->message($jid, 'Raport od '.$nickname."\n".$raport);	
			}
			$conn->disconnect();
		}
	}
	$M=createSystemMessage(HINT_MESSAGE,"Raport został wysłany.", "Pamiętaj o nienadużywaniu tej opcji.");
	
}

function commandKick($nickname)
{
	$ggid=getGGID($nickname);
	if($ggid===false)
	{
		return false;
	}
	
	commandStartStop($ggid,false);
	require_once('push.php');
	sendSystemMessageToUsers("","",array($ggid),RULE_KICK);
	return true;
}

function commandBan($nickname, $ban=true)
{
	global $M;
	
	if($ban)
	{
		$sqlValue=1;
		$infoMessage='zbanowany';
		$rule=RULE_BAN;
	}
	else
	{
		$sqlValue=0;
		$infoMessage='odbanowany';
		$rule=RULE_UNBAN;
	}
	
	$ggid=getGGID($nickname);
	if($ggid===false)
	{
		$M=createSystemMessage(HINT_MESSAGE,"Użytkownik nie istnieje","");
		return;
	}
	
	global $mysqli;
	$sql="UPDATE `users` SET `banned` = '".$sqlValue."' WHERE `users`.`ggid` =".$ggid.";";
	$mysqli->query($sql);
	$M=createSystemMessage(HINT_MESSAGE,"Użytkownik został ".$infoMessage.".");
	
	global $_GET;
	$adminNickName=getNick($_GET['from']);
	
	$M2=createSystemMessage($rule,$adminNickName,"");
	$M2->setRecipients(array($ggid));
	require_once('../PushConnection.php');
	global $bot_id;
	global $bot_login;
	global $bot_password;
	$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->push($M2);
	
}

function commandOnline()
{
	$sql="SELECT ggid,nickname, active_only_when_online FROM `users` WHERE `active_channel` =1 AND banned = 0 ORDER BY `users`.`nickname` ASC";
	global $mysqli;
	global $M;

	
	$usersActiveOnlyOnline="Użytkownicy aktywni, ale niepołączeni (otrzymają wiadomości po zalogowaniu): \n\t";
	$usersActive="Użytkownicy aktywni i połączeni (widzą konwersację na żywo): \n\t";
	
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		while($row = $result->fetch_array())
		{
			try
			{
				$status=getGGNetworkStatus($row['ggid']);
				if($status==GG_STATUS_NIEDOSTEPNY/*|| $row['active_only_when_online']==0*/) //niedostępny, ale aktywny offline
				{
					$usersActiveOnlyOnline.=$row['nickname'].", ";
				}
				else if ($status!=GG_STATUS_NIEDOSTEPNY && $status!==false) //dostępny i aktywny
				{
					$usersActive.=$row['nickname'].", ";
				}
			}
			catch (Exception $e)
			{}
			
			
		}
		$result->close();
	}
	
	$usersActive=substr($usersActive, 0, -2); 
	$usersActiveOnlyOnline=substr($usersActiveOnlyOnline, 0, -2); 
	$M=createSystemMessage(HINT_MESSAGE,"Użytkownicy aktywni na czacie:\n".$usersActive."\n",$usersActiveOnlyOnline);
}


function buildMOTD(&$M)
{
		
		$motd=getValueSetting("motd");
		if(strlen($motd)>3)
		{
			$M->addImage("icons/motd.png");
			$M->addBBcode(" [b][color=0000CC]Wiadomość dnia[/color][/b]: ".$motd);
		}
}

define("PLEC_KOBIETA",1);
define("PLEC_MEZCZYZNA",2);
function getGender($ggid)
{
	$plec=getUserInfo($ggid);
	return $plec->gender[0];
}

function commandStartStop($ggid,$start,$silent=false)
{
	$gender=getGender($ggid);
	//var_dump($gender);
	$sufix['2LP']="e";	if($gender==PLEC_KOBIETA){$sufix['2LP']="a";} //druga osoba liczby poj.
	$sufix['2LP_2']="y";	if($gender==PLEC_KOBIETA){$sufix['2LP_2']="a";} //druga osoba liczby poj.
	$sufix['3LP']="";	if($gender==PLEC_KOBIETA){$sufix['3LP']="a";} // trzecia osoba lp
	
	
	
	$sql="UPDATE `users` SET `active_channel` = '".(int)$start."' WHERE `users`.`ggid` ='".escapeDBString($ggid)."' AND `active_channel` = ".(int)(!$start);
	global $M;
	global $mysqli;
	$mysqli->query($sql);
	
	
	
	if($mysqli->affected_rows==0)
	{
		if(!$start){$tmp = "nie ";}
		$M->addBBcode("[b]Aktualnie ".$tmp."jesteś obecn".$sufix['2LP_2']." na czacie.[/b]");
		return;
	}
	
	require_once('push.php');
	if($start){
		
		
		
		
		$mynickname=getNick($ggid);
		if($silent==false){
			adminMessage("Dołączył".$sufix['3LP']." do nas [b]".$mynickname."[/b]",true,false,'icons/information.png','0000FF');
		}
		
		$M->addBBcode("[b]Dołączył".$sufix['2LP']."ś do czatu pod nickiem: [u]".$mynickname."[/u]. Aby wyjść, wpisz [u]!stop[/u][/b]\n\n");
		
		//getLastMessages(10);
		
		
		
		
		buildMOTD($M);
		

	}
	else
	{
		$M->addBBcode("[b]Opuścił".$sufix['2LP']."ś czat.[/b]");
		if($silent==false){
			adminMessage("[b]".getNick($ggid)."[/b] opuścił".$sufix['3LP']." czat...",true,false,'icons/information.png','0000FF');
		}
	}
	
}


function commandOczekuj($ggid)
{
	global $M;
	global $mysqli;
	$newSet=0;
	$sql="SELECT active_only_when_online  FROM `users` WHERE `ggid` = '".escapeDBString($ggid)."'";
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		$row = $result->fetch_array();
		
		if($row['active_only_when_online']==0)
		{
			$newSet=1;
		}
		$result->close();
	}
	
	
	$gender=getGender($ggid);
	$sufix="y"; if($gender==PLEC_KOBIETA){$sufix="a";}
	
	$sql="UPDATE `users` SET `active_only_when_online` = '".$newSet."' WHERE `users`.`ggid` ='".escapeDBString($ggid)."'";
	$mysqli->query($sql);
	
	
	if($newSet==0){
		$M=createSystemMessage(HINT_MESSAGE,"Od teraz wiadomości z czatu [u]będą[/u] wysyłane do Ciebie również wtedy, gdy będziesz niedostępn".$sufix,"");
	}
	else
	{
		$M=createSystemMessage(HINT_MESSAGE,"Od teraz wiadomości z czatu [u]nie będą[/u] wysyłane do Ciebie wtedy, gdy będziesz niedostępn".$sufix,"");
	}
}

function nickValidate($nick)
{
	global $M;
	if(strlen($nick)>15)
	{
		$M->addText("Nick za długi. ");
		return false;
	}
	$nick=str_replace(" ","",$nick);
	if(!ctype_alnum($nick))
	{
		$M->addText("Nick zawiera niedozwolone znaki. Możesz użyć tylko znaków alfanumerycznych oraz spacji.\n ");
		return false;
	}
	
	return true;
	
}

function commandNick($ggid,$new_nick)
{
	global $M;
	
	$old_nick=getNick($ggid);
	if($new_nick!=null && strpos(strtolower($new_nick),"admin")===false && nickValidate($new_nick))
	{
		$new_nick=str_replace(" ","_",$new_nick);
		$sql="UPDATE `users` SET `nickname` = '".escapeDBString($new_nick)."' WHERE `users`.`ggid` ='".escapeDBString($ggid)."';";
		global $mysqli;
		$mysqli->real_query($sql);
		
		if($mysqli->errno == 1062)
		{
			$M->addBBcode("[b]Takiego pseudonimu nie możesz użyć ;)[/b]\n");
		}else
		{
			require_once('push.php');
			adminMessage("Zmiana nicku: ".$old_nick." → ".$new_nick,true,false,'icons/information.png','0000FF');
		}
	}

	$M->addBBcode("[b]Twój nick: ".getNick($ggid)."[/b]");
	
}


function getGGID($nickname)
{
	$sql="SELECT ggid FROM `users` WHERE `nickname` ='".escapeDBString($nickname)."'";
	global $mysqli;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		$row = $result->fetch_array();
		$ggid=$row['ggid'];
		$result->close();
	}
	if(is_numeric($ggid) && $ggid>0)
	{
		return $ggid;
	}
	else
	{
		return false;
	}
}


function getNick($ggid)
{
	$sql="SELECT nickname FROM `users` WHERE `ggid` ='".escapeDBString($ggid)."'";
	global $mysqli;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		$row = $result->fetch_array();
		$nick=$row['nickname'];
		$result->close();
	}
	if(isset($nick))
	{
		return $nick;
	}
	else
	{
		return false;
	}
}

function commandStan($status,$opis)
{
	require_once('../PushConnection.php');
	switch($status)
	{
		case 1: $status=STATUS_FFC; break;
		case 2: $status=STATUS_BACK; break;
		case 3: $status=STATUS_AWAY; break;
		case 4: $status=STATUS_DND; break;
		case 5: $status=STATUS_INVISIBLE; break;
		default:$status=null; break; 
	}
	
	global $bot_id;
	global $bot_login;
	global $bot_password;
	$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->setStatus(trim($opis), $status);
}

function commandMotd($motd)
{
	
	
	if($motd=="0")
	{
		setValueSetting("motd","");
	}
	else if(strlen($motd)>3)
	{
		setValueSetting("motd",$motd);
		$M_local=new MessageBuilder();
		buildMOTD($M_local);
		require_once('push.php');
		adminMessage("",true,true,'','0000FF',$M_local);
		
	}
	else
	{
		global $M;
		$M->addText("Aktualna wiadomość dnia: \n");
		$M->addBBcode(getValueSetting("motd"));
	}
	
	
}

function commandAvatar($nickname)
{
	$sql="SELECT ggid FROM `users` WHERE `nickname` ='".escapeDBString($nickname)."'";
	global $mysqli;
	global $M;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		$row = $result->fetch_array();
		$ggid=$row['ggid'];
		$result->close();
	}
	if($ggid==null){
		$M->addBBCode('[b]Nie ma takiego usera[/b]');
	}
	else
	{
		$user_info=getUserInfo($ggid);
		
		if($user_info->avatars[0]->avatar[0]->smallAvatar !=null)
		{
			$M->addImage($user_info->avatars[0]->avatar[0]->smallAvatar);
			$M->addBBCode(' [b]Awatar użytkownika [u]'.$nickname.'[/u][/b]');
		}
		else
		{
			$M->addBBCode('[b]Użytkownik [u]'.$nickname.'[/u] nie posiada awatara.[/b]');
		}
	}
}


function isActive($ggid)
{
	global $mysqli;
	$sql="SELECT active_channel,ggid FROM `users` WHERE `ggid` ='".escapeDBString($ggid)."'";
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{	
		$row = $result->fetch_array();
		if($row['active_channel']==0){
			return false;
		}
		$result->close();
	}
	return true;
}



define('ADMIN_MESSAGE_REFER',0);
define('PRIV_MESSAGE',1);
define('RULE_FLOOD',2);
define('HINT_MESSAGE',3);
define('RULE_BAN',4);
define('RULE_KICK',5); 
define('RULE_ABUSE',6); 
define('RULE_UNBAN',7);
function createSystemMessage($type,$p1=null,$p2=null)
{
	$M=new MessageBuilder();
	
	if($type==ADMIN_MESSAGE_REFER) //$p1 = nick usera, $p2 = wiadomość
	{
		$gender=getGender(getGGID($p1));
		$sufix=""; if($gender==PLEC_KOBIETA){$sufix="a";}
	
		$M->addImage("icons/refer.png");
		$M->addBBcode(" [color=B803FF][b][u]".$p1."[/u] wspomniał".$sufix." o Tobie: [/b][/color]\n\t");
		$M->addText($p2);
	
	}
	else if($type==PRIV_MESSAGE) //p1 - nick usera, p2 - wiadomość
	{
		$M->addImage('icons/private_message.png');	
		$M->addBBcode(" [color=B803FF]✉[b]Prywatna wiadomość od [u]".$p1."[/u][/b]:[/color]\n");
		$M->addText("\t".$p2);
	}
	else if($type==RULE_KICK) //
	{
		$M->addImage('icons/kick.gif');	
		$M->addBBcode(" [color=FF0000][b]Wykopano Cię z czatu (tylko Admin wie kto to zrobił).[/b][/color] Wróć do nas jak zapoznasz się z regulaminem. Jeśli uważasz, że wyrzucono Cię bezpodstawnie, napisz zgłoszenie na maila z godziną o której się to wydarzyło (nasz adres znajduje się w regulaminie) - rozprawimy się ze złym moderatorem ;)");
	//	$M->addText("\t".$p2);
	}
	else if($type==RULE_FLOOD) //p1 - nick usera, p2 - wiadomość
	{
		$M->addImage('icons/not_delivery.png');	
		$M->addBBcode(" [color=FF0000][b]Wiadomość nie została wysłana[/b][/color]. Wysyłasz za dużo wiadomości w krótkich odstępach czasu. Spokojnie napisz dłuższą wypowiedź i dopiero wtedy ją wyślij.");
		
	}
	else if($type==HINT_MESSAGE) //p1 - w pogrubieniu z kolorem, p2 - bez pogrubienia i koloru
	{
		$M->addImage('icons/information2.png');	
		$M->addBBcode(" [color=FF0000][b]".$p1."[/b][/color] ".$p2);
		
	}
	else if($type==RULE_BAN) //p1 - kto dał bana
	{
		//$M->addImage('icons/not_delivery.png');	
		$M->addBBcode(" [color=FF0000][b]Twoje konto zostało zbanowane przez użytkownika ".$p1."[/b][/color]. Jeśli nie zgadzasz się z tą decyzją, zgłoś się do nas mailowo.");
		
	}
	else if($type==RULE_UNBAN) //p1 - kto dał unbana
	{
		//$M->addImage('icons/not_delivery.png');	
		$M->addBBcode(" [color=0000FF][b]Twoje konto zostało odbanowane przez użytkownika ".$p1."[/b][/color].");
		
	}
	else if($type==RULE_ABUSE) //p1 - kto zgłasza (nick), p2, treść
	{
		$M->addImage('icons/warning.png');	
		$M->addBBcode(" [color=FF0000][b]Raport od [u]".$p1."[/u][/b][/color]:\n\t".$p2);
		
	}
	return $M;
}

function flooded($ggid) //sprawdzamy czy user nie spamuje za bardzo
{
	define('FLOOD_MAX_TIME',10); /// w ciągu x sekund
	define('FLOOD_MESSAGE_COUNT',2); // można napisać y wiadomości
	$sql="SELECT count(message) as ile  FROM `messages` WHERE `timestamp` > TIMESTAMPADD(SECOND,-".FLOOD_MAX_TIME.",NOW()) AND ggid='".escapeDBString($ggid)."'";
	global $mysqli;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{	
		$row = $result->fetch_array();
		if($row['ile']>=FLOOD_MESSAGE_COUNT){
			return true;
		}
		$result->close();
	}
	return false;
}

function banned($ggid) //sprawdzamy czy user jest zbanowany
{
	$sql="SELECT banned  FROM `users` WHERE `ggid` = '".escapeDBString($ggid)."'";
	global $mysqli;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{	
		$row = $result->fetch_array();
		if($row['banned']!=0){
			return true;
		}
		$result->close();
	}
	return false;
}


function isInGGID($ggid,$str) //sprawdzamy czy w wiadomości podano swój numer GG, na publicznym czacie nie można tego wykonywać
{
	$wyrazenie="";
	for($i=0;$i<strlen($str);$i++)
	{
		if(ctype_digit($str[$i]))
		{
			$wyrazenie.=$str[$i];
		}
	}
	
	if(strpos($wyrazenie,$ggid)!==false)
	{
			return true;
	}
	return false;
	
}

function isAdmin($ggid)
{
	$adminList=getValueSetting("admins");
	$adminList=explode(",", $adminList);
	foreach($adminList as $admin)
	{
		if($admin==$ggid)
		{
			return true;
		}
	}
	
	return false;
}

function isModerator($ggid)
{
	if(isAdmin($ggid)){return true;} //każdy admin ma też uprawnienia moderatora

	$moderators=getValueSetting("moderators");
	$moderators=explode(",", $moderators);
	foreach($moderators as $moderator)
	{
		if($moderator==$ggid)
		{
			return true;
		}
	}	
	return false;
}

function getValueSetting($key)
{
	global $mysqli;	
	$sql="SELECT value FROM `settings` WHERE `key` = '".escapeDBString($key)."'";
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{	
		$row = $result->fetch_array();
		return $row['value'];
		$result->close();
	}
	return false;
	
}

function setValueSetting($key,$value)
{
	global $mysqli;	
	$sql="UPDATE `settings` SET `value` = '".escapeDBString($value)."' WHERE `settings`.`key` = '".escapeDBString($key)."';";
	$mysqli->query($sql);
	
	if($mysqli->affected_rows>0){return true;}
	
	
	$sql="INSERT INTO `settings` (`key`, `value`) VALUES ('".escapeDBString($key)."', '".escapeDBString($value)."');";
	$mysqli->query($sql);
	if($mysqli->affected_rows>0){return true;}
	return false;
	
}

?>
