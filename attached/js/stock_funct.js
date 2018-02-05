// JavaScript Document

function openpopup_newentry(product_id){
	popup = window.open("popup_newentry.php", "popup_newentry", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=600');
}
function openpopup_updproduct(product_id){
  open_popup('popup_updproduct.php?a=' + product_id + '', 'popup', '1010', '580');
}
function new_exit(product_id, product_cantidad){
	var str = prompt("Indique la cantidad:");
		ans = val_intwdec(str);
	if (ans) {
		if(product_cantidad >= str){
			new upd_cant(product_id, str);
		}else{
			alert("El monto es demasiado alto");
			return false;
		}
	}

}
