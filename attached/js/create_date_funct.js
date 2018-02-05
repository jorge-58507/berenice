
// JavaScript Document

//#################### FUNCION CHECK CEDULA ####################
content_cide = "";
limit_cide = 15;

function chk_cide(cide){
	cide.value = Solo_Numerico(cide.value);
	var num_char = cide.value.length;
	if(num_char > limit_cide){
		cide.value = content_cide;
		new pac_info_ci();
	}else{
		content_cide = cide.value;
		new pac_info_ci();
	}
}
//#################### FUNCION CHECK NOMBRE ####################
content_nya = "";
limit_nya = 80;

function chk_nya(nya){
	var num_char = nya.value.length;
	if(num_char > limit_nya){
		nya.value = content_nya;
		new pac_info_nya()
	}else{
		content_nya = nya.value;
		new pac_info_nya()
	}
}
//#################### FUNCION CHECK H ####################
content_h = "";
limit_h = 10;

function chk_h(h){
	var num_char = h.value.length;
	if(num_char > limit_h){
		h.value = content_h;
		new pac_info_h()
	}else{
		content_h = h.value;
		new pac_info_h()
	}
}
//#################### FUNCION CHECK EDAD ####################
content_edad = "";
limit_edad = 3;

function chk_edad(edad){
	edad.value = Solo_Numerico(edad.value);
	var num_char = edad.value.length;
	if(num_char > limit_edad){
		edad.value = content_edad;
	}else{
		return content_edad = edad.value	
	}
}

function js_sel_motivo(){
	var mc = document.getElementById("sel_mc").value
	document.getElementById("date_mc").value = mc;
}

function sel_patient(id,ci,nya,edad,h,genero){
	document.getElementById("pac_id").value = id;
	document.getElementById("pac_ci").value = ci;
	document.getElementById("pac_nya").value = nya;
	document.getElementById("pac_edad").value = edad;
	document.getElementById("pac_h").value = h;
	document.getElementById("pac_genero").selectedIndex = genero;
	document.getElementById("pac_genero").value = genero;
}

function js_save_pac(){
	if(isEmpty("pac_ci")){
		setFocus("pac_ci")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("pac_nya")){
		setFocus("pac_nya")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("pac_edad")){
		setFocus("pac_edad")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("pac_h")){
		setFocus("pac_h")
	alert("Campos vacio");
	return false;	
	}
	var pac_id = extraer_value("pac_id");
	if(pac_id == ''){
		new config_pac('add');
	}else{
		new config_pac('upd');
	}
}

function js_del_pac(){
	if(isEmpty("pac_ci")){
		setFocus("pac_ci")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("pac_nya")){
		setFocus("pac_nya")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("pac_edad")){
		setFocus("pac_edad")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("pac_h")){
		setFocus("pac_h")
	alert("Campos vacio");
	return false;	
	}
		new config_pac('del');
}

function sel_date(date_id,pac_id,hora,tt,mc){
	document.getElementById("date_pac_id").value = pac_id;
	document.getElementById("date_id").value = date_id;
	document.getElementById("date_hora").value = hora;
	document.getElementById("date_mc").value = mc;
	document.getElementById("date_meridian").value = tt;
}


function checktime(){
	var date = new Date();var horas = date.getHours();var meridian = "am";
	if(horas < 12){
		var meridian = "am";	
		if(horas < 10){	var horas = "0"+(horas);	}
	}
	if(horas > 11){
		var meridian = "pm";
		if(horas > 21){	var horas = horas-12;	}
		if(horas > 12){	var horas =  "0"+(horas-12);	}
	}
	var minutos = date.getMinutes();
	if(minutos < 10){	minutos = "0"+(minutos);	}

	object=document.getElementById("date_hora");
	valueForm=object.value;
	valueMeridian = document.getElementById("date_meridian").value;
	if(valueForm == "" || valueMeridian == ""){
		object.value = horas+":"+minutos;
		document.getElementById("date_meridian").value = meridian;
		return false();
	}else{
		var patron=/^(0[0-9]|1[0-2]):([0-5]\d)$/;
		var patron_tt=/^(am)|(pm)/;
		if(valueForm.search(patron)!=0 || valueMeridian.search(patron_tt)!=0){
			object.value = horas+":"+minutos;
			document.getElementById("date_meridian").value = meridian;
			return false();
		}
	}
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

function js_save_date(){
	if(extraer_value("date_id") == ""){
		check_date("ins");
	}else{
		check_date("upd");
	}
}
function js_del_date(){
		check_date("del");
}

function check_date(funct){
	var field_name = [];
	field_name = ["Hora", "Fecha", "Meridiano", "M. de Consulta"];
	var field = [];
	field = ["date_hora", "date_fecha", "date_meridian", "date_mc"];
	for(it=0;it<field.length;it++){
		if(extraer_value(field[it]) == ""){
			alert("Campo: "+field_name[it]+" esta vacio.");
			return false();
		}
	}
	if(funct == "upd" || funct == "del"){
		if(extraer_value("date_pac_id") == ""){
			alert("Debe seleccionar un paciente del listado");
			return false();
		}
	}else{
		if(extraer_value("pac_id") == ""){
			alert("Debe seleccionar un paciente del listado");
			return false();
		}
	}
	new config_date(funct)
}

