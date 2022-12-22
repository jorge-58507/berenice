<?php
require '../../bh_conexion.php';
$link = conexion();

$producto_id=$_GET['a'];
$medida_id=$_GET['b'];

$qry_precio=$link->query("SELECT AI_precio_id, TX_precio_uno,TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco FROM bh_precio WHERE precio_AI_producto_id = '$producto_id' AND precio_AI_medida_id = '$medida_id' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC");
$nr_precio =	$qry_precio->num_rows;

?>
<label class="label label_blue_sky"  for="input_price">Precio:</label>
<?php
$rs_precio=$qry_precio->fetch_array(MYSQLI_ASSOC);
if($nr_precio > 0){
if($rs_precio['TX_precio_cuatro'] === '0' || $rs_precio['TX_precio_cuatro'] === '' || $rs_precio['TX_precio_cuatro'] === '0.00'){
?>		<input type="text" name="input_price" id="input_price" class="form-control" /><?php
}else{ ?>
  <select id="input_price" name="input_price" class="form-control">
<?php if($rs_precio['TX_precio_uno'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_uno'] ?>"><?php echo $rs_precio['TX_precio_uno'] ?></option>
<?php }
  if($rs_precio['TX_precio_dos'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_dos'] ?>"><?php echo $rs_precio['TX_precio_dos'] ?></option>
<?php }
  if($rs_precio['TX_precio_tres'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_tres'] ?>"><?php echo $rs_precio['TX_precio_tres'] ?></option>
<?php }
  if($rs_precio['TX_precio_cuatro'] > 0){ ?>
<option value="<?php echo $rs_precio['TX_precio_cuatro'] ?>" selected="selected">Regular: <?php echo $rs_precio['TX_precio_cuatro'] ?></option>
<?php }
  if($rs_precio['TX_precio_cinco'] > 0){ ?>
<option value="<?php echo $rs_precio['TX_precio_cinco'] ?>"><?php echo $rs_precio['TX_precio_cinco'] ?></option>
<?php } ?>
</select>
<?php }
}else{
?> 		<input type="text" name="input_price" id="input_price" class="form-control" onblur="this.value = val_intw2dec(this.value)" /><?php
} ?>
