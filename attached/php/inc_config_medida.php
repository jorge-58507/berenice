<?php
include_once '../../bh_conexion.php';
$link=conexion();
  $qry_medida = $link->query("SELECT AI_medida_id, medida_AI_user_id, TX_medida_value, bh_user.TX_user_seudonimo, count(bh_producto.TX_producto_medida) AS conteo
FROM ((bh_medida
INNER JOIN bh_user ON bh_user.AI_user_id = bh_medida.medida_AI_user_id)
left JOIN bh_producto ON bh_medida.AI_medida_id = bh_producto.TX_producto_medida)
 GROUP BY AI_medida_id
 ORDER BY TX_medida_value")or die($link->error);
?>
<script type="text/javascript">
  $('#txt_medida_description').validCampoFranz('.0123456789');

</script>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> &nbsp;  </div>
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 py_14">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
     <label for="txt_area_description" class="label label_blue_sky">Descripci&oacute;n</label>
     <input type="text" name="" id="txt_medida_description" class="form-control" value="" placeholder="Descripci&oacute;n" />
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_14">
      <button type="button" onclick="add_medida()" class="btn btn-success" name="button"><strong><i class="fa fa-save"></i> Agregar</strong></button>
    </div>
  </div>
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> &nbsp;  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">

    <div id="tbl_area" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table table_hovered">
      <div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell no_padding br_1">DESCRIPCION</div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">ABREV</div>
          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell no_padding br_1">USUARIO</div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">CANTIDAD</div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1"></div>
        </div>
      </div>
      <div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
    <?php
      while ($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)) {
        ?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell br_1 al_center"><?php echo $rs_medida['TX_medida_value'] ?></div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center"><?php echo substr($rs_medida['TX_medida_value'],0,3); ?></div>
          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell br_1 al_center"><?php echo $rs_medida['TX_user_seudonimo'] ?></div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center"><?php echo $rs_medida['conteo'] ?></div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center"></div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center">
            <button type="button" class="btn btn-danger btn-sm btn_squared_sm" onclick="des_medida('<?php echo $rs_medida['AI_medida_id'] ?>');" name="button"><i class="fa fa-times"></i></button>
          </div>
        </div>
<?php } ?>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
    </div>
  </div>
</div>
