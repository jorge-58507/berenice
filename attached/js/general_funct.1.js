// JavaScript Document
//################# DETECTA CONTROL Q ##################
isCtrl=false;
document.onkeydown = function (e) {
if(e.which == 17)
	isCtrl=true;
	if(e.which == 123 && isCtrl == true) {
		popup = window.open("popup_loginadmin.php?z=start_admin.php", "popup_loginadmin", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
		isCtrl=false;
		return false;
	}
	if(e.which == 49 && isCtrl == true) {
		$("#1").click(); isCtrl=false; return false;
	}
	if(e.which == 50 && isCtrl == true) {
		$("#2").click(); isCtrl=false; return false;
	}
	if(e.which == 51 && isCtrl == true) {
		$("#3").click(); isCtrl=false; return false;
	}
	if(e.which == 52 && isCtrl == true) {
		$("#4").click(); isCtrl=false; return false;
	}
	if(e.which == 53 && isCtrl == true) {
		$("#5").click(); isCtrl=false; return false;
	}
	if(e.which == 55 && isCtrl == true) {
		$("#7").click(); isCtrl=false; return false;
	}
}
function val_intwdec(str){
	pat = new RegExp('^[0-9]+([.][0-9]+)?$')
	ans = pat.test(str);
	return ans;
}
function val_intw2dec (str) {
  var pat = new RegExp('^[0-9]+([.][0-9]{1,2})?$')
  var ans = pat.exec(str)
  var str_splited = str.split('.')
  if(str_splited.length > '1') {
		if(str_splited.length > '2') {
			str_splited.splice(2);
		}
		if (str_splited[0].length === 0) {
			str_splited[0]='0';
		}
		if (str_splited[1].length === 0) {
			str_splited[1]='00';
    }
		if (str_splited[1].length === 1) {
			str_splited[1] = str_splited[1] + '0';
    }
		if (str_splited[1].length > 2) {
			str_splited[1] = str_splited[1].substr(0, 2);
    }
		str = str_splited[0] + '.' + str_splited[1];
		str = parseFloat(str).toFixed(2);
		return str;
  } else {
		if(str != ""){
    	str = str_splited[0] + '.00'
			str = parseFloat(str).toFixed(2);
			return str;
  	}else{
			return str;
		}
	}
}

function val_intw4dec (str) {
  var pat = new RegExp('^[0-9]+([.][0-9]{1,2})?$')
  var ans = pat.exec(str)
  var str_splited = str.split('.')
  if(str_splited.length > '1') {
		if(str_splited.length > '2') {
			str_splited.splice(2);
		}
		if (str_splited[0].length === 0) {
			str_splited[0]='0';
		}
		if (str_splited[1].length === 0) {
			str_splited[1]='0000';
    }
		if (str_splited[1].length === 1) {
			str_splited[1] = str_splited[1] + '000';
    }
		if (str_splited[1].length > 2) {
			str_splited[1] = str_splited[1].substr(0, 4);
    }
		str = str_splited[0] + '.' + str_splited[1];
		str = parseFloat(str).toFixed(4);
		return str;
  } else {
		if(str != ""){
    	str = str_splited[0] + '.0000'
			str = parseFloat(str).toFixed(4);
			return str;
  	}else{
			return str;
		}
	}
}

var popup;
function open_popup(href,target,w,h){
	if(!popup){
	popup = window.open(href, target,'toolbar=0,scrollbars=yes,location=0,statusbar=0,menubar=0,resizable=no,width='+w+',height='+h+'');
	}else{
	popup.close();
	popup = window.open(href, target,'toolbar=0,scrollbars=yes,location=0,statusbar=0,menubar=0,resizable=no,width='+w+',height='+h+'');
	}
}
function open_popup_w_scroll(href,target,w,h){
	if(!popup){
	popup = window.open(href, target,'toolbar=0,scrollbars=yes,location=0,statusbar=0,menubar=0,resizable=no,width='+w+',height='+h+'');
	}else{
	popup.close();
	popup = window.open(href, target,'toolbar=0,scrollbars=yes,location=0,statusbar=0,menubar=0,resizable=no,width='+w+',height='+h+'');
	}
}
function print_html(href){
	if(!popup){
	popup = window.open(href);
	}else{
	popup.close();
	popup = window.open(href);
	}
}
function get(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
function fire_recall(container,content){
	$("#"+container).show(300)
	$("#"+container).html(content);
	setTimeout(function(){ $("#"+container).hide(300) }, 8000);
}



//#################### FUNCION CAMPO LLENO ####################
function isSet(aTextField) {
if ((document.forms[0][aTextField].value.length==0) || (document.forms[0][aTextField].value==null)) {
	return false;
	}else{ return true; }
}
//#################### FUNCION ENFOCAR ####################
function setFocus(aField) {
document.forms[0][aField].focus();
}
//#################### FUNCION CAMPO VACIO ####################
function isEmpty(aTextField) {
if ((document.forms[0][aTextField].value.length==0) || (document.forms[0][aTextField].value==null)) {
		document.forms[0][aTextField].style.border = '2px outset #F00';
		setFocus(aTextField);
		return true;
	}else{
		document.forms[0][aTextField].style.border = '2px inset #797b7e80';
	}
}
//#################### FUNCION CAMPO INVALIDO ####################
function isInvalid(aTextField) {
		$("#"+aTextField+"").addClass("input_invalid");
		setFocus(aTextField);
}
//#################### FUNCION CAMPO VALIDO ####################
function isValid(aTextField) {
		$("#"+aTextField+"").addClass("input_valid");
}
//#################### FUNCION VACIAR CAMPO ####################
function setEmpty(aTextField){
	object = document.forms[0][aTextField];
	object.value = "";
}
//#################### FUNCION SOLO NUMEROS ####################
function Solo_Numerico(variable){
    Numer=parseInt(variable);
    if (isNaN(Numer)){
       return "";
       }
       return Numer;
    }
//#################### FUNCION SOLO LETRAS ####################
function Solo_Alfabeto(str){
	var reg = RegExp(/^[a-zA-Z_áéíóúñ\s]*$/);
	var new_str = str;
//		alert(str);
	if(reg.test(new_str)){
//		alert("son letras");
		return true;
	}else{
//		alert("son numeros");
		return false;
	}
}
function setUpperCase(field){
	val = field.value;
	cap = val.toUpperCase();
	field.value = cap; return false;
}

//#################### FUNCION SOLO LETRAS ####################
/*
function sinnumero_simbolo(str){
	var campo = str.id;
	var value_str = str.value;
		var reg = value_str.substr(-1);
		var new_value = value_str.replace(reg,"");
//		alert("a: "+value_str+" lequite: "+substraer+" y me da: "+new_value);
	var reg = RegExp(/^[a-zA-Z_áéíóúñ\s]*$/);
	var test_str = reg.test(value_str);
	if(test_str==true){
//		alert("el campo: "+campo+" /// "+value_str+" "+test_str);
		return str.value = value_str;
	}else{
//		alert(value_str+" "+test_str);
		return str.value = new_value;
	}
}
*/
//#################### FUNCION CHECK NYA ####################
content_nya = "";
limit_nya = 70;

function chk_nya(nya){
	nya_bolean = Solo_Alfabeto(nya.value);
	var num_char = nya.value.length;
	if(num_char > limit_nya){
		nya.value = content_nya;
		alert("Ah Llegado al Maximo de Caracteres.");
	}else{
		if(nya_bolean){
			return content_nya = nya.value
		}else{
			nya.value = content_nya;
		}
	}
}
//#################### FUNCION SOLO LETRAS ####################
function Solo_Letra(variable){
    Numer=parseInt(variable);
    if (isNaN(Numer)){
       return variable;
       }
       return "";
    }
//#################### FUNCION EXTRAER VALUE ####################
function extraer_value(campo){
return document.forms[0][campo].value
}
//#################### FUNCION AGREGAR SALTOS DE LINEA ####################
function js_add_reglones(str){
	var reg = new RegExp("\n",'g');
	var new_str = str.replace(reg,"<br />");
	return new_str;
}
//#################### FUNCION CAMBIAR SALTOS DE LINEA ####################
function js_del_reglones(str){
	var reg = new RegExp("<br />",'g');
	var old_str = str.replace(reg,"\n");
	return old_str;
}
//#################### FUNCION QUITAR SALTOS DE LINEA ####################
function js_clean_reglones(str){
	var reg = new RegExp("<br />|\n",'g');
	var clean_str = str.replace(reg,", ");
	return clean_str;
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
function leerCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) {
			return decodeURIComponent( c.substring(nameEQ.length,c.length) );
		}
	}
	return null;
}
function cap_fl(string){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}












