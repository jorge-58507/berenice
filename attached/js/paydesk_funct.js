// JavaScript Document
function prueba(str){
	alert(str);
}
function upd_quantityonnewcollect(datoventa_id){
var new_quantity=prompt("Ingrese la nueva cantidad:");
	ans = val_intwdec(new_quantity);
	if(!ans){
		return false;
	}
	upd_quantityproduct2addcollect(datoventa_id,new_quantity,get("a"))
}

function  unset_filterclient(e){
	if (e.which === 13) {
		$("#btn_addclient").click();
	}else{
		$( "#txt_filterclient").prop("alt","");
	}
	$( function() {
		$( "#txt_filterclient").autocomplete({
			source: "attached/get/filter_client_sell.php",
			minLength: 2,
			select: function( event, ui ) {
				$("#txt_filterclient").prop('alt', ui.item.id);
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
				fire_recall('container_client_recall', content)
			}
		});
	});
}
