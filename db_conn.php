<?php  
$db_name = 'foodlocker';
$host = "localhost";

$dsn = 'mysql:dbname='.$db_name.';host='.$host;
$user = 'root';
$password = '';

try {
    $db_conn = new PDO($dsn, $user, $password, array(
        PDO::ATTR_EMULATE_PREPARES=>false,
        PDO::MYSQL_ATTR_DIRECT_QUERY=>false,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
    ));
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage(); //You could have caught the error here.
}


?>