<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';


				$bh_del="DELETE FROM bh_nuevadevolucion WHERE nuevadevolucion_AI_user_id = '$user_id'";
				$link->query($bh_del) or die($link->error);
