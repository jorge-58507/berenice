// JavaScript Document
//#################### FUNCION ENFOCAR ####################
function setFocus(aField) {
document.forms[0][aField].focus();
}
//#################### FUNCION CAMPO VACIO ####################
function isEmpty(aTextField) {
if ((document.forms[0][aTextField].value.length==0) ||
 (document.forms[0][aTextField].value==null)) {
return true;
}
else { return false; }

}
//#################### FUNCION CAMPO LLENO ####################
function isSet(aTextField) {
if ((document.forms[0][aTextField].value.length==0) ||
 (document.forms[0][aTextField].value==null)) {
return false;
}
else { return true; }
}
//#################### FUNCION VACIAR CAMPO ####################
function setEmpty(aTextField){
	object = document.forms[0][aTextField];
	object.value = "";
}

//#################### FUNCION VALIDADORA ####################
function validate_empty_user() {

if (isEmpty("txt_correo")) {
	alert("Porfavor Ingrese el Usuario.");
	setFocus("txt_correo");
	return false;
}
if (isEmpty("txt_clave")) {
	alert("Porfavor ingrese la Contrase"+'\u00f1a'+".");
	setFocus("txt_correo");
	return false;
}
return false;
}
//#################### FUNCION VALIDAR EMAIL ####################
function validatemail(){
	//Creamos un objeto
	object=document.forms['form_login'].txt_correo;
	valueForm=object.value;
	if(valueForm == ""){
	setFocus('txt_correo');
	}else{
	// Patron para el correo
	var patron=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
	if(valueForm.search(patron)==0)
	{
//Mail correcto
	object.style.color="#000000";
	return;
	}else{
	object.style.color="#FF0000";
	alert("Error de sintaxis en: E-Mail");
	//Mail incorrecto
	//object.value="";
	}
	}
}
function validatemailonblur(){
	//Creamos un objeto
	object=document.forms['form_login'].txt_correo;
	valueForm=object.value;
	// Patron para el correo
	var patron=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
	if(valueForm.search(patron)==0)
	{
//Mail correcto
	object.style.color="#000000";
	return;
	}else{
	//Mail incorrecto
	object.style.color="#FF0000";
	}
}
function Solo_Numerico(variable){
    Numer=parseInt(variable);
    if (isNaN(Numer)){
       return "";
       }
       return Numer;
    }
function Solo_Letra(variable){
    Numer=parseInt(variable);
    if (isNaN(Numer)){
       return variable;
       }
       return "";
    }
//#################### FUNCION CHECK CEDULA ####################
content_cide = "";
limit_cide = 10;

function chk_cide(cide){
	cide.value = Solo_Numerico(cide.value);
	var num_char = cide.value.length;
	if(num_char > limit_cide){
		cide.value = content_cide;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_cide = cide.value	
	}
}
//#################### FUNCION CHECK FC ####################
content_fc = "";
limit_fc = 3;

function chk_fc(fc){
	fc.value = Solo_Numerico(fc.value);
	var num_char = fc.value.length;
	if(num_char > limit_fc){
		fc.value = content_fc;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_fc = fc.value	
	}
}
//#################### FUNCION CHECK NYA ####################
content_nya = "";
limit_nya = 60;

function chk_nya(nya){
	nya.value = Solo_Letra(nya.value);
	var num_char = nya.value.length;
	if(num_char > limit_nya){
		nya.value = content_nya;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_nya = nya.value	
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
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_edad = edad.value	
	}
}
//#################### FUNCION CHECK FR ####################
content_fr = "";
limit_fr = 2;

function chk_fr(fr){
	fr.value = Solo_Numerico(fr.value);
	var num_char = fr.value.length;
	if(num_char > limit_fr){
		fr.value = content_fr;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_fr = fr.value	
	}
}
//#################### FUNCION CHECK TA SISTOLICA ####################
content_tas = "";
limit_tas = 3;