//#################### FUNCION URL ACTIVA ####################
function GetActiveURL(iduser){
	new_nhis(iduser);
	document.forms['form_npac'].pac_cide.focus();
	var doc_url = document.URL;
	var varindex = /index.php/.test(doc_url);
	var vardate = /date.php/.test(doc_url);
	var varexplorer = /explorer.php/.test(doc_url);
	if(varindex == true){
		document.getElementById('shnv1').classList.add('shnv11');
		document.getElementById('shnv2').classList.add('shnv22');
		document.getElementById('shnv3').classList.add('shnv33');
	}
	if(vardate == true){
		document.getElementById('shnv1').classList.add('shnv11');
		document.getElementById('shnv2').classList.add('shnv22');
		document.getElementById('shnv3').classList.add('shnv33');
	}
	if(varexplorer == true){
		document.getElementById('shnv1').classList.add('shnv11');
		document.getElementById('shnv2').classList.add('shnv22');
		document.getElementById('shnv3').classList.add('shnv33');
	}
}



//#################### FUNCION CALCULAR EDAD ####################
function getAge(str){

	var fecha = new Date();
	var day = fecha.getDate();
	var month = fecha.getMonth()+1;
	var year = fecha.getFullYear();

	var fnac = str.split('/'), i;
	var bday = fnac[0];
	var bmonth = fnac[1];
	var byear = fnac[2];

	if(byear < year){
		return calAge(str);
	}else{
		if(byear > year){
			alert("La fecha es incorrecta");
			document.forms['form_npac'].pac_age.value = "";
			document.getElementById('pac_age').display = none;
		}else{
			if(bmonth < month){
				return calAge(str);
			}else{
				if(bmonth > month){
					alert("La fecha es incorrecta");
					document.forms['form_npac'].pac_age.value = "";

				}else{
					if(bday < day){
						return calAge(str);
					}else{
						if(bday > day){
							alert("La fecha es incorrecta");
							document.forms['form_npac'].pac_age.value = "";
						}else{
							return calAge(str);
						}
					}
				}
			}
		}
	}
}
//#################### FUNCION SOLO LETRAS CON ESPACIO ####################
function solo_letraws(e) {
tecla = (document.all) ? e.keyCode : e.which;
if (tecla==8) return true;
patron =/^([A-Za-zÑñ \xc0-\xff]+)$/i;
te = String.fromCharCode(tecla);
return patron.test(te);
}
function blur_letraws(ob){
	object = document.forms[0][ob];
	objectValue = object.value;
patron =/^([A-Za-zÑñ \xc0-\xff]+)$/i;
	if(objectValue.search(patron) != 0){
		object.value = "";
	}

}
function blur_lname(){
	object = document.forms['form_npac'].pac_lname;
	objectValue = object.value;
patron =/^([A-Za-zÑñ \xc0-\xff]+)$/i;
	if(objectValue.search(patron) != 0){
		object.value = "";
	}

}
function blur_fname(){
	object = document.forms['form_npac'].pac_fname;
	objectValue = object.value;
patron =/^([A-Za-zÑñ \xc0-\xff]+)$/i;
	if(objectValue.search(patron) != 0){
		object.value = "";
	}

}
//#################### FUNCION SOLO NUMEROS CON PUNTO####################
function solo_numerowd(e) {
tecla = (document.all) ? e.keyCode : e.which;
if (tecla==8)
return true;
patron =/[0-9.]/g;
te = String.fromCharCode(tecla);
return patron.test(te);
}


