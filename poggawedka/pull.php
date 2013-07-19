<?
if($_SERVER['REMOTE_ADDR']!='91.197.15.34'){header('Location: http://gadunews.pl/'); die(''); }
require_once('../MessageBuilder.php');
require_once('config.php');
require_once('db_connect.php');
require_once('../functions.php');
require_once('functions.php');



$M=new MessageBuilder();
$inChat=false;
$user_exists=false;
$username_exists=false;




$sql="SELECT count(ggid) as ile FROM `users` Where ggid='".escapeDBString($_GET['from'])."'";
if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
{	
	$row = $result->fetch_array();
		$user_exists=(bool)$row['ile'];
	$result->close();
}

if(!$user_exists)
{
	$user_info=getUserInfo($_GET['from']);
	$sql="SELECT count(ggid) as ile FROM `users` Where nickname='".escapeDBString($user_info->nick)."'";

	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		$row = $result->fetch_array();
			$username_exists=(bool)$row['ile'];
		$result->close();
	}

}







if(!$user_exists)
{
	if($username_exists)
	{
		$username=escapeDBString($user_info->nick."_".$_GET['from']);		
		$username=str_replace(" ","_",$username);	
	}
	else
	{
		$username=escapeDBString($user_info->nick);
		$username=str_replace(" ","_",$username);
		$username =iconv('UTF-8', 'ASCII//TRANSLIT', $username);		
		$username=preg_replace('/(\W+)/i', "", $username, -1);	
		if(strlen($username)<3)
		{
			$username="GG_".$_GET['from'];
		}		
	}
	$sql="INSERT INTO `users` (`ggid` ,`nickname` )VALUES ('".escapeDBString($_GET['from'])."', '".$username."')";
	$mysqli->query($sql);
	newUserAddedToDataBase($username);
	$M->addBBCode("\n\nNie wybrałeś nicku, nick przydzielony przez automat to: [b]".$username."[/b]. Możesz go zmienić korzystając z komendy !nick");
	//commandStartStop($_GET['from'],true);
}
else if(banned($_GET['from'])) //baaaan!
{
	$M=createSystemMessage(HINT_MESSAGE,"Zostałeś/aś zbanowany/a na czacie. Nie możesz korzystać z żadnych jego funkcjonalności."," Odbanowanie możliwe jest jedynie przy kontakcie bezpośrednim z administratorem czatu (np. mailowo).");
}
else if(base64_encode(trim($HTTP_RAW_POST_DATA))=='IWluZm8=') // nie usuwać i nie ruszać
{
		$M->addBBcode(base64_decode("W2JdU2tyeXB0IGN6YXR1IG9wYXJ0eSBvIFBvR0dhd8SZZGthU1JDWy9iXSAtIGRhcm1vd3kgc2tyeXB0IGN6YXRvd3kgZGxhIHNpZWNpIEdHLiBXacSZY2VqIGluZm9ybWFjamk6IGh0dHA6Ly9nYWR1bmV3cy5wbC8/cD0xNDYy"));
}
else if($HTTP_RAW_POST_DATA[0]=='!') //czyli komendy
{
	$sql="INSERT INTO `logs` (`ggid` ,`raw_post_data` ,`timestamp` )VALUES ('".escapeDBString($_GET['from'])."', '".escapeDBString($HTTP_RAW_POST_DATA)."', NOW( ) );";
	$mysqli->query($sql);
	
	$command=explode(' ',$HTTP_RAW_POST_DATA);
	if(strtolower($command[0])=='!start')
	{
		commandStartStop($_GET['from'],true);
	}
	else if(strtolower($command[0])=='!stop')
	{
		commandStartStop($_GET['from'],false);
	}
	else if(strtolower($command[0])=='!nick')
	{
		$nick=trim(str_replace($command[0],"",$HTTP_RAW_POST_DATA));
		commandNick($_GET['from'],$nick);
	}
	else if(strtolower($command[0])=='!avatar' || strtolower($command[0])=='!awatar')
	{
		$avatar=trim(str_replace($command[0],"",$HTTP_RAW_POST_DATA));
		commandAvatar($avatar);
	}
	else if(strtolower($command[0])=='!oczekuj')
	{
		commandOczekuj($_GET['from']);
	}
	else if(strtolower($command[0])=='!online')
	{
		commandOnline();
	}
	else if(strtolower($command[0])=='!ranking')
	{
		commandTop();
	}
	
	else if(strtolower($command[0])=='!raport' || strtolower($command[0])=='!raportuj')
	{
		$raport=trim(str_replace($command[0],"",$HTTP_RAW_POST_DATA));
		if(strlen($raport)<5)
		{
				$M=createSystemMessage(HINT_MESSAGE,'Musisz podać jakiś powód dla którego używasz funkcji raportowania, np.:[br]','!raport użytkownik XYZ mnie obraża i używa wulgaryzmów.');
		}else
		{		
			commandRaport($_GET['from'],$raport,$M);
		}
	}
	#ADMIN COMMANDS
	else if(strtolower($command[0])=='!silentstart' || strtolower($command[0])=='!silentstop')
	{
		if(isModerator($_GET['from']))
		{
			commandStartStop($_GET['from'],(strtolower($command[0])=='!silentstart'),true);
		}
		else
		{
			$M=createSystemMessage(HINT_MESSAGE,'Nie masz uprawnień do operacji zmiany opisu/statusu');
		}
	}
	else if(strtolower($command[0])=='!kickidle')
	{
		if(isModerator($_GET['from']))
		{
			commandKickIdle();
		}
		else
		{
			$M=createSystemMessage(HINT_MESSAGE,'Nie masz uprawnień do operacji zmiany opisu/statusu');
		}
	}
	else if(strtolower($command[0])=='!stan')
	{
		$opis=str_replace($command[0]." ".$command[1],"",$HTTP_RAW_POST_DATA);
		if(strlen($opis)>240)
		{
			$M=createSystemMessage(HINT_MESSAGE,'Opis za długi');
		}
		else if(isAdmin($_GET['from']))
		{
			commandStan($command[1],trim($opis));
			die();
		}
		else
		{
			$M=createSystemMessage(HINT_MESSAGE,'Nie masz uprawnień do operacji zmiany opisu/statusu');
		}
	}
	else if(strtolower($command[0])=='!ban')
	{
		$username=str_replace($command[0]." ","",$HTTP_RAW_POST_DATA);
		if(isModerator($_GET['from']))
		{
			commandBan($command[1]);
			
		}
		else
		{
			$M=createSystemMessage(HINT_MESSAGE,'Nie masz uprawnień do operacji banowania użytkowników');
		}
	}
	else if(strtolower($command[0])=='!unban')
	{
		$username=str_replace($command[0]." ","",$HTTP_RAW_POST_DATA);
		if(isModerator($_GET['from']))
		{
			commandBan($command[1], false);
			
		}
		else
		{
			$M=createSystemMessage(HINT_MESSAGE,'Nie masz uprawnień do operacji odbanowania użytkownika');
		}
	}
	else if(strtolower($command[0])=='!kick')
	{
		$username=trim(str_replace($command[0],"",$HTTP_RAW_POST_DATA));
		if(isModerator($_GET['from']))
		{
			if(commandKick($command[1]))
			{
				$M=createSystemMessage(HINT_MESSAGE,'Troll wykopany.');
			}
			else
			{
				$M=createSystemMessage(HINT_MESSAGE,'Nie ma usera o takim nicku.');
			}
			
		}
		else
		{
			$M=createSystemMessage(HINT_MESSAGE,'Nie masz uprawnień do operacji wyrzucania użytkowników');
		}
	}
	else if(strtolower($command[0])=='!motd')
	{
		if(isAdmin($_GET['from']))
		{
			commandMotd(trim(str_replace($command[0],"",$HTTP_RAW_POST_DATA)));
			
		}
		else
		{
			$M=createSystemMessage(HINT_MESSAGE,'Nie masz uprawnień do operacji zmiany opisu/statusu');
		}
	}
	else if(strtolower($command[0])=='!debug')
	{
		$tekst=(trim(str_replace($command[0],"",$HTTP_RAW_POST_DATA)));
	/*	$tekst=preg_replace('{(http:\/\/[\w\.\-_]+\.[a-z0-9.][^\n\s]*)}', '<'.base64_encode($1).'>', $tekst);
		*/
		if(preg_match_all("{(http:\/\/[\w\.\-_]+\.[a-z0-9.][^\n\s]*)}", $tekst, $matches)>0)
		{
			foreach($matches[0] as $t)
			{
				
				
				$tekst=str_replace($t,urlencode(base64_encode($t)),$tekst);
			}
		}
		
		$M->addText($tekst);
	}
	else
	{
		helpMessage($_GET['from']);
	}
	
	
}
else
{
	
	
	require_once('functions.php');
	$inChat=isActive($_GET['from']);		
	
	if($inChat==false)
	{
		helpMessage($_GET['from']);
	}
	else if($HTTP_RAW_POST_DATA[0]=='>') //pw
	{
		require_once('push.php');
		require_once('functions.php');
		$ggid_to_tmp=explode(' ',$HTTP_RAW_POST_DATA);
		$ggid_to=$ggid_to_tmp[0];
		$ggid_to=str_replace('>','',$ggid_to);
		$ggid_to=getGGID($ggid_to);
		if($ggid_to===false)
		{
			$M->addText("Użytkownik nie istnieje");
		}
		else if($ggid_to==$_GET['from'])
		{
			$M->addText("Do samego siebie nie możesz wysyłać wiadomości prywatnych");
		}
		else{
			$msg=(str_replace($ggid_to_tmp[0],"",$HTTP_RAW_POST_DATA));
			$sql="INSERT INTO `messages_priv` (`ggid_from`,`ggid_to` ,`message` ,`timestamp` )VALUES ('".escapeDBString($_GET['from'])."','".$ggid_to."' , '".escapeDBString($msg)."', NOW( ) );";
			$mysqli->query($sql);
			sendPrivMessageToUser($msg,getNick($_GET['from']),$ggid_to);
			die();
		}
	
	}
	//czy wiadomość coś sobą reprezentuje?
	else if(strlen(str_replace(" ","",$HTTP_RAW_POST_DATA))<3)
	{
		$M=createSystemMessage(HINT_MESSAGE,"Wiadomość za krótka","Wysil się trochę ;)");
	}
	//czy to nie jest wiadomość z serii "kto jest na czacie?"?
	else if(strpos(strtolower($HTTP_RAW_POST_DATA),"kto")!==false && strpos(strtolower($HTTP_RAW_POST_DATA),"jest")!==false && strlen($HTTP_RAW_POST_DATA)<40)
	{
			$M=createSystemMessage(HINT_MESSAGE,"Zamiast pytać kto jest na czacie, skorzystaj z opcji !online."," Więcej informacji pod komendą !pomoc.");
			
	}
	//czy w środku numer GG?	
	else if(isInGGID($_GET['from'],$HTTP_RAW_POST_DATA))
	{
		$M=createSystemMessage(HINT_MESSAGE,"Na publicznym czacie nie można podawać swojego numeru GG :( ","Jeśli chcesz go komuś podać, skorzystaj z opcji prywatnych wiadomości pisząc:\n>nick_osoby wiadomość\nWięcej informacji w pomocy (komenda: !pomoc)");
	}
	//czy wulgarne?	
	else if(isVulgar($HTTP_RAW_POST_DATA))
	{
		$M=createSystemMessage(HINT_MESSAGE,"Znaleźliśmy wulgaryzmy w Twojej wiadomości. ","Wiadomość oczywiście nie została wysłana.");
	}
	//sprawdzamy czy człowieczak nie spamuje za bardzo na głównym czacie
	else if(flooded($_GET['from']))
	{
			$M=createSystemMessage(RULE_FLOOD);
			
	}
	else //pozostałe wiadomości
	{
			
		
		
		$sql="INSERT INTO `messages` (`ggid` ,`message` ,`timestamp` )VALUES ('".escapeDBString($_GET['from'])."', '".escapeDBString($HTTP_RAW_POST_DATA)."', NOW( ) );";
		$mysqli->query($sql);
		if($mysqli->errno == 0)
		{
		
			require_once('push.php');
			$message=$HTTP_RAW_POST_DATA;
	


			//przekierowanie linków z kodowaniem
			if($url_prefix!=NULL)
			{
				if(preg_match_all("{(http:\/\/[\w\.\-_]+\.[a-z0-9.][^\n\s]*)}", $message, $matches)>0)
				{
					foreach($matches[0] as $t)
					{						
						if(strpos($t,$url_prefix)!==0)
						{
							$message=str_replace($t,$url_prefix.urlencode(base64_encode($t)),$message);
						}
					}
				}
			}
			
			//$message=preg_replace('/(\d{5,})/i', "[Numer GG Ukryty]", $HTTP_RAW_POST_DATA, -1);
			
			sendChatMessageToActiveUsers($message,$_GET['from']);

			die();
			
		}
		
	}

}

$M->reply();



?>
