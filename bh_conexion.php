<?php
function conexion(){
	$mysqli = new mysqli('127.0.0.1', 'root', '', 'bill_helper');
	if ($mysqli->connect_errno) {
	    echo "Lo sentimos, este sitio web está experimentando problemas.";
	    echo "Error: Fallo al conectarse a MySQL debido a: \n";
	    echo "Errno: " . $mysqli->connect_errno . "\n";
	    echo "Error: " . $mysqli->connect_error . "\n";
	    exit;
	}
return $mysqli;
}

class recurrent_function{
	public function replace_special_character($str){
		$to_replace = array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&nolger;","&squote;","&deg;","°","&ntilde;","&laremun;","&dblquote;");
		// $replacement = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","\n","'","°","º","ñ","#","\"");
		$replacement = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","\n","&apos;","°","º","ñ","#","&quot;");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function replace_special_character_no_html($str){
		$to_replace = array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&nolger;","&squote;","&deg;","°","&ntilde;","&laremun;","&dblquote;");
		$replacement = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","\n","'","°","º","ñ","#","\"");
		// $replacement = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","\n","&apos;","°","º","ñ","#","&quot;");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function replace_regular_character($str){
		$to_replace = array("Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","\n","'","º","°","ñ","#","\"");
		$replacement = array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&nolger;","&squote;","°","&deg;","&ntilde;","&laremun;","&dblquote;");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function url_replace_special_character($str){
		$to_replace = array("ampersand;","Aacute;","Eacute;","Iacute;","Oacute;","Uacute;","Ntilde;","aacute;","eacute;","iacute;","oacute;","uacute;","laremun;","nolger;","squote;","deg;","°","ntilde;","dblquote;");
		$replacement = array("&","Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","°","º","ñ","\"");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function url_replace_regular_character($str){
		$to_replace = array("&","Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","º","°","ñ","\"");
		$replacement = array("ampersand;","Aacute;","Eacute;","Iacute;","Oacute;","Uacute;","Ntilde;","aacute;","eacute;","iacute;","oacute;","uacute;","laremun;","nolger;","squote;","°","deg;","ntilde;","dblquote;");
		foreach($to_replace as $key => $val){
			$str= str_replace($val,$replacement[$key],$str);
		}
		return $str;
	}
	public function method_message ($verb, $sender='', $recipient='', $title='', $content='', $type='', $hour='', $date='') {
		$link=conexion();
		switch($verb) {
			case 'create':
				$slug = time();
				$link->query("INSERT INTO bh_mensaje (emisor_AI_user_id,receptor_AI_user_id,TX_mensaje_titulo,TX_mensaje_value,TX_mensaje_tipo,TX_mensaje_activo,TX_mensaje_hora,TX_mensaje_fecha,TX_mensaje_slug) VALUES ('$sender','$recipient','$title','$content','$type','1','$hour','$date','$slug')")or die($link->error);
			break;
			case 'read':
				$txt_query = '';
				// if(intval($_COOKIE['coo_tuser']) > 2) {
					$txt_query .= "SELECT AI_mensaje_id, TX_mensaje_titulo, TX_mensaje_value, TX_mensaje_fecha, TX_mensaje_hora, TX_mensaje_activo FROM bh_mensaje WHERE TX_mensaje_tipo = 'notification' AND receptor_AI_user_id = '{$_COOKIE['coo_iuser']}' GROUP BY TX_mensaje_slug ORDER BY AI_mensaje_id DESC LIMIT 25";
				// }else{
					// $txt_query .= "SELECT AI_mensaje_id, TX_mensaje_titulo, TX_mensaje_value, TX_mensaje_fecha, TX_mensaje_hora, TX_mensaje_activo FROM bh_mensaje WHERE TX_mensaje_tipo = 'notification' GROUP BY TX_mensaje_slug ORDER BY AI_mensaje_id DESC LIMIT 25";
				// }
				$qry_notification = $link->query($txt_query)or die($link->error);
				// $qry_notification = $link->query()or die($link->error);
				$raw_notification = array();
				while ($rs_notification = $qry_notification->fetch_array(MYSQLI_ASSOC)){
					$raw_notification[] = $rs_notification;
				}
				return $raw_notification;
			break;
			case 'read_active':
			break;
			case 'update_status':
					$link->query("UPDATE bh_mensaje SET TX_mensaje_activo = 0 WHERE receptor_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
				return 'Updated';
			break;
			case 'delete':
			break;

		}
	}
	public function read_user () {
		$link = conexion();
		$qry_user = $link->query("SELECT AI_user_id, TX_user_seudonimo, TX_user_type FROM bh_user")or die($link->error);
		$raw_user = array();
		while($rs_user=$qry_user->fetch_array(MYSQLI_ASSOC)) {
			$raw_user[$rs_user['AI_user_id']] = $rs_user['TX_user_seudonimo'];
		}
		return $raw_user;
	}
	public function read_option () {
		$link = conexion();
		$qry_option = $link->query("SELECT AI_opcion_id, TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
		$raw_option = array();
		while($rs_option=$qry_option->fetch_array(MYSQLI_ASSOC)) {
			$raw_option[$rs_option['TX_opcion_titulo']] = $rs_option['TX_opcion_value'];
		}
		return $raw_option;
	}
}
$r_function = new recurrent_function();

?>
