<?
/*
Aplikacja u¿ywaj¹ca tego zbioru musi posiadaæ owe sta³e, bez polskich znaków, zmienne w pliku config.php: 
$APP_ID_NAME="Nazwa_aplikacji";
$APP_VERSION="1.0";
$APP_COPY_OWNER="Nazwa_W³aœciciela_TEJ_kopii_lub_instancji_aplikacji";
*/


function isVulgar($str) //czy wulgarnie
{
	$wulgarneSlowka=array("kurw", "huj", "pierdala","pierdol","jeban","dziwk", "pizda","pizdo","pizdu");
	
	//same znaki alfanumeryczne, istotne aby nie obchodziæ tego w sposób np. wstawiaj¹c bia³y znak w œrodku s³owa
	$wyrazenie="";
	for($i=0;$i<strlen($str);$i++)
	{
		if(ctype_alnum($str[$i]) && !ctype_digit($str[$i]))
		{
			$wyrazenie.=$str[$i];
		}
	}
	
	foreach($wulgarneSlowka as $slowo)
	{
		if(strpos($wyrazenie,$slowo)!==false)
		{
			return true;
		}
	}
	
	return false;
	

}

function escapeDBString($str)
{
	global $mysqli;
	return $mysqli->real_escape_string($str);
}

function HTTPGet($url)
{
	
	//return file_get_contents($url);
	
	global $APP_COPY_OWNER;
	global $APP_VERSION;
	global $APP_COPY_OWNER;
	if(!isset($APP_COPY_OWNER) || !isset($APP_VERSION) || !isset($APP_COPY_OWNER)){return false;}
	$useragent="GaduNews.pl/BotAPI ".$APP_ID_NAME."/".$APP_VERSION." Owner: ".$APP_COPY_OWNER;	
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}


define("GG_STATUS_POGGADAJMY","talktome");
define("GG_STATUS_DOSTEPNY","available");
define("GG_STATUS_ZARAZ_WRACAM","busy");
define("GG_STATUS_NIE_PRZESZKADZAC","dnd");
define("GG_STATUS_NIEDOSTEPNY","unavailable");
function getGGNetworkStatus($GGID)
{


	$status=HTTPGet('http://status.gadu-gadu.pl/users/status.asp?id='.$GGID.'&styl=6');
	if($status!==false)
	{
		return trim($status);
	}
	return $status;
	//available, talktome, dnd, busy (z/w), unavailable
}

function getUserInfo($uid)
{
	$d = simplexml_load_string(trim(HTTPGet('http://api.gadu-gadu.pl/users/'.$uid.'.xml')));
	return $d->users->user[0];
}
?>