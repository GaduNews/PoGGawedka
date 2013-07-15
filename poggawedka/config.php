<?
#### SEKCJA IDENTYFIKACJI APLIKACJI
$APP_ID_NAME="";
$APP_VERSION="";
$APP_COPY_OWNER=""; //wpisujemy swój nick lub nazwê strony www w jakiej dzia³a czat

#### SEKCJA DLA XMPP
/* XMPP jest protoko³em przesy³ania wiadomoœci w czasie rzeczywistym. U¿ywany jest w komunikatorach takich jak AQQ, Google Talk, Tlen czy Facebook Chat i NKTalk. Skonfigurowanie konta XMPP pozwoli Ci na otrzymywanie raportów (komenda !raportuj) równie¿ w tej sieci, np. bezpoœrednio na facebooka lub na telefon z systemem Android (ka¿dy taki telefon posiada wbudowany dzia³aj¹cy w tle komunikator Google TALK). Listê identyfikatorów nale¿y podaæ w tabeli `settings` w rekordzie `reports_xmpp` (ka¿dy identyfikator oddzielony przecinkiem).*/
$xmpp_config['host']='jid.pl'; //nazwa hosta
$xmpp_config['server']=null; //adres serwera, podawaæ jeœli inny ni¿ host, jeœli taki sam to ustawiamy null
$xmpp_config['port']='5222'; //port serwera
$xmpp_config['username']=''; // nazwa u¿ytkownika
$xmpp_config['password']=''; //has³o



$url_prefix = "http://xn--2da.tk/m/";//wpisujemy url bêd¹cy prefiksem innych urli wysy³anych na czacie. Jeœli user wyœle na czat link np. http://gg.pl/ to to co jest w tej zmiennej zostanie doklejone przed tym adresem, np: http://czat.com/r/http://gg.pl . jeœli zmienna bêdzie pusta, to nic do linków nie bêdzie doklejane. Stosowane w momencie gdy chcemy usera przekierowaæ przez jakiœ anonimizer albo stronê wyœwietlaj¹c¹ ostrze¿enie/reklamy itp. Ustaw wartoœæ na NULL jeœli nie chcesz korzystaæ z tej opcji.


#### SEKCJA KONFIGURACJI BOTAPI
$bot_id=0; //numer bota
$bot_login=""; // login bota
$bot_password=""; //has³o bota
?>