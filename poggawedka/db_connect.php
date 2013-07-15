<?
#Konfiguracja połączenia z bazą
$db_host	= "";		#nazwa hosta bazy danych, domyślnie "localhost"\n
$db_login	= "";		#login konta użytkownika bazy danych\n
$db_passwd	= "";		#hasło konta użytkownika bazy danych\n
$db_name	= "";		#nazwa bazy danych\n








$mysqli = new mysqli($db_host, $db_login, $db_passwd, $db_name);


if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

if (!$mysqli->set_charset("utf8"))
{
    printf("Error loading character set utf8: %s\n", $mysqli->error);
}


?>