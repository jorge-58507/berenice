<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$value=$r_function->replace_regular_character($_GET['term']);
$date_i = date('Y-m-d',strtotime('-12 week'));
$date_f = date('Y-m-d');

$prep_cliente_datoventa=$link->prepare("SELECT TX_datoventa_descripcion, count(TX_datoventa_descripcion) as conteo_descripcion, datoventa_AI_producto_id, TX_datoventa_precio FROM bh_datoventa INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id WHERE facturaventa_AI_cliente_id = ? AND bh_facturaventa.TX_facturaventa_fecha >= ? AND bh_facturaventa.TX_facturaventa_fecha <= ? GROUP BY TX_datoventa_descripcion ORDER BY conteo_descripcion DESC LIMIT 8");
$prep_cliente_asiduo=$link->prepare("SELECT AI_facturaventa_id, facturaventa_AI_facturaf_id FROM bh_facturaventa WHERE facturaventa_AI_cliente_id = ?")or die($link->error);

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_client="SELECT * FROM bh_cliente WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_client=$txt_client."TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_client=$txt_client."TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}
$qry_client=$link->query($txt_client." ORDER BY TX_cliente_nombre ASC LIMIT 10")or die($link->error);
$raw_cliente = array();
$i=0;
while($rs_cliente=$qry_client->fetch_array(MYSQLI_ASSOC)){
	if (substr_count($rs_cliente['TX_cliente_nombre'], 'NO USAR') < 1 ) {
		$prep_cliente_asiduo->bind_param("i",$rs_cliente['AI_cliente_id']); $prep_cliente_asiduo->execute(); $qry_cliente_asiduo = $prep_cliente_asiduo->get_result();
		$counter=0;
		while($rs_cliente_asiduo=$qry_cliente_asiduo->fetch_array()){
			if ($rs_cliente_asiduo['facturaventa_AI_facturaf_id'] != '') {
				$counter++;
			}
		}
		$asiduo = ($counter < 1) ? '0' : ($counter*100)/$qry_cliente_asiduo->num_rows;
		$prep_cliente_datoventa->bind_param("iss",$rs_cliente['AI_cliente_id'],$date_i,$date_f); $prep_cliente_datoventa->execute(); $qry_cliente_datoventa = $prep_cliente_datoventa->get_result();
		$raw_favorito=array();
		while ($rs_cliente_datoventa=$qry_cliente_datoventa->fetch_array(MYSQLI_ASSOC)) {
			$raw_favorito[]=$rs_cliente_datoventa;
		}

		$raw_cliente[$i]['id'] = $rs_cliente['AI_cliente_id'];
		$raw_cliente[$i]['value'] = $r_function->replace_special_character($rs_cliente['TX_cliente_nombre'])." | Dir: ".$rs_cliente['TX_cliente_direccion'];
		$raw_cliente[$i]['telefono'] = $rs_cliente['TX_cliente_telefono'];
		$raw_cliente[$i]['direccion'] = $rs_cliente['TX_cliente_direccion'];
		$raw_cliente[$i]['ruc'] = $rs_cliente['TX_cliente_cif'];
		$raw_cliente[$i]['json_favorito'] = json_encode($raw_favorito);
		$raw_cliente[$i]['asiduo'] = round($asiduo)."%";
		$i++;
	}
}


echo json_encode($raw_cliente);
?>
