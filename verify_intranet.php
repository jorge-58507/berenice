<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');

function ObtenerIP(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
	$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	$ip = $_SERVER['REMOTE_ADDR'];
	else
	$ip = "IP desconocida";
	return($ip);
}
$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);

$arr_pc=['TRILLI001','TRILLI002','TRILLI003','TRILLI004','TRILLI005','TRILLI006','COTIZADOR','TPV4','TPV3','TPV2','TRIILLI-CAJA','Trilli2015','Servidor','SERVIDORFIRES','TRILLISA'];
$ans_client = in_array($cliente, $arr_pc);

$txt_vendor="SELECT TX_user_seudonimo, TX_user_password FROM bh_user WHERE TX_user_type = '3' AND TX_user_activo = '1'";
$qry_vendor=$link->query($txt_vendor)or die($link->error);
$vendor_array=array();	$i=0;
if ($ans_client) {
	while($rs_vendor=$qry_vendor->fetch_array()){
		$vendor_array[$i] = $rs_vendor;
		$i++;
	};
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>
<link href="attached/image/f_icono.ico" rel="shortcut icon" type="icon" />
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/index_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/login_funct.js"></script>
<script type="text/javascript" src="attached/js/jshash-2.2/sha1.js"></script>
<script type="text/javascript">
</script>
</head>
<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
	  <div id="logo" ></div>
		</div>
		<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
			<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
		</div>
	</div>
	<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<?php
$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
// $host_name='noexiste';
$qry_impresora = $link->query("SELECT AI_impresora_id, TX_impresora_retorno, TX_impresora_recipiente FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
$nr_impresora = $qry_impresora->num_rows;
if ($nr_impresora < 1) {
	echo "denied";
	return false;
}

$rs_impresora=$qry_impresora->fetch_array();
$impresora_id = $rs_impresora['AI_impresora_id'];
$recipiente = $rs_impresora['TX_impresora_recipiente'];
// $recipiente = "//noexiste/P_CAJA/";
// $recipiente = "//TPV3/docs trilli/";

// ############################# 								VERIFICAR SI HAY ACCESO A LA RED								########################
$retorno = $rs_impresora['TX_impresora_retorno'];
$replaced_recipiente = str_replace('/','\\',$recipiente);
if (!file_exists($replaced_recipiente)) {
    // if(!mkdir($recipiente, 0777, true)){
			echo "Querido $host_name, el recipiente $replaced_recipiente no es accesible.";
		// };
}else{
	echo "Querido $host_name, se estableci&oacute; la conexi&oacute;n con $replaced_recipiente.";
}

?>		
	</div>
<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?></div>
</div>

</div>
</body>
</html>
