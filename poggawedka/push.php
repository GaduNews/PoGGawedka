<?
/* Zawiera funkcje wysyłające wiadomości do użytkowników innych niż osoba wywołująca funkcje*/


function adminMessage($msg,$onlyInChat,$offline,$icon,$color='FF0000', $M_input=null)
{
	global $bot_id;
	global $bot_login;
	global $bot_password;
	
	require_once('../PushConnection.php');
	require_once('../functions.php');
	
	$sql="SELECT ggid FROM `users`";
	if($onlyInChat){
		$sql.=" WHERE active_channel!=0";
	}
	
	global $mysqli;
	$i=0;
	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		while($row = $result->fetch_array())
		{
				$recipients[$i]=$row['ggid'];
				$i++;
		}
		$result->close();
	}
	$M=new MessageBuilder();
	
	if($icon!=null)
	{
		$M->addImage($icon);
		$M->addText(" ");
	}
	$M->addBBcode("[color=".$color."]\t".$msg."[/color]");
	
	if($M_input!==null){ //jeśli $M_input wypełnione, czyli jeśli chcemy wprowadzić sformatowaną już wiadomość z innej funkcji
		$M=$M_input;
	}
	
	$M->setRecipients($recipients);
	$M->setSendToOffline($offline);
	$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->push($M);
	
}

function sendSystemMessageToUsers($msg1,$msg2,$ggid_to_array,$type=null) // msg1 - p1, msg2 - p2
{
	global $bot_id;
	global $bot_login;
	global $bot_password;
	
	require_once('../PushConnection.php');
	require_once('../functions.php');
	require_once('functions.php');
	if($type!=null)
	{
		$M=createSystemMessage($type,$msg1,$msg2);
	}
	else
	{
		$M=new MessageBuilder();
		$M->addText($msg1);
	}
	
	$M->setRecipients($ggid_to_array);
	$M->setSendToOffline(true);
	$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->push($M);
	
}

function sendPrivMessageToUser($msg1,$nick_from,$ggid_to)
{
	global $bot_id;
	global $bot_login;
	global $bot_password;
	
	require_once('../PushConnection.php');
	require_once('../functions.php');
	require_once('functions.php');
	
	$M=createSystemMessage(PRIV_MESSAGE,$nick_from,$msg1);
		
	$M->setRecipients(array($ggid_to));
	$M->setSendToOffline(true);
	$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->push($M);
		
}


function referInChat($msg,$M)
{
	preg_match_all('/@\w{1,}\W{1}/', $msg, $matches);
	$i=0;
	global $refferer_list;
	$refferer_list=array();
	foreach($matches[0] as $m)
	{
		
		$nick=trim(str_replace("@","",$m));
		while(!ctype_alnum(substr($nick, -1)))
		{
			$nick=substr($nick, 0,-1);
		}		

		$ggid=getGGID($nick);
		if($ggid!==false)
		{
			if(!isActive($ggid)){//tylko gdy nie jest aktywny wysyłamy mu informację, że wspomniano o nim
				array_push($refferer_list,$ggid);
			
			}			
			$nick_list[$i]=$nick;			
			$i++;
		}
		
		
	}
	
	$msg_array=explode("@",$msg);
	//if(strlen($msg_array[0])==0){$msg_array[0]="@".$msg_array[0];}
	for($i=1;$i<count($msg_array);$i++)
	{
		$msg_array[$i]="@".$msg_array[$i];		
		
	}
	
	for($i=0;$i<count($msg_array);$i++)
	{
		$id_nick=false;
		for($j=0;$j<count($nick_list);$j++){
		
			if(strpos($msg_array[$i],"@".$nick_list[$j])!==false)
			{
				$id_nick=$j;
				break;
			}
		}
		if($id_nick!==false)
		{
			$M->addBBcode("[b]"."@".$nick_list[$id_nick]."[/b]");
			$msg_array[$i]=str_replace("@".$nick_list[$id_nick],"",$msg_array[$i]);
		}
		
		$M->addText($msg_array[$i]);
	
	}
	

	return $M;
	
}



function sendChatMessageToActiveUsers($msg,$senderGGID)
{
	
	

	global $bot_id;
	global $bot_login;
	global $bot_password;
	
	require_once('../PushConnection.php');
	require_once('../functions.php');
	
	$M=new MessageBuilder();
	
	
	$sql="SELECT ggid, active_only_when_online FROM `users` WHERE `ggid` !='".escapeDBString($senderGGID)."' AND active_channel!=0 AND banned=0";
	$recipients;
	$recipients_online_only;
	$i=0;
	$i_online_only=0;
	global $mysqli;
	
	

	if ($result = $mysqli->query($sql, MYSQLI_USE_RESULT))
	{
		while($row = $result->fetch_array())
		{
			if($row['active_only_when_online']==0)
			{
				$recipients[$i]=$row['ggid'];
				$i++;
			}
			else
			{
				$recipients_online_only[$i]=$row['ggid'];
				$i_online_only++;
			}
		}
		$result->close();
	}
	
	$senderNick=getNick($senderGGID);
	$M->addBBcode("[b]".$senderNick."[/b]:\t");
	$nick_list=array();
	$M=referInChat($msg,$M);
	
	
	
	
	#wszyscy
	$M->setRecipients($recipients);
	$M->setSendToOffline(true);
	$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->push($M);
	
	#online tylko
	$M->setRecipients($recipients_online_only);
	$M->setSendToOffline(false);
	//$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->push($M);
	
	//wysyłanie informacji o "wspomnieniach"
	unset($M);
	$M=createSystemMessage(ADMIN_MESSAGE_REFER,$senderNick, $msg);
	global $refferer_list;
	$M->setRecipients($refferer_list);
	$M->setSendToOffline(false);
	//$P=new PushConnection($bot_id, $bot_login, $bot_password);
	$P->push($M);
	

}



?>