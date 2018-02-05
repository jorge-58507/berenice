<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
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

$qry_product=mysql_query($txt_product." ORDER BY TX_producto_value ASC ".$line_limit);
$rs_product=mysql_fetch_assoc($qry_product)or die (mysql_error());

$nr_product=mysql_num_rows($qry_product);
?>
    <table id="tbl_product" class="table table-bordered table-hover table-striped">
    <caption>Lista de Productos:</caption>
    <thead class="bg-primary">
    	<tr>
        	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
            	Codigo
            </th>
            <th class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
            	Nombre
            </th>
        	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
            	Cantidad
            </th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            	Precio
            </th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            	Letra
            </th>
        </tr>
    </thead>
    <tfoot class="bg-primary">
	    <tr>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    	</tr>
    </tfoot>
    <tbody>
    <?php
	if($nr_product > 0){
	do{
	?>
    	<tr onclick="javascript:open_product2sell(<?php echo $rs_product['AI_producto_id']; ?>);">
        	<td title="<?php echo $rs_product['AI_producto_id']; ?>">
            <?php echo $rs_product['TX_producto_codigo']; ?>
            </td>
        	<td>
            <?php echo $rs_product['TX_producto_value']; ?>
            </td>
        	<td>
            <?php echo $rs_product['TX_producto_cantidad']; ?>
            </td>
        	<td>
            <?php
			$rs_precio=mysql_fetch_array(mysql_query("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = '{$rs_product['AI_producto_id']}' AND TX_precio_inactivo = '0'"));
			echo $rs_precio['TX_precio_cuatro']; ?>
            </td>
        	<td>
            <?php
			$rs_letra=mysql_fetch_array(mysql_query("SELECT bh_letra.TX_letra_value FROM bh_letra, bh_producto WHERE bh_letra.AI_letra_id = '{$rs_product['producto_AI_letra_id']}'"));
			echo $rs_letra['TX_letra_value']; ?>
            </td>
        </tr>
    <?php }while($rs_product=mysql_fetch_assoc($qry_product)); ?>
<?php
}else{
?>
	    <tr>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    	</tr>
<?php
}
?>
    </tbody>
    </table>
