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


// const obj = {
//   get prop() {
//     return this.__prop__;
//   },
//   set prop(value) {
//     this.__prop__ = value * 2;
//   },
// };

// obj.prop = 12;

// console.log(obj.prop); //24




class product_management {
	constructor () {
		this.intervalo;
	}
	pick_line (id,code,description) {
		var condition = 1;
		const raw_product_selected = product_management.prototype.array_product_selected;
		for (const a in raw_product_selected) {
			if (raw_product_selected[a]['selected_id'] === id) {	condition = 0;	}
		}
		if (condition === 0) { return false;	}

		var raw_pick = { "selected_id" : id, "selected_code" : code, "selected_description" : description };
		this.array_product_selected.push(raw_pick);		
		this.render_group();	
	}
	render_group () {
		const raw_product_selected = this.array_product_selected;
		var tbl_group = '';
		for (const a in raw_product_selected) {
			tbl_group += `
			<tr>
				<td>${raw_product_selected[a]['selected_code']}</td>
				<td class="cursor_pointer" ondblclick="cls_management.upd_product(${replace_special_character(raw_product_selected[a]['selected_id'])})">${replace_special_character(raw_product_selected[a]['selected_description'])}</td>
				<td class="al_center">
					<button type="button" class="btn btn-danger btn-sm" onclick="cls_management.del_line(${a})"><i class="fa fa-times"></i></button>
				</td>
			</tr>`
		}	
		$("#tbl_group tbody").html(tbl_group);
	}
	del_line (key){
		this.array_product_selected.splice(key,1);
		this.render_group();
	}
	filter_product(str){
		var intervalo = this.intervalo;
		clearInterval(intervalo);
		var intervalo = setInterval(function(){
			var limit = ($("input[name=r_limit]:checked").val());
			var data = {"a" : str, "b" : limit, "cls" : "class_product", "mtd" : "filter_product"};
			var url_data = data_fetch(data);
			
			var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
			fetch(myRequest)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					var raw_product = JSON.parse(text);
					var tbl_content = '';
					for (const a in raw_product) {
						tbl_content += `
							<tr onclick='cls_management.pick_line("${raw_product[a]['AI_producto_id']}","${raw_product[a]['TX_producto_codigo']}","${replace_regular_character(raw_product[a]['TX_producto_value'])}")'>
								<td>${raw_product[a]['TX_producto_codigo']}</td>
								<td>${replace_special_character(raw_product[a]['TX_producto_value'])}</td>
							</tr>
						`;
					}
					$("#tbl_product tbody").html(tbl_content);
				});
			});
			clearInterval(intervalo);
		}, 700);
	}
	upd_product (product_id) {
		var href = `popup_updproduct.php?a=${product_id}`;
		var target = 'popup';	var w = '1010';	var h = '665';
		open_popup(href,target,w,h);
	}
	// ACTIONS
	block_group (){	
		var message = (document.getElementById("cb_disable").checked == true) ? 'Se bloquear\u00E1n los productos, ¿Continuar?' : 'Procedemos a Desbloquear, ¿Continuar?';
		if (document.getElementById("cb_disable").checked == true) {	var method = 'enable_group';	}else{	var method = 'disable_group';	}
		if(confirm(message)){
			var data = {a : this.array_product_selected};			
			var url_data = data_fetch({cls : "class_product", mtd : method});
			var miInit = {method: 'POST',body: JSON.stringify(data),headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default' };
			var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
			fetch(myRequest, miInit)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					var array_obj = JSON.parse(text);
					var msg = array_obj['message'];
					shot_snackbar(msg);
				});
			});
		}
	}

	count_group () {
		var message = (document.getElementById("cb_count").checked == true) ? 'Se quitar\u00E1n los conteos, ¿Continuar?' : 'Procedemos a Marcar como contado, ¿Continuar?';
		if (document.getElementById("cb_count").checked == true) { var method = 'count_group';	}else{	var method = 'uncount_group';	}
		if (confirm(message)) {
			var data = {a : this.array_product_selected};			
			var url_data = data_fetch({cls : "class_product", mtd : method});
			var miInit = {method: 'POST',body: JSON.stringify(data),headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default' };
			var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
			fetch(myRequest, miInit)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					var array_obj = JSON.parse(text);
					var x = document.getElementById("snackbar");
					x.innerHTML = array_obj['message'];
					x.className = "show";
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
				});
			});
		}
	}

	set_to_cero () {
		if (confirm("¿Seguro desea contar a Cero este grupo?")) {
			var data = {a : this.array_product_selected};			
			var url_data = data_fetch({cls : "class_product", mtd : 'set_to_cero'});
			var miInit = {method: 'POST',body: JSON.stringify(data),headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default' };
			var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
			fetch(myRequest, miInit)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					var array_obj = JSON.parse(text);
					var x = document.getElementById("snackbar");
					x.innerHTML = array_obj['message'];
					x.className = "show";
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
				});
			});
		}
	}

	set_modal (){
		const array_selected = this.array_product_selected; var counter = 0;
		for (const a in array_selected) {	counter++;	}
		if (counter < 1) {	
			shot_snackbar('Agregue los productos primero.');
			return false;
		}
		$("#txt_group_title").val('');
		setTimeout(function(){	$("#txt_group_title").focus();	},500);
		modal_in('mod_set_savegroup');
	}

	save_group () {
		var group_title = document.getElementById("txt_group_title").value;
		group_title = replace_regular_character(group_title);
		var regex = RegExp("\\w", "g");
		if (!regex.test(group_title)) {	shot_snackbar ('Ingrese un Titulo Adecuado.');	return false;	}

		const array_selected = this.array_product_selected;
		var data = {a : group_title, b : array_selected};	
		var url_data = data_fetch({cls : "class_product", mtd : 'validate_group'});
		var miInit = {method: 'POST',body: JSON.stringify(data),headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default' };
		var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
		fetch(myRequest, miInit)
		.then(function(response) {
			return response.text()
			.then(function(text) {
				var array_obj = JSON.parse(text);
				if (array_obj['success'] == 0) {	shot_snackbar(array_obj['message']);	return false;	}
				var url_data = data_fetch({cls : "class_product", mtd : 'save_group'});
				var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
				fetch(myRequest, miInit)
				.then(function(response) {
					return response.text()
					.then(function(text) {
						var array_obj = JSON.parse(text);
						shot_snackbar(array_obj['message']);
						modal_out('mod_set_savegroup');
					});
				});
			});
		});
	}
	del_group (group_id) {
		if (confirm("¿Seguro desea contar eliminar este grupo?")) {
			var data = {a : group_id};			
			var url_data = data_fetch({cls : "class_product", mtd : 'delete_group'});
			var miInit = {method: 'DELETE',headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default',
			body: JSON.stringify(data)};
			var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
			fetch(myRequest, miInit)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					var array_obj = JSON.parse(text);
					var message = array_obj['message'];
					shot_snackbar(message);
					var content_grouplist = '';
					const array_group = JSON.parse(array_obj['array_group']);
					for (const a in array_group) {
						content_grouplist += `
							<tr>
								<td class="al_center"  onclick='cls_management.pick_group("${array_group[a]['AI_productogrupo_id']}")'>
									${replace_special_character(array_group[a]['TX_productogrupo_titulo'])}
								</td>
								<td class="al_center">
									<button type="button" class="btn btn-danger btn-sm" onclick="cls_management.del_group(${array_group[a]['AI_productogrupo_id']})"><i class="fa fa-times"></i></button>
								</td>
							</tr>
						`;
					}
					$("#tbl_productgroup tbody").html(content_grouplist);
				});
			});
		}
	}	
	async pick_group (group_id) {
		var data = {a : group_id};			
		var url_data = data_fetch({cls : "class_product", mtd : 'pick_group'});
		var miInit = {method: 'POST',body: JSON.stringify(data),headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default' };
		var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
		const response = await fetch(myRequest, miInit);
		const array_obj = await response.json();
		var raw_product_selected = array_obj['array_group'][0]['TX_productogrupo_json'];
		var array_selected = JSON.parse(raw_product_selected);
		this.array_product_selected = array_selected;
					
		var tbl_group = '';
		for (const a in array_selected) {
			tbl_group += `
			<tr>
				<td>${array_selected[a]['selected_code']}</td>
				<td class="cursor_pointer" ondblclick="cls_management.upd_product(${array_selected[a]['selected_id']})">${replace_special_character(array_selected[a]['selected_description'])}</td>
				<td class="al_center">${array_selected[a]['selected_quantity']}</td>
			</tr>`;
		}	
		$("#tbl_group tbody").html(tbl_group);
	}
	discount_group (){
		var message = (document.getElementById("cb_discount").checked === true) ? 'Se habilitara descontable, ¿Continuar?' : 'Se deshabilitara descontable, ¿Continuar?';
		if (document.getElementById("cb_discount").checked == true) { var method = 'discount_group';	}else{	var method = 'undiscount_group';	}
		if (confirm(message)) {
			var data = {a : this.array_product_selected};			
			var url_data = data_fetch({cls : "class_product", mtd : method});
			var miInit = {method: 'POST',body: JSON.stringify(data),headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default' };
			var myRequest = new Request(`attached/get/class_product_management.php${url_data}`);
			fetch(myRequest, miInit)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					var array_obj = JSON.parse(text);
					var msg = array_obj['message'];
					shot_snackbar(msg);
				});
			});
		}

	}
}
product_management.prototype.array_product_selected = new Array;


