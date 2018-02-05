<?php
require 'bh_conexion.php';
$link=conexion();


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SQL BH_PRODUCTO</title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/stock_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/stock_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/jquery.cookie.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	function free_singlequote(str){
//		var pat = ['/'/'];
			rep = ['\\\''];
		return value = str.replace(/'/,rep);
	}
	$("#btn_process").click(function(){
		var codigo_value = $("#ta_codigo").val();
			raw_code = new Object();
			i=1;
			arr_code = [];
			arr_code = codigo_value.split("\n");
			for (var value in arr_code) {
				raw_code[i] = arr_code[value];
				i++;
			}
			json_code = JSON.stringify(raw_code);

			$.ajax({	data: {"a" : json_code },	type: "GET",	dataType: "text",	url: "qry_4_del_code.php", })
			 .done(function( data, textStatus, jqXHR ) {
				 console.log("good "+data);
				})
			 .fail(function( jqXHR, textStatus, errorThrown ) {		});



// 		var nombre_value = $("#ta_nombre").val();
// 			arr_nombre = [];
// 			arr_nombre = nombre_value.split("\n");
// 			nombre_length = arr_nombre.length;
//
// 		var ref_value = $("#ta_ref").val();
// 			arr_ref = [];
// 			arr_ref = ref_value.split("\n");
// 			ref_length = arr_ref.length;
//
// 			if(code_length != nombre_length){
// 				alert("no tienen misma amplitud"+code_length+" / "+nombre_length);
// 				return false;
// 			}
// //INSERT INTO `bh_producto` (`AI_producto_id`, `TX_producto_codigo`, `TX_producto_value`, `TX_producto_medida`, `TX_producto_cantidad`, `TX_producto_minimo`, `TX_producto_maximo`, `TX_producto_rotacion`, `TX_producto_exento`, `TX_producto_alarma`, `TX_producto_activo`) VALUES
// //(NULL, '585858', 'PRUEBA', 'UNIDADES', '999', '1', '50', '2', '7', '0', '0'),
// //(NULL, '696969', 'PRUEBA 2', 'UNIDADES', '999', '1', '50', '2', '7', '0', '0')
// 			var resultado_value = "INSERT INTO `bh_producto` (TX_producto_codigo,TX_producto_value,TX_producto_medida,TX_producto_cantidad,TX_producto_minimo,TX_producto_maximo,TX_producto_rotacion,TX_producto_exento,TX_producto_alarma,TX_producto_activo) VALUES ";
// 			for(i=0;i<code_length;i++){
// 				if(arr_code[i].length === 6){
// 					var code = 	"0000000"+arr_code[i];
// 					console.log(arr_code[i].length);
// 				}else{
// 					var code = 	arr_code[i];
// 					console.log(arr_code[i].length);
// 				}
//
// 				resultado_value = resultado_value+"('"+code+"', '"+arr_nombre[i]+"', 'UNIDADES', '999', '5', '50', '50', '7', '0', '0'),"
// 			}


			$("#ta_resultado").val(arr_code);

	})


});

</script>

</head>

<body>

<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	<label>Codigos</label>
    <textarea id="ta_codigo" class="form-control"></textarea>
</div>

<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	<label>Nombre</label>
    <textarea id="ta_nombre" class="form-control"></textarea>
</div>
<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	<label>Referencia</label>
    <textarea id="ta_ref" class="form-control"></textarea>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button id="btn_process" class="btn btn-success">Procesar</button>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label>Resultado</label>
    <textarea id="ta_resultado" class="form-control"></textarea>
</div>


</body>
</html>
