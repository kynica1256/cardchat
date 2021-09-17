<?php


error_reporting(E_ALL ^ E_WARNING); 

define('ENCRYPTION_KEY', 'e3f080b6edfcf6fff70654021c7c2e43');



$mess = base64_decode($_GET["message"]);
$ip = $_GET["ip"];
$city = json_decode(file_get_contents("http://api.sypexgeo.net/json/".$ip, true))->city;
echo $city;


$con = mysqli_connect(checkinfo("host"),checkinfo("login"),checkinfo("password"),checkinfo("database"));
if (mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_error();
}


$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");



$query = $con->query("SELECT `token` FROM `account`");
while ($row = $query->fetch_assoc()) {
    $TELEGRAM_TOKEN = $row["token"];
}

$query = $con->query("SELECT `id` FROM `users` WHERE `ip` = '".$ip."'");
while ($row = $query->fetch_assoc()) {
    $ID = (int)$row["id"];
}

$query = $con->query("SELECT `link` FROM `users` WHERE `ip` = '".$ip."'");
while ($row = $query->fetch_assoc()) {
    $link = $row["link"];
}

$con->close();

$TELEGRAM_MESS = getResponse("https://api.telegram.org/bot".$TELEGRAM_TOKEN."/getUpdates");

$TELEGRAM_CHATID = json_decode($TELEGRAM_MESS, true)["result"][0]["message"]["from"]["id"];


$rep = file_get_contents($mess);

if ($rep == false) {
  $message = "id - ".$ID."%0A";
  $message = "Switched over - ".$link."%0A";
  $message .= "city - ".$city."%0A";
  $message .= "ip - ".$ip."%0A"."Sample response - ";
  $message .= $ID.".your text"."%0A";
  $message .= "id.your text"."%0A";
  $message .= "!!!!You don't need a space before the id!!!!"."%0A";
  $message .= "Message from the user - %0A".$mess;
  echo getResponse ('https://api.telegram.org/bot'.$TELEGRAM_TOKEN.'/sendMessage?chat_id='.$TELEGRAM_CHATID.'&text='.$message);
} else {
  $message = "id - ".$ID."%0A";
  $message = "Switched over - ".$link."%0A";
  $message .= "city - ".$city."%0A";
  $message .= "ip - ".$ip."%0A"."Sample response - ";
  $message .= $ID.".your text"."%0A";
  $message .= "id.your text"."%0A";
  $message .= "!!!!You don't need a space before the id!!!!"."%0A";
  $message .= "Message from the user - %0A"."<a href='".$mess."'>".$mess."</a>";
  echo getResponse ('https://api.telegram.org/bot'.$TELEGRAM_TOKEN.'/sendMessage?chat_id='.$TELEGRAM_CHATID.'&text='.$message.'&parse_mode=HTML');
}


function getResponse($url)
{
    $response = file_get_contents($url);
    return $response;
}


function checkinfo($name) {
    $text = '';
    $fh = fopen("../../datareg/".$name, 'r');
    while (!feof($fh)) {
      $line = fgets($fh);
      $text .= $line . PHP_EOL;
    }
    fclose($fh);
    return decrypt($text, ENCRYPTION_KEY);
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