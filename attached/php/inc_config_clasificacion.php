<?php
  include_once '../../bh_conexion.php';
  $link=conexion();
  $qry_familia = $link->query("SELECT bh_familia.AI_familia_id, bh_familia.TX_familia_value, bh_familia.TX_familia_prefijo, bh_familia.TX_familia_status, count(bh_producto.producto_AI_subfamilia_id) as conteo
FROM ((bh_familia
LEFT JOIN bh_subfamilia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id)
LEFT JOIN bh_producto ON bh_subfamilia.AI_subfamilia_id = bh_producto.producto_AI_subfamilia_id)
 GROUP BY bh_familia.AI_familia_id
 ORDER BY bh_familia.TX_familia_value")or die($link->error);
 $qry_lastprefix = $link->query("SELECT TX_familia_prefijo FROM bh_familia ORDER BY AI_familia_id DESC LIMIT 1")or die($link->error);
 $rs_lastprefix = $qry_lastprefix->fetch_array(MYSQLI_ASSOC);
 $prefijo='000'.($rs_lastprefix['TX_familia_prefijo']+1);
?>
<script type="text/javascript">
</script>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> &nbsp;  </div>
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> &nbsp;  </div>

  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 no_padding">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 py_14 ">

        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 no_padding">
          <label for="txt_clasification_description" class="label label-primary">Familia de Productos</label>
          <input type="text" name="" id="txt_clasification_description" class="form-control" value="" />
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 px_7">
          <label for="txt_clasification_prefijo" class="label label-primary">Prefijo</label>
          <input type="text" name="" id="txt_clasification_prefijo" class="form-control" value="<?php echo substr($prefijo,-2); ?>" />
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 px_0 pt_14 al_center">
          <button type="button" onclick="add_familia()" class="btn btn-primary" name="button"><strong><i class="fa fa-save"></i> Crear</strong></button>
        </div>
      </div>
      <div id="tbl_familia" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table table_hovered">
        <div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell no_padding br_1">DESCRIPCION</div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">PREF</div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">CANT</div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell no_padding br_1"></div>
          </div>
        </div>
        <div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
      <?php
        while ($rs_familia = $qry_familia->fetch_array(MYSQLI_ASSOC)) {
          $background = ($rs_familia['TX_familia_status'] === '0') ? '#feadad' : '#fff';
          ?>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding" style="background-color:<?php echo $background; ?>;">
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell br_1 al_center">
              <button type="button" class="btn btn-link" onclick="set_subfamilia(<?php echo $rs_familia['AI_familia_id'] ?>,'<?php echo $r_function->replace_special_character($rs_familia['TX_familia_value']) ?>')" name="button"><?php echo $r_function->replace_special_character($rs_familia['TX_familia_value']) ?></button>
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center pt_7">
              <?php echo $rs_familia['TX_familia_prefijo']; ?>
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center pt_7">
              <?php echo $rs_familia['conteo']; ?>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell br_1 al_center pt_7">
              <button type="button" class="btn btn-success btn-sm btn_squared_sm" onclick="act_familia('<?php echo $rs_familia['AI_familia_id'] ?>');" name="button"><i class="fa fa-check"></i></button>
              &nbsp;
              <button type="button" class="btn btn-danger btn-sm btn_squared_sm" onclick="des_familia('<?php echo $rs_familia['AI_familia_id'] ?>');" name="button"><i class="fa fa-times"></i></button>
            </div>
          </div>
<?php     }  ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
      </div>
    </div>
    <!-- SUBFAMILIA  -->
    <div id="container_subfamilia" class="col-xs-12 col-sm-12 col-md-6 col-lg-6 no_padding display_none">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 pt_14">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
          <label for="txt_clasification_subfamilia" class="label label-info">Subfamilia</label>
          <input type="text" alt="" name="txt_clasification_subfamilia" id="txt_clasification_subfamilia" class="form-control" value="" placeholder="Descripci&oacute;n" />
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 px_7">
          <label for="txt_clasification_subprefijo" class="label label-info">Sub-Prefijo</label>
          <input type="text" name="txt_clasification_subprefijo" id="txt_clasification_subprefijo" class="form-control" value="" />
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pt_14 pr_0">
          <button type="button" onclick="add_subfamilia()" class="btn btn-info" name="button"><strong><i class="fa fa-save"></i> Agregar</strong></button>
        </div>
      </div>
      <div id="tbl_subfamilia" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table table_hovered px_0 pt_7">
        <div id="caption_subfamilia" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding caption">
        </div>
        <div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-info">
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell no_padding br_1">DESCRIPCION</div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">PREF</div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">CANT</div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell no_padding br_1"></div>
          </div>
        </div>
        <div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell br_1 al_center"></div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-info">&nbsp;</div>
      </div>
    </div>
  </div>
</div>
