<?php
include_once '../../bh_conexion.php';
$link=conexion();
  $qry_area = $link->query("SELECT bh_area.AI_area_id, bh_area.TX_area_value, bh_area.TX_area_status, bh_user.TX_user_seudonimo FROM bh_area INNER JOIN bh_user ON bh_user.AI_user_id = bh_area.area_AI_user_id ORDER BY TX_area_status DESC, TX_area_value ASC")or die($link->error);
?>
<script type="text/javascript">
  $('#txt_area_description').validCampoFranz('.0123456789');

</script>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> &nbsp;  </div>
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 py_14">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
     <label for="txt_area_description" class="label label_blue_sky">Descripci&oacute;n</label>
     <input type="text" name="" id="txt_area_description" class="form-control" value="" placeholder="Descripci&oacute;n" />
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_14">
      <button type="button" onclick="add_area()" class="btn btn-success" name="button"><strong><i class="fa fa-save"></i> Agregar</strong></button>
    </div>
  </div>
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> &nbsp;  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">

    <div id="tbl_area" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table table_hovered">
      <div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding br_1">DESCRIPCION</div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">USUARIO</div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">STATUS</div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1"></div>
        </div>
      </div>
      <div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
    <?php
      while ($rs_area = $qry_area->fetch_array(MYSQLI_ASSOC)) {
        if($rs_area['TX_area_status'] === '1') { $area_status = 'ACTIVA'; $font_color = '#51AA51';  }else{ $area_status = 'INACTIVA'; $font_color = '#c67250'; };
        ?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell br_1 al_center"><?php echo $rs_area['TX_area_value'] ?></div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center"><?php echo $rs_area['TX_user_seudonimo'] ?></div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center" style="color:<?php echo $font_color; ?>"><?php echo $area_status ?></div>
          <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center">
            <button type="button" class="btn btn-success btn-sm btn_squared_sm" onclick="act_area('<?php echo $rs_area['AI_area_id'] ?>');" name="button"><i class="fa fa-check"></i></button>
            &nbsp;
            <button type="button" class="btn btn-danger btn-sm btn_squared_sm" onclick="des_area('<?php echo $rs_area['AI_area_id'] ?>');" name="button"><i class="fa fa-times"></i></button>
          </div>
        </div>
<?php } ?>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
    </div>
  </div>
</div>
