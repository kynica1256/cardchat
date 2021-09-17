<?php


try {
    SetCookie("host", $_POST["host"], time()+60*60*24*365*10, '/');
    SetCookie("login", $_POST["login"], time()+60*60*24*365*10, '/');
    SetCookie("password", $_POST["password"], time()+60*60*24*365*10, '/');
    SetCookie("database", $_POST["database"], time()+60*60*24*365*10, '/');
    SetCookie("name", $_POST["name"], time()+60*60*24*365*10, '/');
    SetCookie("id", $_POST["id"], time()+60*60*24*365*10, '/');
    SetCookie("token", $_POST["token"], time()+60*60*24*365*10, '/');
} catch (Exception $e) {
	$_COOKIE['host'] = $_POST["host"];
	$_COOKIE['login'] = $_POST["login"];
	$_COOKIE['password'] = $_POST["password"];
	$_COOKIE['database'] = $_POST["database"];
	$_COOKIE['name'] = $_POST["name"];
	$_COOKIE['token'] = $_POST["token"];
	$_COOKIE['id'] = $_POST["id"];
}


?>