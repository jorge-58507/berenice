<?php
require '../../bh_con.php';
$link = conexion();

$qry_product=mysql_query("SELECT * FROM bh_producto WHERE TX_producto_activo = '1'");
$rs_product=mysql_fetch_assoc($qry_product);
$nr_product=mysql_num_rows($qry_product);


?>


	<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
	<thead class="bg-primary">
	<tr>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Referencia</th>
        <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Nombre</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
	</tr>
	</thead>
	<tfoot class="bg-primary">
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	</tfoot>
	<tbody>
	<?php
    if($nr_product=mysql_num_rows($qry_product) > '0'){
	?>
	<?php
    do{
	?>
    <tr>
        <td><?php echo $rs_product['TX_producto_codigo'] ?></td>
        <td><?php echo $rs_product['TX_producto_referencia'] ?></td>
        <td><?php echo $rs_product['TX_producto_value'] ?></td>
        <td>
        <?php 
        if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
            echo '<font style="color:#51AA51">'.$rs_product['TX_producto_cantidad'].'</font>';
        }elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
            echo '<font style="color:#C63632">'.$rs_product['TX_producto_cantidad'].'</font>';
        }else{
            echo '<font style="color:#000000">'.$rs_product['TX_producto_cantidad'].'</font>';
        }
        ?>
        </td>
        <td>
<button type="button" name="btn_reg_exit" id="btn_reg_exit" class="btn btn-success btn-sm" onclick="new_exit(<?php echo $rs_product['AI_producto_id'] ?>,<?php echo $rs_product['TX_producto_cantidad'] ?>);">
        Salida</button>
        </td>
        <td>
<button type="button" name="btn_upd_product" id="btn_upd_product" class="btn btn-warning btn-sm" onclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">
        Modificar</button>
        </td>
        <td>
        <?php 
		$qry_checkfacturaventa=mysql_query("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = '{$rs_product['AI_producto_id']}'");
		$nr_checkfacturaventa=mysql_num_rows($qry_checkfacturaventa);
		if($nr_checkfacturaventa < 1){
		 ?>
<button type="button" name="btn_del_product" id="btn_del_product" class="btn btn-danger btn-sm" onclick="del_product('<?php echo $rs_product['AI_producto_id'] ?>');">
        Eliminar</button>
        <?php } ?>
        </td>
    </tr>
	<?php
    }while($rs_product=mysql_fetch_assoc($qry_product));
    ?>
    <?php
	}else{
	?>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>  
    <?php		
	}
	?>
    </tbody>
    </table>

