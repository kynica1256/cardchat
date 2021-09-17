<?php

define('ENCRYPTION_KEY', 'e3f080b6edfcf6fff70654021c7c2e43');

$login = $_POST["login"];
$password = $_POST["password"];
$database = $_POST["database"];
$host = $_POST["host"];
$token = $_POST["token"];
$name = base64_decode($_POST["name"]);
$img = $_POST["img"];
$imgname = $_POST["imgname"];
$id = $_POST["id"];
$color = $_POST["color"];



$con = mysqli_connect($host,$login,$password,$database);
if (mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_error();
    exit(1);
}
$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");
$arr = array("img" => $img, "imgname" => $imgname);
requests($protocol."://".$_SERVER["SERVER_NAME"]."/chatproject/datareg/upload/", $arr);
$con->query("DROP TABLE `users`");
$con->query("DROP TABLE `account`");
$con->query("DROP TABLE `telegramsend`");
$con->query("DROP TABLE `telegramaccept`");
$con->query("CREATE TABLE users( id INT NOT NULL AUTO_INCREMENT, ip TEXT, link TEXT, city TEXT, PRIMARY KEY(id) )");
$con->query("CREATE TABLE account( name TEXT, token TEXT, img TEXT, id TEXT, color TEXT)");
$con->query("INSERT INTO account(name, token, img, id, color) VALUES('".$name."', '".$token."', '".$protocol."://".$_SERVER["SERVER_NAME"]."/chatproject/datareg/upload/".$imgname."', '".$id."', '".$color."')");
$con->query("CREATE TABLE telegramsend( id INT NOT NULL AUTO_INCREMENT, ip TEXT, mess TEXT, PRIMARY KEY(id))");
$con->query("CREATE TABLE telegramaccept( id INT NOT NULL AUTO_INCREMENT, ip TEXT, mess TEXT, PRIMARY KEY(id))");
$con->close();



addinfo(encrypt($host, ENCRYPTION_KEY), "host");
addinfo(encrypt($login, ENCRYPTION_KEY), "login");
addinfo(encrypt($password, ENCRYPTION_KEY), "password");
addinfo(encrypt($database, ENCRYPTION_KEY), "database");


function addinfo($text, $namefile) {
	$files = fopen($namefile,'w');
    fwrite($files, $text);
    fclose($files);
}

function requests($url, $arr) {
  $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($arr)
    ));
    $response = curl_exec($myCurl);
    curl_close($myCurl);
}

function encrypt($encrypt, $key) {
  $encrypt = serialize($encrypt);
  $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
  $key = pack('H*', $key);
  $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
  $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
  $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
  return $encoded;
}
 
function decrypt($decrypt, $key) {
  $decrypt = explode('|', $decrypt.'|');
  $decoded = base64_decode($decrypt[0]);
  $iv = base64_decode($decrypt[1]);
  if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
  $key = pack('H*', $key);
  $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
  $mac = substr($decrypted, -64);
  $decrypted = substr($decrypted, 0, -64);
  $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
  if($calcmac!==$mac){ return false; }
  $decrypted = unserialize($decrypted);
  return $decrypted;
}
?>