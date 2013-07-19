<?
#### SEKCJA IDENTYFIKACJI APLIKACJI
$APP_ID_NAME="";
$APP_VERSION="";
$APP_COPY_OWNER=""; //wpisujemy swój nick lub nazwę strony www w jakiej działa czat

#### SEKCJA DLA XMPP
/* XMPP jest protokołem przesyłania wiadomości w czasie rzeczywistym. Używany jest w komunikatorach takich jak AQQ, Google Talk, Tlen czy Facebook Chat i NKTalk. Skonfigurowanie konta XMPP pozwoli Ci na otrzymywanie raportów (komenda !raportuj) również w tej sieci, np. bezpośrednio na facebooka lub na telefon z systemem Android (każdy taki telefon posiada wbudowany działający w tle komunikator Google TALK). Listę identyfikatorów należy podać w tabeli `settings` w rekordzie `reports_xmpp` (każdy identyfikator oddzielony przecinkiem).*/
$xmpp_config['host']='jid.pl'; //nazwa hosta
$xmpp_config['server']=null; //adres serwera, podawać jeśli inny niż host, jeśli taki sam to ustawiamy null
$xmpp_config['port']='5222'; //port serwera
$xmpp_config['username']=''; // nazwa użytkownika
$xmpp_config['password']=''; //hasło



$url_prefix = "http://xn--2da.tk/m/";//wpisujemy url będący prefiksem innych urli wysyłanych na czacie. Jeśli user wyśle na czat link np. http://gg.pl/ to to co jest w tej zmiennej zostanie doklejone przed tym adresem, np: http://czat.com/r/http://gg.pl . jeśli zmienna będzie pusta, to nic do linków nie będzie doklejane. Stosowane w momencie gdy chcemy usera przekierować przez jakiś anonimizer albo stronę wyświetlającą ostrzeżenie/reklamy itp. Ustaw wartość na NULL jeśli nie chcesz korzystać z tej opcji.


#### SEKCJA KONFIGURACJI BOTAPI
$bot_id=$_GET['to']; //numer bota, zautomatyzowane
$bot_login=""; // login bota
$bot_password=""; //hasło bota
?>
