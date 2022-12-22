


 <?php 	$raw_notification = $r_function->method_message('read');
        $unread = 0;
        foreach ($raw_notification as $key => $value) { if ($value['TX_mensaje_activo'] == 1) { $unread++; }		} ?>
 				<button type="button" name="button" id="btn_notification" class="btn" onclick="toggle_notification();"><i id="i_notification" class="fa fa-user-o fa-2x"></i>
 <?php 		if ($unread > 0) { 	?>
 						<span id="span_notification" class="badge"><?php echo $unread; ?></span>
 <?php			} 									?>
 				</button>
 				<div id="div_notification" class="notification_min">
 <?php 		 foreach ($raw_notification as $key => $notification) {		$class_active = ($notification['TX_mensaje_activo'] == 1) ? 'active' : ''; ?>
 						<div id="notification_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
 							<div id="" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 notification_title <?php  echo $class_active; ?>">
 								<?php echo $notification['TX_mensaje_titulo'].' - '.$notification['TX_mensaje_fecha'].' ('.$notification['TX_mensaje_hora'].')'; ?>
 							</div>
 							<div id="notification_value" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 notification_content <?php  echo $class_active; ?>">
 								<?php echo $r_function->replace_special_character($notification['TX_mensaje_value']); ?>
 							</div>
 						</div>
 <?php			} ?>
 				</div>

<script type="text/javascript">
  function toggle_notification () {
    $("#div_notification").toggleClass('notification_min');
    $("#div_notification").toggleClass('notification_max');
    // $(".notification_title").removeClass('active');
    // $(".notification_content").removeClass('active');
    $("#span_notification").addClass('display_none');
    // actualizar status notificacion
    data = {"a":'update_status'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/method_message.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        console.log(text);
      });
    });

  }
</script>
