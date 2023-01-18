<?php
include_once("storage.php");
include_once("userstorage.php");
include_once("auth.php");
include_once("helper.php");

session_start();
$auth = new Auth(new UserStorage());
$user_storage = new UserStorage();

$auth->logout();
redirect("index.php");
?>