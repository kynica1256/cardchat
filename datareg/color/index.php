<?php

define('ENCRYPTION_KEY', 'e3f080b6edfcf6fff70654021c7c2e43');


$con = mysqli_connect(checkinfo("host"),checkinfo("login"),checkinfo("password"),checkinfo("database"));
if (mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_error();
}
$query = $con->query("SELECT `color` FROM `account`");
if ($query == false) {
	echo "{'result':false}";
} else {
	while ($row = $query->fetch_assoc()) {
	    $color = $row["color"];
	}
  echo $color;
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