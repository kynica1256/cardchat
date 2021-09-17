<?php

define('ENCRYPTION_KEY', 'e3f080b6edfcf6fff70654021c7c2e43');


$data = $_POST["data"];
$namefile = $_POST["namefile"];
$ip = $_SERVER["REMOTE_ADDR"];



$city = json_decode(file_get_contents("http://api.sypexgeo.net/json/".$ip))->city;
$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");

file_get_contents($protocol.'://'.$_SERVER["SERVER_NAME"].'/chatproject/telegram/send/index.php?message='.base64_encode($protocol.'://'.$_SERVER["SERVER_NAME"].'/chatproject/datareg/userfiles/'.$namefile)."&ip=".$ip);


$con = mysqli_connect(checkinfo("host"),checkinfo("login"),checkinfo("password"),checkinfo("database"));
if (mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_error();
}

base64ToFile($data, $namefile);

$protocol = "http".(isset($_SERVER['HTTPS']) ? "s" : "");

$mess = base64_encode($protocol.'://'.$_SERVER["SERVER_NAME"].'/chatproject/datareg/userfiles/'.$namefile);


$query = $con->query("SELECT * FROM `users` WHERE `ip` = '".$ip."'");
if ($query == false) {
	echo "{'result':false}";
} else {
  $con->query("INSERT INTO telegramsend (ip, mess) VALUES('".$ip."', '".$mess."')");
	$query = $con->query("SELECT `id` FROM `users` WHERE `ip` = '".$ip."'");
	while ($row = $query->fetch_assoc()) {
	    $num = (int)$row["id"];
	}
	$con->query("UPDATE `telegramsend` SET `mess` = ".$mess." WHERE id = ".$num."");
  echo $protocol.'://'.$_SERVER["SERVER_NAME"].'/chatproject/datareg/userfiles/'.$namefile;
}

$con->close();



function checkinfo($name) {
    $text = '';
    $fh = fopen("../".$name, 'r');
    while (!feof($fh)) {
      $line = fgets($fh);
      $text .= $line . PHP_EOL;
    }
    fclose($fh);
    return decrypt($text, ENCRYPTION_KEY);
}

function base64ToFile($data, $path) {
    $source = fopen($data, 'r');
    $destination = fopen($path, 'w');
    stream_copy_to_stream($source, $destination);
    fclose($source);
    fclose($destination);
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