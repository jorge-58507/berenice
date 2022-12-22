<?php
include_once '../../bh_conexion.php';
$link=conexion();
$qry_printer = $link->query("SELECT *  FROM bh_impresora");
?>
<script type="text/javascript">
  $('#txt_medida_description').validCampoFranz('.0123456789');
</script>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">

  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 py_14">
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     <label for="txt_printer_serial" class="label label_blue_sky">Serial</label>
     <input type="text" name="" id="txt_printer_serial" class="form-control"  alt="" value="" placeholder="" />
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     <label for="txt_printer_client" class="label label_blue_sky">Cliente</label>
     <input type="text" name="" id="txt_printer_client" class="form-control" value="" placeholder="Host" />
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     <label for="txt_printer_seudonim" class="label label_blue_sky">Nombre</label>
     <input type="text" name="" id="txt_printer_seudonim" class="form-control" value="" placeholder="Seudonimo" />
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 py_14">
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     <label for="txt_printer_recipient" class="label label_blue_sky">Recipiente</label>
     <input type="text" name="" id="txt_printer_recipient" class="form-control" value="" placeholder="//HOST/Carpeta/" />
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     <label for="txt_printer_return" class="label label_blue_sky">Retorno</label>
     <input type="text" name="" id="txt_printer_return" class="form-control" value="" placeholder="//HOST/Return/" />
    </div>

    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
     <label for="txt_printer_till" class="label label_blue_sky">Caja Registradora</label>
     <input type="text" name="" id="txt_printer_till" class="form-control" value="" placeholder="\\HOST\Impresora" />
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center pt_7">
      <button type="button" onclick="save_printer()" class="btn btn-success" name="button"><strong><i class="fa fa-save"></i> Guardar</strong></button>
    </div>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <div id="tbl_area" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table table_hovered">
      <div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding br_1">RECIPIENTE</div>
          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell no_padding br_1">RETORNO</div>
          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell no_padding br_1">CAJA R.</div>
        </div>
      </div>
      <div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
<?php   while ($rs_printer = $qry_printer->fetch_array(MYSQLI_ASSOC)) {   ?>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell br_1 bg-info al_left">
              <?php echo "<span class='font_bolder'>".$rs_printer['TX_impresora_cliente'].":</span> ".$rs_printer['TX_impresora_serial']." - ".$rs_printer['TX_impresora_seudonimo']; ?>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell br_1 al_center"><?php echo $rs_printer['TX_impresora_recipiente']; ?></div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell br_1 al_center"><?php echo $rs_printer['TX_impresora_retorno'] ?></div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell br_1 al_center"><?php echo $rs_printer['TX_impresora_cajaregistradora'] ?></div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center">
              <button type="button" class="btn btn-warning btn-sm btn_squared_sm" onclick="set_printer('<?php echo $rs_printer['AI_impresora_id'] ?>');" name="button"><i class="fa fa-wrench"></i></button>
              <button type="button" class="btn btn-danger btn-sm btn_squared_sm" onclick="del_printer('<?php echo $rs_printer['AI_impresora_id'] ?>');" name="button"><i class="fa fa-times"></i></button>
            </div>

          </div>
<?php   } ?>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
    </div>
  </div>
</div>
