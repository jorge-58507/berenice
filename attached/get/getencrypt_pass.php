<?php
require '../../gi_con.php';
$link = conexion();

$pass = $_GET['q'];

$var_encriptada=crypt($pass,"2A");

echo "<input type='text' name='txt_clave' id='txt clave' value=".$var_encriptada." />";
?>