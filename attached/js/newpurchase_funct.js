// JavaScript Document

function open_product2purchase(field){
	open_popup('popup_product2purchase.php?a='+field+'', 'popup_product2purchase','985','461');
}

function open_addprovider(field){
	open_popup('popup_addprovider.php','popup_addprovider','425','420');
}
var limit_txtobservation= 200;
var val_txtobservation= "";
function chk_txtobservation(value){
	if(value.length > limit_txtobservation){
		$("#ta_observation").val(val_txtobservation);
	}else{
		val_txtobservation=value;
	};
}

function upd_newpurchase_price(f){
	id = f.id;
	new_price = prompt("Ingrese la Nueva Cantidad:");
	new_price = val_intw2dec(new_price);
	ans = val_intwdec(new_price);
	if (!ans) {		return false;	}
	new_price = parseFloat(new_price);
	$.ajax({	data: {"a" : id, "b" : new_price }, type: "GET", dataType: "text", url: "attached/get/upd_newpurchase_price.php",	})
	.done(function( data, textStatus, jqXHR ) {
		$("#container_tblnewentry").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
