<?php
ini_set("session.cookie_lifetime","7200");
ini_set("session.gc_maxlifetime","7200");
session_start();
if(!isset($_SESSION["seg"]) or $_SESSION["seg"] != "ok")
{
header("location: index.php");
exit;
}
?>