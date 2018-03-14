<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$r_function->replace_regular_character($_GET['a']);
$line_limit = "";
if(!empty($_GET['b']) && $_GET['b'] != 'undefined'){
	$line_limit = "LIMIT ".$_GET['b'];
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.producto_AI_letra_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_cantidad FROM bh_producto WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' AND TX_producto_activo = '0'";
	}else{
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_product=$txt_product." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' AND TX_producto_activo = '0'";
	}else{
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$qry_precio = $link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0'")or die($link->error);
$qry_letra = $link->prepare("SELECT bh_letra.TX_letra_value FROM (bh_letra INNER JOIN bh_producto ON bh_letra.AI_letra_id = bh_producto.producto_AI_letra_id) WHERE bh_producto.AI_producto_id = ? ")or die($link->error);

$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC ".$line_limit)or die($link->error);
$raw_producto=array(); $i=0;
while ($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)) {
	$qry_precio->bind_param("i", $rs_product['AI_producto_id']); $qry_precio->execute(); $result = $qry_precio->get_result();
	$rs_precio=$result->fetch_array(MYSQLI_ASSOC);
	$qry_letra->bind_param("i", $rs_product['AI_producto_id']); $qry_letra->execute(); $result = $qry_letra->get_result();
	$rs_letra=$result->fetch_array(MYSQLI_ASSOC);

	$raw_producto[$i]=$rs_product;
	$raw_producto[$i]['precio']=$rs_precio['TX_precio_cuatro'];
	$raw_producto[$i]['letra']=(!empty($rs_letra['TX_letra_value'])) ? $rs_letra['TX_letra_value'] :  '';
	$i++;
};


			if($nr_product=$qry_product->num_rows > 0){
				foreach ($raw_producto as $key => $rs_product) {
?>
			    <tr onclick="javascript:open_product2sell(<?php echo $rs_product['AI_producto_id']; ?>);">
	        	<td title="<?php echo $rs_product['AI_producto_id']; ?>"><?php echo $rs_product['TX_producto_codigo']; ?></td>
	        	<td><?php echo $rs_product['TX_producto_value']; ?></td>
	        	<td><?php echo $rs_product['TX_producto_cantidad']; ?></td>
						<td><?php echo $rs_product['precio']; ?></td>
						<td><?php echo $rs_product['letra']; ?></td>
	        </tr>
<?php 	};
			}else{
?>
		    <tr>
	    		<td colspan="5">  </td>
	    	</tr>
<?php
			}
