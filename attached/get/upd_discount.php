<?php
require '../../bh_conexion.php';
$link = conexion();

$facturaventa_id=$_GET['b'];
$percent=$_GET['c'];

$txt_facturaventa="SELECT
bh_datoventa.AI_datoventa_id
FROM ((bh_facturaventa
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
WHERE";
?>
<?php
$str_factid = $_GET['b'];
$arr_factid = explode(",",$str_factid);


$arr_length=count($arr_factid);
for($it=0;$it<$arr_length;$it++){
	if($it==$arr_length-1){
	$txt_facturaventa=$txt_facturaventa." AI_facturaventa_id = '$arr_factid[$it]'";
	}
	else{
	$txt_facturaventa=$txt_facturaventa." AI_facturaventa_id = '$arr_factid[$it]' OR";
	}
}
$qry_facturaventa=$link->query($txt_facturaventa);
$nr_facturaventa=$qry_facturaventa->num_rows;
$rs_facturaventa=$qry_facturaventa->fetch_assoc();
do{
	$link->query("UPDATE bh_datoventa SET TX_datoventa_descuento='$percent' WHERE AI_datoventa_id = '{$rs_facturaventa['AI_datoventa_id']}'");
}while($rs_facturaventa=$qry_facturaventa->fetch_assoc());
echo json_encode(['status'=>'success']);
?>
