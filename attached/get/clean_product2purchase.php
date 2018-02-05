<?php
require '../../bh_con.php';
$link = conexion();


		$qry_nuevacompra=mysql_query("SELECT * FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'", $link);
		$nr_nuevacompra=mysql_num_rows($qry_nuevacompra);

		
		if($nr_nuevacompra > 0){
			mysql_query("DELETE FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'");
		}


