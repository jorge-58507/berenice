// JavaScript Document














function send_npac(form){
var obj_form = document.forms['form_npac'];
var afield = new Array('pac_nhis','pac_nati','pac_cide','pac_lname','pac_fname','pac_bday', 'pac_age');
var fieldn = new Array('Nº Historia','Nacionalidad','C.I.','Apellido','Nombre','Fecha de Nacimiento','Edad erroneo o ');
	for(var a in afield){
		if (isEmpty(afield[a])) {
			alert("Campo: "+fieldn[a]+" vacio.");
			setFocus(afield[a]);
			return false;
		}
	}
obj_form.submit();
//document.location.href="ins_npac.php?nhis="+nhis+"&nati="+nati+"&cide="+cide+"&lname="+lname+"&fname="+fname+"&bday="+bday+"&gend="+gend+"&tlocC="+tlocC+"&tlocN="+tlocN+"&tmovC="+tmovC+"&tmovN="+tmovN+"&eciv="+eciv+"&emai="+emai+"&addr="+addr+"&dcom="+dcom+"&pict="+pict+"";
//if(nhis == "" || nati == "" || cide == "" || lname == "" || fname == "" || bday == "" ){
//	alert(
//}
}

function send_ndate(){
	obj_form = document.forms['form_ndate'];
var afield = new Array('sel_date_pac','sel_date_reason','txt_date_date','txt_date_time','sel_date_meri', 'form_name');
var fieldn = new Array('Paciente','Razon de Cita','Fecha','Hora','Meridiano', 'Fecha, erroneo o');
	for(var a in afield){
		if (isEmpty(afield[a])) {
			alert("Campo: "+fieldn[a]+" vacio.");
			setFocus(afield[a]);
			return false;
		}
	}
obj_form.submit();
}