function blur_numerowd(ob){
	object = document.forms[0][ob];
	objectValue = object.value;
patron =/[0-9.]/g;
	if(objectValue.search(patron) != 0){
		object.value = "";
	}
}
//#################### FUNCION SOLO NUMEROS ####################
function solo_numero(e) {
tecla = (document.all) ? e.keyCode : e.which;
if (tecla==8) return true;
patron =/[0-9]/g;
te = String.fromCharCode(tecla);
return patron.test(te);
}
function blur_numero(ob){
	object = document.forms[0][ob];
	objectValue = object.value;
patron =/[0-9]/g;
	if(objectValue.search(patron) != 0){
		object.value = "";
	}
}
function blur_tlocC(){
	object_tloc = document.forms['form_npac'].pac_tlocC;
	objectValue = object_tloc.value;
patron =/[0-9]/g;
	if(objectValue.search(patron) != 0){
		object_tloc.value = "";
	}
}
function blur_tlocN(){
	object_tloc = document.forms['form_npac'].pac_tlocN;
	objectValue = object_tloc.value;
patron =/[0-9]/g;
	if(objectValue.search(patron) != 0){
		object_tloc.value = "";
	}
}
function blur_tmovC(){
	object_tmov = document.forms['form_npac'].pac_tmovC;
	objectValue = object_tmov.value;
patron =/[0-9]/g;
	if(objectValue.search(patron) != 0){
		object_tmov.value = "";
	}
}
function blur_tmovN(){
	object_tmov = document.forms['form_npac'].pac_tmovN;
	objectValue = object_tmov.value;
patron =/[0-9]/g;
	if(objectValue.search(patron) != 0){
		object_tmov.value = "";
	}
}
//#################### FUNCION RECARGAR PAGINA ####################
function denied_reload(){
	document.location.reload();
}
//#################### FUNCIONES LIMPIAR TELEFONOS ####################
function clean_tmov(){
	setEmpty("pac_tmovC");
	setEmpty("pac_tmovN");
}

