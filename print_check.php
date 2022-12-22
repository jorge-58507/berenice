<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
 	$qry_cheque = $link->query("SELECT bh_cheque.AI_cheque_id, bh_cheque.TX_cheque_numero, bh_cheque.TX_cheque_fecha, bh_cheque.TX_cheque_monto, bh_cheque.TX_cheque_montoletra, bh_cheque.TX_cheque_observacion, bh_proveedor.TX_proveedor_nombre
		FROM (bh_cheque INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cheque.cheque_AI_proveedor_id)
		WHERE AI_cheque_id = '{$_GET['a']}'")or die($link->error);
	if ($qry_cheque->num_rows < 1) {
		$qry_cheque = $link->query("SELECT bh_cheque.AI_cheque_id,bh_cheque.TX_cheque_numero,bh_cheque.TX_cheque_monto,bh_cheque.TX_cheque_montoletra,bh_cheque.TX_cheque_observacion, bh_proveedor.TX_proveedor_nombre
			FROM ((bh_cheque INNER JOIN bh_cpp ON bh_cpp.AI_cpp_id = bh_cheque.cheque_AI_cpp_id)
			INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cpp.cpp_AI_proveedor_id)
			WHERE AI_cheque_id = '{$_GET['a']}'")or die($link->error);
	}
	$rs_cheque = $qry_cheque->fetch_array(MYSQLI_ASSOC);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cheque: <?php echo $rs_cheque['TX_cheque_numero']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="attached/js/jquery.js"></script>

</head>

<body style="font-family:Arial">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
?>
<table id="tbl_check" cellpadding="0" cellspacing="0" border="0" style="height:585px; width:1263px; font-size:11.25pt;">
<tr style="height:11px">
<td width="10%"><img src="attached\image\imagen-cheque-formato.jpg" width='1263px' style="position: absolute; z-index: -2; margin-top: -2px; display: none;"></img></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
</tr>
<tr style="height:79px;" align="right">
	<td valign="top" colspan="10" class="optmayuscula" style="vertical-align: bottom;">
		<div id="print_fontpaper" style="width:848px; float:left; text-align: left;">&nbsp;&nbsp;
			<div class="no_print" style="position: fixed; display: flex; flex-direction: column; left: 80px;">
				<span class="no_print" id="span_fontsize" style="background-color: #ccc6; border: solid 1px #ccc; margin: 2px 0; border-radius: 5px;"><strong>Tama&ntilde;o:</strong> 11</span>
				<div class="no_print" >
					<button type="button" class="squared_button" onclick="increment_font()" name="button" class=""><i class="fa fa-arrow-circle-up"></i></button>
					<button type="button" class="squared_button" onclick="reduce_font()" name="button" class=""><i class="fa fa-arrow-circle-down"></i></button>
					<div class="" style="display: flex; flex-direction: column;">
						<button type="button" name="button" onclick="print_rail()" style="border: solid 1px #ccc; border-radius: 5px; margin: 5px 0;">Imp. Riel</button>
						<button type="button" name="button" onclick="print_tray()" style="border: solid 1px #ccc; border-radius: 5px;">Imp. Bandeja</button>
					</div>
				</div>
			</div>
		</div>
		<div id="check_fecha" style="width:345px; float:left; letter-spacing: 28px;"><?php echo date('dmY',strtotime($rs_cheque['TX_cheque_fecha'])); ?></div>
  </td>
</tr>
<tr style="height:59px" align="center">
	<td valign="top" colspan="10" style="vertical-align:bottom;">
		<div style="width:191px; float:left;"> &nbsp;</div>
		<div id="check_provider_name" onclick="set_provider_name('<?php echo substr($rs_cheque['TX_proveedor_nombre'],0,41); ?>')" style="width:692px; float:left; letter-spacing: 5px;">
			<span id="container_name">
				**<?php echo $rs_cheque['TX_proveedor_nombre']; ?>**
			</span>
		</div>
		<div id="check_amount" style="width:272px; float:left; padding-top:10px; letter-spacing: 5px; text-align:right;">
			<span id="container_amount">
				<?php echo number_format($rs_cheque['TX_cheque_monto'],2); ?>
			</span>
		</div>
  </td>
</tr>
<tr style="height:25px" align="center">
	<td valign="bottom" colspan="10">
		<div style="width:193px; float:left;"> &nbsp;</div>
		<div  id="check_letter" style="width:878px; float:left; letter-spacing: 5px;">
			<span id="container_letter">
				**<?php echo $rs_cheque['TX_cheque_montoletra']; ?>**
			</span>
		</div>
  </td>
</tr>
<tr style="height:230px">
	<td valign="top" colspan="10">&nbsp;</td>
</tr>
<tr style="height:188px">
	<td valign="top" colspan="10">
		<div id="comment_fecha" style="width:193px; float:left; text-align:right; letter-spacing: 5px;"><?php echo date('d-M-y',strtotime($fecha_actual)); ?></div>
		<div id="comment_observation" style="width:629px; float:left; letter-spacing: 5px; padding:0 28px;"><?php echo str_replace("&nolger;","<br />",$rs_cheque['TX_cheque_observacion']); ?></div>
		<div style="width:163px; float:left;">&nbsp;</div>
		<div id="comment_amount" style="width:181px; float:left; letter-spacing: 5px; text-align:right;"><?php echo $rs_cheque['TX_cheque_monto']; ?>&nbsp;&nbsp;</div>
  </td>
</tr>
</table>

<script type="text/javascript">
	var font_size = parseFloat($('#tbl_check').css('font-size'));
	document.getElementById("span_fontsize").innerHTML = `<strong>Tama&ntilde;o:</strong> ${font_size}`;

	function set_provider_name (provider) {
		var provider_name = prompt('Ingrese los Datos:',provider);
		if (provider_name != '' && provider_name === null) {
			console.log("esta vacio"); return false;
		}
		provider_name = "**"+provider_name+"**";
		document.getElementById('check_provider_name').innerHTML = `<span id="container_name">${provider_name}</span>`;
		$("#container_name").css('font-size',font_size);
		check_longitude();
	}
	function check_longitude (){
		var span_width = {"name" : $("#container_name").css('width'), "amount" : $("#container_amount").css('width'), "letter" : $("#container_letter").css('width')};
		var span_height = {"name" : $("#container_name").css('height'), "amount" : $("#container_amount").css('height'), "letter" : $("#container_letter").css('height')};
		const array_width = { "name" : 680, "amount" : 180, "letter" : 860 }
		for (var a in span_width) {
			if (parseFloat(span_width[a]) > array_width[a]) {
				reduce_font();
				break;
			}
		}
		for (var a in span_height) {
			if (parseFloat(span_height[a]) > 18) {
				reduce_font();
				break;
			}
		}
	}
	function reduce_font () {
		console.log("Reduciendo");
		var array_div = ['container_name','container_amount','container_letter'];
		font_size = font_size-0.5;
		for (var a in array_div) {
			$("#"+array_div[a]).css('font-size',font_size+'px');
		}
		document.getElementById("span_fontsize").innerHTML = `<strong>Tama&ntilde;o:</strong> ${font_size}`;
		check_longitude();
	}
	function increment_font () {
		console.log("Incrementando");
		var array_div = ['container_name','container_amount','container_letter'];
		font_size = font_size+0.5;
		for (var a in array_div) {
			$("#"+array_div[a]).css('font-size',font_size+'px');
		}
		document.getElementById("span_fontsize").innerHTML = `<strong>Tama&ntilde;o:</strong> ${font_size}`;
		check_longitude();
	}
	function print_tray () {
		document.getElementById('print_fontpaper').style.width = '848px';
		window.print();
	}
	function print_rail () {
		document.getElementById('print_fontpaper').style.width = '825px';
		window.print();
	}

</script>
</body>
</html>