function chk_tas(tas){
	tas.value = Solo_Numerico(tas.value);
	var num_char = tas.value.length;
	if(num_char > limit_tas){
		tas.value = content_tas;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_tas = tas.value	
	}
}
//#################### FUNCION CHECK TA DIASTOLICA ####################
content_tad = "";
limit_tad = 2;

function chk_tad(tad){
	tad.value = Solo_Numerico(tad.value);
	var num_char = tad.value.length;
	if(num_char > limit_tad){
		tad.value = content_tad;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_tad = tad.value	
	}
}
//#################### FUNCION CHECK TEMPERATURA ####################
content_temp = "";
limit_temp = 2;

function chk_temp(temp){
	temp.value = Solo_Numerico(temp.value);
	var num_char = temp.value.length;
	if(num_char > limit_temp){
		temp.value = content_temp;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_temp = temp.value	
	}
}
//#################### FUNCION CHECK GLICEMIA ####################
content_gc = "";
limit_gc = 3;

function chk_gc(gc){
	gc.value = Solo_Numerico(gc.value);
	var num_char = gc.value.length;
	if(num_char > limit_gc){
		gc.value = content_gc;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_gc = gc.value	
	}
}







function js_sel_motivo(){
var tesis_value = document.forms['pac_informe']['inf_mc'].value;
var antitesis_value = document.forms['pac_informe']['sel_mc'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_mc'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_mc'].value = sintesis_value;
}
}

function js_sel_ea(){
var tesis_value = document.forms['pac_informe']['inf_ea'].value;
var antitesis_value = document.forms['pac_informe']['sel_ea'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ea'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ea'].value = sintesis_value;
}
}

function js_sel_antecedente(){
var tesis_value = document.forms['pac_informe']['inf_antecedente'].value;
var antitesis_value = document.forms['pac_informe']['sel_antecedente'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_antecedente'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_antecedente'].value = sintesis_value;
}
}

function js_sel_piel(){
var tesis_value = document.forms['pac_informe']['inf_ef_piel'].value;
var antitesis_value = document.forms['pac_informe']['sel_ef_piel'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ef_piel'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ef_piel'].value = sintesis_value;
}
}

function js_sel_orl(){
var tesis_value = document.forms['pac_informe']['inf_ef_orl'].value;
var antitesis_value = document.forms['pac_informe']['sel_ef_orl'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ef_orl'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ef_orl'].value = sintesis_value;
}
}

function js_sel_neck(){
var tesis_value = document.forms['pac_informe']['inf_ef_neck'].value;
var antitesis_value = document.forms['pac_informe']['sel_ef_neck'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ef_neck'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ef_neck'].value = sintesis_value;
}
}

function js_sel_respiratory(){
var tesis_value = document.forms['pac_informe']['inf_ef_chest'].value;
var antitesis_value = document.forms['pac_informe']['sel_ef_respiratory'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ef_chest'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ef_chest'].value = sintesis_value;
}
}

function js_sel_cardiac(){
var tesis_value = document.forms['pac_informe']['inf_ef_chest'].value;
var antitesis_value = document.forms['pac_informe']['sel_ef_cardiac'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ef_chest'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ef_chest'].value = sintesis_value;
}
}

function js_abdomen(antitesis){
var tesis_value = document.forms['pac_informe']['inf_ef_abdomen'].value;

if(tesis_value==''){
var sintesis_value = antitesis;
document.forms['pac_informe']['inf_ef_abdomen'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis;
document.forms['pac_informe']['inf_ef_abdomen'].value = sintesis_value;
}
}

function js_sel_abd_auscultation(){
	var antitesis_value = document.forms['pac_informe']['sel_ef_abd_auscultation'].value;
	
	new js_abdomen(antitesis_value);
}
function js_sel_abd_inspection(){
	var antitesis_value = document.forms['pac_informe']['sel_ef_abd_inspection'].value;
	
	new js_abdomen(antitesis_value);
}
function js_sel_abd_palpation(){
	var antitesis_value = document.forms['pac_informe']['sel_ef_abd_palpation'].value;
	
	new js_abdomen(antitesis_value);
}

function js_sel_head(){
var tesis_value = document.forms['pac_informe']['inf_ef_head'].value;
var antitesis_value = document.forms['pac_informe']['sel_ef_head'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ef_head'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ef_head'].value = sintesis_value;
}
} 