function clean_tloc(){
	setEmpty("pac_tlocC");
	setEmpty("pac_tlocN");
}
//#################### FUNCION LLAMAR CALENDARIO ####################
function cal_popup(){
var cal19 = new CalendarPopup();
cal19.showYearNavigation();
cal19.showYearNavigationInput();
cal19.select(document.forms['form_npac'].pac_bday,'anchor1','dd/MM/yyyy'); return false;
}
//#################### FUNCION LLAMAR CALENDARIO CITAS ####################
function cal_popup_date(){
var cal19 = new CalendarPopup();
cal19.showYearNavigation();
cal19.showYearNavigationInput();
cal19.select(document.forms['form_ndate'].txt_date_date,'anchor1','dd/MM/yyyy'); return false;
}
//#################### FUNCION VERIFICAR FECHA CALENDARIO ####################
function chkDate(str){

	var fecha = new Date();
	var day = fecha.getDate();
	var month = fecha.getMonth()+1;
	var year = fecha.getFullYear();

	var fnac = str.split('/'), i;
	var bday = fnac[0];
	var bmonth = fnac[1];
	var byear = fnac[2];

	if(byear > year){
		return printDate(str);
	}else{
		if(byear < year){
			alert("La fecha es incorrecta");
			document.forms['form_ndate'].form_name.value = "";
		}else{
			if(bmonth > month){
				return printDate(str);
			}else{
				if(bmonth < month){
					alert("La fecha es incorrecta");
					document.forms['form_ndate'].form_name.value = "";
				}else{
					if(bday > day){
						return printDate(str);
					}else{
						if(bday < day){
							alert("La fecha es incorrecta");
							document.forms['form_ndate'].form_name.value = "";
						}else{
							return printDate(str);
						}
					}
				}
			}
		}
	}
}
function printDate(){
			document.forms['form_ndate'].form_name.value = "ins_ndate";
}
//#################### FUNCION LIMITAR HORA "CITAS" ####################
content_time = "";
limit_time = "5";
function chktime(){
	num_char = document.forms['form_ndate'].txt_date_time.value.length;
	if(num_char > limit_time){
		document.forms['form_ndate'].txt_date_time.value = content_time;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		content_time = document.forms['form_ndate'].txt_date_time.value;
	}
}
//#################### FUNCION VERIFICAR HORA "CITAS" ####################
function checktimes(str){
	object=document.forms['form_ndate'].txt_date_time;
	valueForm=object.value;
	if(valueForm == ""){
		setFocus("txt_date_time");
	}else{
//		var patron=/^(0[1-9]|1\d|2[0-3]):([0-5]\d)$/;
//		var patron=/^([0-1])([0-2]):([0-5]\d)$/;
		var patron=/^(0[0-9]|1[0-2]):([0-5]\d)$/;
		if(valueForm.search(patron)==0){
//Hora correcta
			object.style.color="#000000";
			return;
		}else{
//Hora incorrecta
			alert("Error de sintaxis en: Hora");
			setFocus("txt_date_time");
		}
	}
}
//#################### FUNCION VALIDAR EMAIL ####################
function validatemail(object){
	name_obj = object.name;
	valueForm=extraer_value(name_obj);
	// Patron para el correo
	var patron=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
	if(valueForm.search(patron)==0){
		//Mail correcto
		object.style.color="#000000";
		return;
	}else{
		//Mail incorrecto
		object.style.color="#FF0000";
	}
}
//#################### FUNCION VALIDAR x2 CAMPOS ####################
function validate_empty_userx2(str1,str2) {
if (isEmpty(str1)) {
	alert("Faltan datos para continuar.");
	setFocus(str1);
	return false;
}
if (isEmpty(str2)) {
	alert("Faltan datos para continuar.");
	setFocus(str2);
	return false;
}
return false;
}


