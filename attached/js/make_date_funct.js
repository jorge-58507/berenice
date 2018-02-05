// JavaScript Document

function chk_pac_info(){	
	var no_empty_field = [];
	var no_empty_field = ['pac_ci','pac_nya','pac_edad','pac_h'];
	var num_empty_field = no_empty_field.length;
	for(it=0; it<num_empty_field; it++){ 
		if(isEmpty(no_empty_field[it])){
			setFocus(no_empty_field[it]);
			alert("Hay campos que no pueden estar vacio");
					return false;
		}
	}
	new add_pac_info_date();
}

function openPopup(field){
	var fecha = extraer_value("date_fecha");
	window.open(field.href+"?q="+fecha, field.name,         'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=0,alwaysRaised=1,dependent,titlebar=no,width=800,         height=545');
}

function checkfecha(f){
	var date = new Date();
	var dia = date.getDate().toString();
	var dia = "000000"+dia;
	var dia = dia.slice(-2);
	
	var mes = date.getMonth()+1;
	if(mes < 9){
		mes = "0"+(mes)
	}
	
	var year = date.getFullYear().toString();
	var year = year.slice(2, 4);
	
	var fecha = dia + "/" + mes + "/" + year;

	object=f;
	valueForm=object.value;
	if(valueForm == ""){
			object.value = fecha;
	}else{
		var patron=/^([0-2][0-9]|3[0-1])\/(0[0-9]|1[0-2])\/([0-9]\d)$/;
		if(valueForm.search(patron)!=0){
//Hora incorrecta
			object.value = fecha;
		}
	}
}
function open_date(date_id, pac_id){
	document.cookie = "coo_date="+date_id+"; max-age=86400";
	document.location="ins_med_history.php?q="+pac_id+"&r="+date_id;
}
