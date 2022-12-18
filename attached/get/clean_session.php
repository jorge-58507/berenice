<?php
session_start();
$str = $_GET['a'];

unset($_SESSION[$str]);

echo "respuesta";

?>
