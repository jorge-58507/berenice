// JavaScript Document

function open_product2sell(id){
	open_popup('popup_product2sell.php?a='+id+'', 'popup_product2sell','425','420');
}

function set_txtfilterclient(field){
	document.getElementById("txt_filterclient").value = field.text;
	document.getElementById("txt_filterclient").alt = field.value;
	$.ajax({	data: { "a" : field.value },	type: "GET",	dataType: "JSON",	url: "attached/get/get_client_info.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 content = '<strong>Nombre:</strong> '+data[field.value]['nombre']+' <strong>Tlf.</strong> '+data[field.value]['telefono']+' <strong>Dir.</strong> '+data[field.value]['direccion'].substr(0,20)+' <strong>RUC:</strong> '+data[field.value]['cif'];
		 fire_recall('container_client_recall', content)
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function upd_unidadesnuevaventa(product_id){
	var new_quantity = prompt("Ingrese la cantidad:");
	new_quantity = val_intw2dec(new_quantity);
	// if(new_quantity > stock_quantity){
		// var answer = confirm("El monto ingresado es mayor que el existente. ¿Desea Continuar?");
		// if(answer == true){
	upd_quantityproduct2sell(product_id,new_quantity);
	// }else{
	// 	upd_quantityproduct2sell(product_id,new_quantity);
	// }
}
function upd_precionuevaventa(product_id){
	$.ajax({ data: {"a" : "1"}, type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	if(data[0][0] != ""){
			var new_price = prompt("Ingrese el Nvo. Precio:");
			new_price = val_intw2dec(new_price);
			new_price = parseFloat(new_price);
			upd_priceproduct2sell(product_id,new_price);
		}	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function close_popup(){
	popup.close();
}

/*########################## FUNCTIONS FOR NEW_PAYDESK   #############################*/

function open_product2addpaydesk(id, facturaventa_id){
	popup = window.open("popup_product2addpaydesk.php?a="+id+"&b="+facturaventa_id+"", "popup_product2sell", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
}

// #############################

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
