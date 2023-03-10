// JavaScript Document

function set_txtfilterclient(field){
	$("#txt_filterclient").val(field.text)
	$("#txt_filterclient").prop("alt",field.value)
}

function open_addclient(next_clientid){
	var name = $("#txt_filterclient").val()
	open_popup('popup_addclient.php?a='+name+'&b='+next_clientid+'', 'popup_addclient','425','420');
}

function popup_make_nc(facturaf_id){
	$.ajax({	data: "",	type: "GET",	dataType: "JSON",	url: "attached/get/get_session_admin.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data[0][0] != ""){
			 window.opener.location.href='make_nc.php?a='+facturaf_id+'';
			 self.close();
		 }else{
			 open_popup('popup_loginadmin.php?z=start_admin.php','_popup','425','420');
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}
function make_nc(facturaf_id){
	$.ajax({	data: "",	type: "GET",	dataType: "JSON",	url: "attached/get/get_session_admin.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data[0][0] != ""){
			 window.location.href='make_nc.php?a='+facturaf_id+'';
		 }else{
			 open_popup('popup_loginadmin.php?z=make_nc.php?a='+facturaf_id,'_popup','425','420');
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}
function open_newdebit(client_id){
	$.ajax({	data: "",	type: "GET",	dataType: "JSON",	url: "attached/get/get_session_admin.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data[0][0] != ""){
			 document.location.href = 'popup_newdebit.php?a='+client_id;
		 }else{
			 open_popup('popup_loginadmin.php?z=start_admin.php','_popup','425','420');
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function new_return(datoventa_id, cantidad_retirada){
	open_popup(`popup_product2newdevolution.php?a=${datoventa_id}&b=${cantidad_retirada}`,"_popup",'420','420');
}

function change_paymentmethod(pm_id){
	switch(pm_id){
		case '1':
			$("#txt_number").prop('disabled',true);
			$("#txt_number").val("");
			$("#txt_amount").prop('disabled',false);
			$("#btn_amount").show(200);
			$("#txt_amount").val("");
			$("#txt_amount").focus();
		break;
		case '2':
			$("#txt_number").prop('disabled',true);
			$("#txt_number").val("");
			$("#txt_amount").prop('disabled',false);
			$("#btn_amount").show(200);
			$("#txt_amount").val("");
			$("#txt_amount").focus();
		break;
		case '3':
			$("#txt_number").prop('disabled',false);
			$("#txt_number").val("");
			$("#txt_amount").prop('disabled',false);
			$("#btn_amount").show(200);
			$("#txt_amount").val("");
			$("#txt_number").focus();
		break;
		case '4':
			$("#txt_number").prop('disabled',true);
			$("#txt_number").val("");
			$("#txt_amount").prop('disabled',true);
			$("#btn_amount").hide(200);
			$("#txt_amount").val("0");
			$("#txt_amount").focus();
		break;
		case '5':
			$("#txt_number").prop('disabled',true);
			$("#txt_number").val($("#sel_paymentmethod option:selected").prop("label"));
		var	option = ($("#sel_paymentmethod option:selected").text());
			numero = option.split(" ");
			numero_length = numero.length;
			exedente = numero[numero_length-1];
			$("#txt_amount").prop('disabled',false);
			$("#btn_amount").show(200);
			$("#txt_amount").val(exedente);
			$("#txt_amount").focus();
		break;
	}
}
function old_quotation(date_limit){
var ans = confirm("??Desea Revisarlos?");
	if(ans){
		filter_oldquotation(date_limit)
	}
}
var raw_payment=[];
i=0;
var abonado = 0;
function add_payment_cpp(method, amount, number, cpp_id, date){
	var tr_payment = new Object();
			tr_payment['fecha'] = date;
			tr_payment['metodo'] = method;
			tr_payment['monto'] = amount;
			tr_payment['numero'] = number;
	if(raw_payment[i]){
		i++;
		add_payment_cpp(method, amount, number, cpp_id, date);
	}else{
		var monto = parseFloat(tr_payment['monto']);
		var saldo = $("#span_saldo").html().replace(",","");saldo = saldo.replace("B/ ","");
		saldo_ab=saldo-abonado; saldo_ab = saldo_ab.toFixed(2); saldo_ab=parseFloat(saldo_ab);
		if (monto > saldo_ab) {
			return false;
		}else{
			raw_payment[i] = tr_payment;
			abonado = abonado + monto;
		}
		print_payment_cpp(raw_payment);
		if (method === '2') {
			var ans = confirm("??Desea elaborar el cheque?");
			if (ans) {
				json_payment_i=JSON.stringify(raw_payment[i]);
				open_popup('popup_make_check.php?a='+json_payment_i+'&b='+i+'&c='+cpp_id,'_popup','520','420');
			}
		}
	}
}
function remove_payment_cpp(index){
	abonado = abonado-raw_payment[index]['monto']; abonado = abonado.toFixed(2);
	raw_payment.splice(index,1);
	print_payment_cpp(raw_payment);
}
function print_payment_cpp(raw_payment){
	payment_method = ['','EFECTIVO','CHEQUE','T. DE CREDITO','T. CLAVE','','','NOTA DE CREDITO','OTRO'];
	tbody="";
	for (var property in raw_payment) {
		var monto = parseFloat(raw_payment[property].monto);
		tbody = tbody+'<tr><td>'+raw_payment[property].fecha+'</td><td>'+payment_method[raw_payment[property].metodo]+'</td><td>'+raw_payment[property].numero+'</td><td>B/ '+monto.toFixed(2)+'</td><td><button type="button" class="btn btn-danger btn-sm" onclick="remove_payment_cpp('+property+')"><i class="fa fa-times"></i></button></td></tr>';
	}
	$("#tbl_payment tbody").html(tbody);
}
function plus_cpp_payment(cpp_id){
	$.ajax({	data: {"a" : JSON.stringify(raw_payment), "b" : cpp_id },	type: "GET",	dataType: "text",	url: "attached/get/plus_cpp_payment.php", })
	 .done(function( data, textStatus, jqXHR ) {	console.log('GOOD '+textStatus);
	 	if (data) {	setTimeout('history.back(1)',250); }
 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}
function del_cpp_payment(payment_id){
	$.ajax({	data: {"a" : payment_id },	type: "GET",	dataType: "text",	url: "attached/get/del_cpp_payment.php", })
	 .done(function( data, textStatus, jqXHR ) {	console.log('GOOD '+textStatus);
	 	 $("#tbl_datocpp tbody").html(data);
 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

var o=new Array("diez", "once", "doce", "trece", "catorce", "quince", "diecis??is", "diecisiete", "dieciocho", "diecinueve", "veinte", "veintiun", "veintid??s", "veintitr??s", "veinticuatro", "veinticinco", "veintis??is", "veintisiete", "veintiocho", "veintinueve");
var u=new Array("cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve");
var d=new Array("", "", "", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa");
var c=new Array("", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos");

function nn(n){
	if(n != ''){
  var n=parseFloat(n).toFixed(2); /*se limita a dos decimales, no sab??a que exist??a toFixed() :)*/
  var p=n.toString().substring(n.toString().indexOf(".")+1); /*decimales*/
  var m=n.toString().substring(0,n.toString().indexOf(".")); /*n??mero sin decimales*/
  var m=parseFloat(m).toString().split("").reverse(); /*tampoco que reverse() exist??a :D*/
  var t="";
  /*Se analiza cada 3 d??gitos*/
  for (var i=0; i<m.length; i+=3)
  {
    var x=t;
    /*formamos un n??mero de 2 d??gitos*/
    var b=m[i+1]!=undefined?parseFloat(m[i+1].toString()+m[i].toString()):parseFloat(m[i].toString());
    /*analizamos el 3 d??gito*/
    t=m[i+2]!=undefined?(c[m[i+2]]+" "):"";
    t+=b<10?u[b]:(b<30?o[b-10]:(d[m[i+1]]+(m[i]=='0'?"":(" y "+u[m[i]]))));
    t=t=="ciento cero"?"cien":t;
    if (2<i&&i<6)
      t=t=="uno"?"mil ":(t.replace("uno","un")+" mil ");
    if (5<i&&i<9)
      t=t=="uno"?"un mill??n ":(t.replace("uno","un")+" millones ");
    t+=x;
  }
	raw_t = t.split(" ");
	t="";
	for (var prop in raw_t) {
		t+=raw_t[prop].charAt(0).toUpperCase()+raw_t[prop].slice(1)+" ";
	}
  t=t+" Balboas con "+p+"/100";
  t=t.replace(" Cero","");
	t=t.replace("  "," ");
  return t;
	}
}
function render_tableff(data) {
	var content = '';
	for (const a in data) {
		var total = parseFloat(data[a]['TX_facturaf_total']);
		var btn = (data[a]['TX_facturaf_deficit'] != '0') ? `<button type="button" id="btn_opennewdebit" name="${ data[a]['facturaf_AI_cliente_id'] }" class="btn btn-success btn-sm" onclick="open_newdebit(this.name);">DEBITAR</button>` : ''; 
		content += `
			<tr  ondblclick="print_html('print_client_facturaf.php?a=${ data[a]['AI_facturaf_id'] }')" >
				<td>${ data[a]['TX_facturaf_numero'] }  </td>
				<td>${ data[a]['TX_cliente_nombre'] } </td>
				<td>${ date_converter('ymd', 'dmy', data[a]['TX_facturaf_fecha']) }<br/>${data[a]['TX_facturaf_hora'] } </td>
				<td>${ total.toFixed(2) }</td>
				<td>${ data[a]['TX_facturaf_deficit'] }</td>
				<td>${ data[a]['TX_user_seudonimo'] }</td>
				<td>
					<button type="button" id="btn_openff" name="${ data[a]['AI_facturaf_id'] }" class="btn btn-info btn-sm" onclick="open_popup_w_scroll('popup_watchfacturaf.php?a='+this.name,'watch_facturaf','950','547');">VER</button>
					<button type="button" id="btn_makenc" name="${ data[a]['AI_facturaf_id'] }" class="btn btn-warning btn-sm" onclick="make_nc(this.name);">N.C.</button>
					${ btn }
				</td>
			</tr>
		`;
	} 
	$("#tbl_facturaf tbody").html(content);
}
