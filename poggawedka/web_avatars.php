<?
/* Plik odpowiada za pobieranie i wyświetlanie awatarów z bazy GG. Aby nie ujawniać numerów GG użytkowników skorzystano z rozwiązania pośredniczącego przez owy skrypt*/
require_once('db_connect.php');
require_once('../functions.php');
require_once('functions.php');
require_once('config.php');

$userNick=$_GET['nick'];


	
header("Expires: ".gmdate("D, d M Y H:i:s",time()+60*60)." GMT");

if($userNick=='' || !isset($userNick) || $userNick==null){
	header('Content-Type: image/png');
	$image=file_get_contents("icons/empty_avatar.png");
	
	
}
else {


	$userGGID=getGGID($userNick);
	
	$userInfo=getUserInfo($userGGID);
	$userAvatar=$userInfo->avatars[0]->avatar[0]->smallAvatar;
	
	
	if($userAvatar==null){
		header("Location: ./web_avatars.php?nick=");
		$mysqli->close();
		die();
	}
	$image=file_get_contents($userAvatar);
	header('Content-Type: image/jpeg');

}
print $image;
$mysqli->close();
?>