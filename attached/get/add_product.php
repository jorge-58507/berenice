<?php
require '../../bh_con.php';
$link = conexion();
function edit_quote($str){
$pat = array("\"", "'", "º", "laremun");
$rep = array("''", "\'", "&deg;", "#");
return $n_str = str_replace($pat, $rep, $str);
}

$codigo=$_GET['a'];
$value=edit_quote($_GET['b']);
$medida=$_GET['c'];
$cantidad=$_GET['d'];
$minimo=$_GET['f'];
$maximo=$_GET['e'];

$exento=$_GET['g'];
$p_5=$_GET['h'];
$p_4=$_GET['i'];
$p_3=$_GET['j'];
$p_2=$_GET['k'];
$p_1=$_GET['l'];

$referencia=$_GET['m'];
$letra=$_GET['n'];

$fecha_actual=date('Y-m-d');
	
	$qry_checkproduct=mysql_query("SELECT * FROM bh_producto WHERE TX_producto_codigo = '$codigo'");
	$nr_checkproduct=mysql_num_rows($qry_checkproduct);
	if($nr_checkproduct < 1){
		$bh_insert="INSERT INTO bh_producto (TX_producto_codigo, TX_producto_value, TX_producto_medida, TX_producto_cantidad, TX_producto_minimo, TX_producto_maximo, TX_producto_exento, TX_producto_referencia, producto_AI_letra_id) VALUES ('$codigo','$value','$medida','$cantidad','$minimo','$maximo','$exento','$referencia','$letra')";
		mysql_query($bh_insert, $link) or die(mysql_error());

		$rs = mysql_query("SELECT MAX(AI_producto_id) AS id FROM bh_producto");
	if ($row = mysql_fetch_row($rs)) {
		$lastid = trim($row[0]);
	}
		$bh_insprecio="INSERT INTO bh_precio (precio_AI_producto_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco, TX_precio_fecha) VALUES ('$lastid','$p_1','$p_2','$p_3','$p_4','$p_5','$fecha_actual')";
		mysql_query($bh_insprecio, $link) or die(mysql_error());

// #################################   ANSWER     ##################

		$qry_product=mysql_query("SELECT * FROM bh_producto LIMIT 50", $link);
		$rs_product=mysql_fetch_assoc($qry_product);
		$nr_product=mysql_num_rows($qry_product);

?>
	<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
	<thead class="bg-primary">
	<tr>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
        <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Nombre</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
	</tr>
	</thead>
	<tfoot class="bg-primary">
	<tr>	<td></td><td></td><td></td><td></td><td></td><td></td>	</tr>
	</tfoot>
	<tbody>
	<?php
	if($nr_product > 0){
    do{
        ?>
    <tr>
        <td><?php echo $rs_product['TX_producto_codigo'] ?></td>
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
	}else{
	?>
	<tr>	<td></td><td></td><td></td><td></td><td></td><td></td>	</tr>
	<?php
	}?>
    </tbody>
    </table>
<?php
	}else{
		echo "<center><strong>
No se agrego el producto, El codigo se encuentra repetido.
</strong></center>";
	}

?>
