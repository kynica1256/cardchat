<?php

$img = $_POST["img"];
$imgname = $_POST["imgname"];

base64ToFile($img, $imgname);



function base64ToFile($data, $path) {
    $source = fopen($data, 'r');
    $destination = fopen($path, 'w');
    stream_copy_to_stream($source, $destination);
    fclose($source);
    fclose($destination);
}

?>