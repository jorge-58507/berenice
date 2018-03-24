<?php
require '../../bh_con.php';
$link = conexion();

function edit_quote($str){
$pat = array("\"", "'", "º", "laremun");
$rep = array("''", "\'", "°", "#");
return $n_str = str_replace($pat, $rep, $str);
}

$value=edit_quote($_GET['a']);
//echo $value;
$type=$_GET['b'];


$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_product="SELECT * FROM bh_producto WHERE ";
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

$txt_product=$txt_product." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_product=$txt_product."TX_producto_referencia LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_product=$txt_product."TX_producto_referencia LIKE '%{$arr_value[$it]}%' AND ";
	}
}


$qry_product=mysql_query($txt_product." ORDER BY TX_producto_value ASC LIMIT 50");
$rs_product=mysql_fetch_assoc($qry_product);

$nr_product=mysql_num_rows($qry_product);



       if($type != "select"){
			?>


	<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
	<thead class="bg-primary">
	<tr>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
		<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Referencia</th>
    <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Nombre</th>
		<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
		<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
    <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
    <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
    <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
	</tr>
	</thead>
	<tfoot class="bg-primary">
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	</tfoot>
	<tbody>
	<?php
    do{
			$font_color='#000'; $title='';
			if ($rs_product['TX_producto_activo'] === '1'){ $font_color = '#fb1414'; $title='INACTIVO'; } ?>
    	<tr ondblclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');"  style="color:<?php echo $font_color ?>" title="<?php echo $title; ?>">
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
					<?php
					$qry_precio=mysql_query("SELECT * FROM bh_precio WHERE precio_AI_producto_id = '{$rs_product['AI_producto_id']}' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC LIMIT 1", $link);
					$rs_precio=mysql_fetch_assoc($qry_precio);
					echo $rs_precio['TX_precio_cuatro'];
					?>

				</td>
        <td>
        <button type="button" class="btn btn-success" onclick="open_popup('popup_relacion.php?a=<?php echo $rs_product['AI_producto_id'] ?>','popup_relacion','500','491')">
		<i class="fa fa-rotate-right" aria-hidden="true"></i>
		<?php echo $rs_product['TX_producto_rotacion']; ?></button>
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
    </tbody>
    </table>

<?php
	   }
?>