function js_sel_pelvis(){
var tesis_value = document.forms['pac_informe']['inf_ef_pelvis'].value;
var antitesis_value = document.forms['pac_informe']['sel_ef_pelvis'].value;
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_ef_pelvis'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_ef_pelvis'].value = sintesis_value;
}
}

function js_sel_dx(){
var tesis_value = extraer_value("inf_dx");
var antitesis_value = extraer_value("sel_dx");
if(tesis_value==''){
var sintesis_value = antitesis_value;
document.forms['pac_informe']['inf_dx'].value = sintesis_value;
}else{
var sintesis_value = tesis_value+', '+antitesis_value;
document.forms['pac_informe']['inf_dx'].value = sintesis_value;
}
}


function extraer_value(campo){
return document.forms['pac_informe'][campo].value
}


function js_save_ef(){
	new js_paste_ef();
	/*new add_hm();*/
}


function js_paste_ef(){
var tesis_value = document.forms['pac_informe']['inf_ef'].value;
	var no_empty_field = [];
	var no_empty_field = ['inf_ef_fc','inf_ef_fr','sel_condition'];
	var num_empty_field = no_empty_field.length;
	for(it=0; it<num_empty_field; it++){
		if(isEmpty(no_empty_field[it])){
			alert("Los campos FC, FR y Condici\u00f3n no pueden estar vacio");
			return false;
			}
		}
		
	var field = new Array()
	var field = ['inf_ef_fc','inf_ef_tas','inf_ef_tad','inf_ef_fr','inf_ef_temp','inf_ef_gc','sel_condition','inf_ef_respiracion','inf_ef_hidratacion','inf_ef_fiebre','inf_ef_pupila','inf_ef_head','inf_ef_piel','inf_ef_orl','inf_ef_neck','inf_ef_chest','inf_ef_abdomen','inf_ef_pelvis'];
	var num_field = field.length;
	for(it=0; it<num_field; it++){
		if(isSet(field[it])){
			if(tesis_value==''){
				var tesis_value = "FC: "+extraer_value(field[it]);
			}
			else if(it==1){
				var tesis_value = tesis_value+" TA: "+extraer_value(field[it]);
			}
			else if(it==2){
				var tesis_value = tesis_value+"/"+extraer_value(field[it]);
			}
			else if(it==3){
				var tesis_value = tesis_value+" FR: "+extraer_value(field[it]);
			}
			else if(it==4){
				var tesis_value = tesis_value+" Temp: "+extraer_value(field[it]);
			}
			else if(it==5){
				var tesis_value = tesis_value+" GC: "+extraer_value(field[it]);
			}
			else if(it==6){
				var tesis_value = tesis_value+"\n"+extraer_value(field[it]);
			}
			else if(it==12){
				var tesis_value = tesis_value+", Piel: "+extraer_value(field[it]);
			}
			else if(it==13){
				var tesis_value = tesis_value+", ORL: "+extraer_value(field[it]);
			}
			else if(it==14){
				var tesis_value = tesis_value+", Cuello: Se evidencia "+extraer_value(field[it]);
			}
			else if(it==15){
				var tesis_value = tesis_value+", Torax: "+extraer_value(field[it]);
			}
			else if(it==16){
				var tesis_value = tesis_value+", Abdomen: "+extraer_value(field[it]);
			}
			else if(it==17){
				var tesis_value = tesis_value+", Pelvis: "+extraer_value(field[it]);
			}
			else{
				var tesis_value = tesis_value+", "+extraer_value(field[it]);
			}
		}
	}
	var tesis = extraer_value("inf_ef");
		if(tesis==tesis_value){
			alert("esto ya se copio");
		}
document.forms['pac_informe']['inf_ef'].value = tesis_value;

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
//#################### FUNCION CHECK Nº HISTORIA ####################
content_nhis = "";
limit_nhis = 15;

function chk_nhis(){
	var num_char = document.forms['form_npac'].pac_nhis.value.length;
	if(num_char > limit_nhis){
		document.forms['form_npac'].pac_nhis.value = content_nhis;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		content_nhis = document.forms['form_npac'].pac_nhis.value	
	}
}
//#################### FUNCION CHECK NOMBRE ####################
fname_content = "";
fname_limit = 50;

function chk_fname(fname){
	var num_char = fname.value.length;
	if(num_char > fname_limit){
		document.forms['form_npac'].pac_fname.value = fname_content;
	}else{
		fname_content = document.forms['form_npac'].pac_fname.value	
	}
}

//#################### FUNCION CHECK DIRECCION ####################
lname_content = "";
lname_limit = 50;

function chk_lname(lname){
	var num_char = lname.value.length;
	if(num_char > lname_limit){
		document.forms['form_npac'].pac_lname.value = lname_content;
	}else{
		lname_content = document.forms['form_npac'].pac_lname.value	
	}
}

//#################### FUNCION CHECK LOCAL ####################
tlocC_content = "";
tlocC_limit = 3;

function chk_tlocC(tlocC){
	var num_char = tlocC.value.length;
	if(num_char > tlocC_limit){
		//document.forms['form_npac'].pac_tlocC.value = tlocC_content;
		setFocus("pac_tlocN");
	}else{
		tlocC_content = document.forms['form_npac'].pac_tlocC.value	
	}
}
tlocN_content = "";
tlocN_limit = 7;

function chk_tlocN(tlocN){
	var num_char = tlocN.value.length;
	if(num_char > tlocN_limit){
		document.forms['form_npac'].pac_tlocN.value = tlocN_content;
	}else{
		tlocN_content = document.forms['form_npac'].pac_tlocN.value	
	}
}

//#################### FUNCION CHECK LOCAL ####################
tmovC_content = "";
tmovC_limit = 3;

function chk_tmovC(tmovC){
	var num_char = tmovC.value.length;
	if(num_char > tmovC_limit){
		//document.forms['form_npac'].pac_tlocC.value = tlocC_content;
		setFocus("pac_tmovN");
	}else{
		tmovC_content = document.forms['form_npac'].pac_tmovC.value	
	}
}
tmovN_content = "";
tmovN_limit = 7;

function chk_tmovN(tmovN){
	var num_char = tmovN.value.length;
	if(num_char > tmovN_limit){
		document.forms['form_npac'].pac_tmovN.value = tmovN_content;
	}else{
		tmovN_content = document.forms['form_npac'].pac_tmovN.value	
	}
}

//#################### FUNCION CHECK DIRECCION ####################
content_emai = "";
limit_emai = 50;

function chk_emai(){
	var num_char = document.forms['form_npac'].pac_emai.value.length;
	if(num_char > limit_emai){
		document.forms['form_npac'].pac_emai.value = content_emai;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		content_emai = document.forms['form_npac'].pac_emai.value
	}
}
//#################### FUNCION CHECK DIRECCION ####################
content_addr = "";
limit_addr = 160;

function chk_addr(){
	var num_char = document.forms['form_npac'].pac_addr.value.length;
	if(num_char > limit_addr){
		document.forms['form_npac'].pac_addr.value = content_addr;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		content_addr = document.forms['form_npac'].pac_addr.value	
	}
}
//#################### FUNCION CHECK DATOS COMPLEMENTARIOS ####################
content_dcom = "";
limit_dcom = 200;

function chk_dcom(){
	var num_char = document.forms['form_npac'].pac_dcom.value.length;
	if(num_char > limit_dcom){
		document.forms['form_npac'].pac_dcom.value = content_dcom;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		content_dcom = document.forms['form_npac'].pac_dcom.value	
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
if (tecla==8) return true; 
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
function checktime(str){
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


function openNdate(str){
	
	
}