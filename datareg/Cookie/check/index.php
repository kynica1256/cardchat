<?php

try {
    echo '{"host":"'.$_COOKIE["host"].'","login":"'.$_COOKIE["login"].'","password":"'.$_COOKIE["password"].'","database":"'.$_COOKIE["database"].'","name":"'.base64_decode($_COOKIE["name"]).'","token":"'.$_COOKIE["token"].'","id":"'.$_COOKIE["id"].'"}';
} catch (Exception $e) {
	echo "false";
}

?>