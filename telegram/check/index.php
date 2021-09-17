<?php

define('ENCRYPTION_KEY', 'e3f080b6edfcf6fff70654021c7c2e43');


$ip = $_SERVER["REMOTE_ADDR"];




$con = mysqli_connect(checkinfo("host"),checkinfo("login"),checkinfo("password"),checkinfo("database"));
if (mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_error();
}


$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");



$query = $con->query("SELECT * FROM `users` WHERE `ip` = '".$ip."'");
if ($query == false) {
	echo "{'result':false}";
} else {
	$query = $con->query("SELECT `token` FROM `account`");
	while ($row = $query->fetch_assoc()) {
	    $TELEGRAM_TOKEN = $row["token"];
	}
  $TELEGRAM_MESS = getResponse("https://api.telegram.org/bot".$TELEGRAM_TOKEN."/getUpdates");
  $mess = json_decode($TELEGRAM_MESS, true)["result"][count(json_decode($TELEGRAM_MESS, true)["result"])-1]["message"]["text"];
  $query = $con->query("SELECT `id` FROM `users` WHERE `ip` = '".$ip."'");
  while ($row = $query->fetch_assoc()) {
      $num = (int)$row["id"];
  }
  $arrmess = str_split($mess);
  $textfor = "";
  for($i = 0; $i < count($arrmess); ++$i) {
    if ($arrmess[$i] == ".") {
      unset($arrmess[$i]);
      break;
    } else {
      $textfor .= $arrmess[$i];
      unset($arrmess[$i]);
    }
  }
  if ((int)$textfor == $num) {
    $con->query("INSERT INTO telegramaccept (id, ip, mess) VALUES(".$num.",'".$ip."', '".implode("", $arrmess)."')");
    $con->query("UPDATE `telegramaccept` SET `mess` = '".implode("", $arrmess)."' WHERE `id` = ".$num."");
    echo implode("", $arrmess);
  } else {
    echo "";
  }
}

$con->close();



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