<?php
require '../../bh_con.php';
$link = conexion();
require '../php/req_login_admin.php';


				$bh_del="DELETE FROM bh_nuevadevolucion WHERE nuevadevolucion_AI_user_id = '$user_id'";
				mysql_query($bh_del, $link) or die(mysql_error());