(function($) {
	$.get = function(key)   {
		key = key.replace(/[\[]/, '\\[');
		key = key.replace(/[\]]/, '\\]');
		var pattern = "[\\?&]" + key + "=([^&#]*)";
		var regex = new RegExp(pattern);
		var url = unescape(window.location.href);
		var results = regex.exec(url);
		if (results === null) {
			return null;
		} else {
			return results[1];
		}
	}
})(jQuery);

function close_popup(){
	popup.close();
}

function replace_regular_character(str){
	var replacement = ["&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&laremun;","&nolger;","&squote;","°","&deg;","&ntilde;"];
	var to_replace = ["Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","º","°","ñ"];
	for (var x in to_replace) {
		patt = RegExp(to_replace[x],'g');
		str = str.replace(patt,replacement[x]);
	}
	return str;
}
function replace_special_character(str){
	var replacement = ["Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","°","º","ñ"];
	var to_replace = ["&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&laremun;","&nolger;","&squote;","&deg;","°","&ntilde;"];
	for (var x in to_replace) {
		patt = RegExp(to_replace[x],'g');
		str = str.replace(patt,replacement[x]);
	}
	return str;
}
function url_replace_regular_character(str){
	var replacement = ["Aacute;","Eacute;","Iacute;","Oacute;","Uacute;","Ntilde;","aacute;","eacute;","iacute;","oacute;","uacute;","laremun;","nolger;","squote;","°","deg;","ntilde;","ampersand;"];
	var to_replace = ["Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","º","°","ñ","&"];
	for (var x in to_replace) {
		patt = RegExp(to_replace[x],'g');
		str = str.replace(patt,replacement[x]);
	}
	return str;
}
function url_replace_special_character(str){
	var replacement = ["Á","É","Í","Ó","Ú","Ñ","á","é","í","ó","ú","#","\n","'","°","º","ñ","&"];
	var to_replace = ["Aacute;","Eacute;","Iacute;","Oacute;","Uacute;","Ntilde;","aacute;","eacute;","iacute;","oacute;","uacute;","laremun;","nolger;","squote;","deg;","°","ntilde;","ampersand;"];
	for (var x in to_replace) {
		patt = RegExp(to_replace[x],'g');
		str = str.replace(patt,replacement[x]);
	}
	return str;
}
function set_good_field(id){
	$("#"+id).removeClass("bad_field");
}
function set_bad_field(id){
	$("#"+id).addClass("bad_field");
}
function convertir_formato_fecha(string) {
  var fecha = string.split('-');
  return fecha[2] + '-' + fecha[1] + '-' + fecha[0];
}
//  ######################   REDONDEO DECIMAL   /////////////////////////
(function() {
function decimalAdjust(type, value, exp) {
	// Si el exp no está definido o es cero...
	if (typeof exp === 'undefined' || +exp === 0) {
		return Math[type](value);
	}
	value = +value;
	exp = +exp;
	// Si el valor no es un número o el exp no es un entero...
	if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
		return NaN;
	}
	// Shift
	value = value.toString().split('e');
	value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
	// Shift back
	value = value.toString().split('e');
	return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
}

// Decimal round
if (!Math.round10) {
	Math.round10 = function(value, exp) {
		return decimalAdjust('round', value, exp);
	}
}
// Decimal floor
if (!Math.floor10) {
	Math.floor10 = function(value, exp) {
		return decimalAdjust('floor', value, exp);
	}
}
// Decimal ceil
if (!Math.ceil10) {
	Math.ceil10 = function(value, exp) {
		return decimalAdjust('ceil', value, exp);
	}
}
})();
