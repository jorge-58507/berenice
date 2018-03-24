// JavaScript Document

function open_product2purchase(field){
	open_popup('popup_product2purchase.php?a='+field+'', 'popup_product2purchase','985','461');
}

function open_addprovider(){
			provider_id = $("#txt_filterprovider").prop("alt");
			if (provider_id === '') {
				var value = $("#txt_filterprovider").val();
				open_popup('popup_addprovider.php?a='+value,'popup_addprovider','460','463');
			} else {
				open_popup('popup_updprovider.php?a='+provider_id,'popup_addprovider','460','463');
			}
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
