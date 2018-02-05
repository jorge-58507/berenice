<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];


$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_client="SELECT bh_cliente.AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, SUM(bh_facturaf.TX_facturaf_deficit) AS deficit, SUM(bh_facturaf.TX_facturaf_subtotalni) AS subtotal_ni, SUM(bh_facturaf.TX_facturaf_subtotalci) AS subtotal_ci, SUM(bh_facturaf.TX_facturaf_total) AS total, SUM(bh_facturaf.TX_facturaf_impuesto) AS impuesto, SUM(bh_facturaf.TX_facturaf_descuento) AS descuento FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_client=$txt_client."TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_client=$txt_client."TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}
$txt_client.=" OR ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_client=$txt_client."TX_cliente_cif LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_client=$txt_client."TX_cliente_cif LIKE '%{$arr_value[$it]}%' AND ";
	}
}
//echo $txt_client;
$qry_client=mysql_query($txt_client." GROUP BY bh_facturaf.facturaf_AI_cliente_id ORDER BY TX_cliente_nombre ASC");
$rs_client=mysql_fetch_array($qry_client);

$nr_client=mysql_num_rows($qry_client);


if($nr_client > 0){

?>
	    <select id="sel_client" name="sel_client" class="form-control" size="4">
        	<?php do{ ?>
            <option value="<?php echo $rs_client['AI_cliente_id']; ?>" onclick="set_txtfilterclient(this,'<?php echo $rs_client['TX_cliente_cif']; ?>','<?php echo $rs_client['TX_cliente_telefono']; ?>','<?php echo round($rs_client['deficit'],2); ?>','<?php echo round($rs_client['subtotal_ci'],2); ?>','<?php echo round($rs_client['subtotal_ni'],2); ?>','<?php echo round($rs_client['total'],2); ?>','<?php echo round($rs_client['impuesto'],2); ?>','<?php echo round($rs_client['descuento'],2); ?>')"><?php
			 echo $rs_client['TX_cliente_nombre']; 
			 ?></option>
            <?php }while($rs_client=mysql_fetch_assoc($qry_client)); ?>
        </select>
<?php
			
}else{
	
?>
	    <select id="sel_client" name="sel_client" class="form-control" size="4">
            <option></option>
        </select>
<?php
	
}

?>

    
    
    
    

