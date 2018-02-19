<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$str_factid=$_GET['b'];


$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_product="SELECT bh_producto.AI_producto_id, bh_producto.producto_AI_letra_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_cantidad FROM bh_producto WHERE ";
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_product=$txt_product."TX_producto_value LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_product=$txt_product." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_product=$txt_product."TX_producto_codigo LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT 10");
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
$nr_product=$qry_product->num_rows;

$qry_letra=$link->prepare("SELECT bh_letra.TX_letra_value FROM bh_letra, bh_producto WHERE bh_letra.AI_letra_id = ?")or die($link->error);

$qry_precio=$link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ?")or die($link->error);

?>
    <table id="tbl_product" class="table table-bordered table-hover table-striped">
    <caption>Lista de Productos:</caption>
    <thead>
    	<tr class="bg-info">
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
          	Codigo
        </th>
        <th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
          	Nombre
        </th>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
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
    <tfoot>
	    <tr class="bg-info">
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
	do{ ?>
    	<tr onclick="open_product2addpaycollect('<?php echo $rs_product['AI_producto_id']; ?>','<?php echo $str_factid ?>');">
        	<td>
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
			$qry_precio->bind_param("i", $rs_product['AI_producto_id']); $qry_precio->execute(); $result=$qry_precio->get_result();
			$rs_precio = $result->fetch_array(MYSQLI_ASSOC);
			if($result->num_rows > 0){
				if (!empty($rs_precio['TX_precio_cuatro'])) {
          echo number_format($rs_precio['TX_precio_cuatro'],2);
        }else if(empty($rs_precio['TX_precio_cuatro'])) {
          echo number_format(0,2);
        }else{
          echo number_format(0,2);
        }
			}
			?>
            </td>
        	<td>
            <?php
			$qry_letra->bind_param("i", $rs_product['producto_AI_letra_id']); $qry_letra->execute(); $result=$qry_letra->get_result();
			$rs_letra=$result->fetch_array(MYSQLI_ASSOC);
			echo $rs_letra['TX_letra_value']; ?>
            </td>
        </tr>
    <?php
	}while($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC));
	}else{
	?>
	    <tr class="bg-info">
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

<?php
