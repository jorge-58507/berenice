// JavaScript Document
class class_payment
{

}
class class_collect
{
	constructor(total,payed){
		this.total = total;
		this.payed = payed;
	}

	
}

function upd_quantityonnewcollect(datoventa_id){
var new_quantity=prompt("Ingrese la nueva cantidad:");
	new_quantity = val_intw2dec(new_quantity);
	ans = val_intwdec(new_quantity);
	if(!ans){	return false;	}
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
	      var n_val = ui.item.value;
	      raw_n_val = n_val.split(" | Dir:");
	      ui.item.value = raw_n_val[0];
				$("#txt_filterclient").prop('alt', ui.item.id);
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
				fire_recall('container_client_recall', content)
				clean_payment($.get('a'),$.get('b'));
			}
		});
	});
}
