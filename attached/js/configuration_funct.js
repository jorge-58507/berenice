// JavaScript Document

function get_config (str) {
  $.ajax({	data: "",	type: "GET",	dataType: "text",	url: "attached/php/"+str, })
   .done(function( data, textStatus, jqXHR ) {
     document.getElementById("container_target").innerHTML = data;
    })
   .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

// function act_area (area_id) {
//   fetch(`attached/get/area_method.php?a=${area_id}&b=activate`)
//     // .then(function(response) {
//     //   console.log(response);
//     //   return response.json();
//     // })
//     // .then(function(myJson) {
//     //   console.log('Mostrar'+ myJson);
//     //   console.log(typeof(myJson));
//     // });
//     .then(function(response) {
//       return response.json();
//     })
//     .then(function(myJson) {
//       console.log(myJson);
//       console.log(typeof(myJson));
//     });
//   }
  function act_area (area_id) {
    data = {"a":area_id,"b":'activate'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/area_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_area').click();
      });
    });
  }
  function des_area (area_id) {
    var myRequest = new Request(`attached/get/area_method.php?a=${area_id}&b=desactivate`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_area').click();
        // console.log(text);
      });
    });
  }
  function add_area () {
    const area_description = document.getElementById('txt_area_description').value;
    var myRequest = new Request(`attached/get/area_method.php?a=${area_description}&b=add`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_area').click();
        // console.log(text);
      });
    });
  }
  function add_medida () {
    var description = document.getElementById('txt_medida_description').value;
    description = description.toUpperCase();
    var myRequest = new Request(`attached/get/medida_method.php?a=${description}&b=add`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_medida').click();
      });
    });
  }
  function des_medida (medida_id) {
    var myRequest = new Request(`attached/get/medida_method.php?a=${medida_id}&b=desactivate`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_medida').click();
      });
    });
  }
  // ########################## FAMILIA  ################################
  function add_familia () {
    var prefix = document.getElementById('txt_clasification_prefijo').value;
    var description = document.getElementById('txt_clasification_description').value;
    description = description.toUpperCase();
    description = url_replace_regular_character(description);
    var myRequest = new Request(`attached/get/clasification_method.php?a=${description}&b=add&c=${prefix}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_familia').click();
      });
    });
  }
  function set_subfamilia (familia_id,familia_value) {
    document.getElementById("txt_clasification_subfamilia").setAttribute('alt', familia_id);
    var myRequest = new Request(`attached/get/clasification_method.php?a=${familia_id}&b=set_subfamilia`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        var raw_subfamilia = JSON.parse(text);
        var tbody = '';
        for (var i in raw_subfamilia) {
          if (raw_subfamilia.hasOwnProperty(i)) {
            var background = (raw_subfamilia[i]['TX_subfamilia_status'] === '0') ? '#feadad' : '#fff';
            tbody += `
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1" style="background-color:${background};">
              <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell br_1 al_center">
                ${raw_subfamilia[i]['TX_subfamilia_value']}
              </div>
              <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center">
                ${raw_subfamilia[i]['TX_subfamilia_prefijo']}
              </div>
              <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1 al_center">
                ${raw_subfamilia[i]['conteo']}
              </div>
              <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 cell br_1 al_center">
                <button type="button" class="btn btn-success btn-sm btn_squared_sm" onclick="act_subfamilia(${raw_subfamilia[i]['AI_subfamilia_id']});" name="button"><i class="fa fa-check"></i></button>
                &nbsp;
                <button type="button" class="btn btn-danger btn-sm btn_squared_sm" onclick="des_subfamilia(${raw_subfamilia[i]['AI_subfamilia_id']});" name="button"><i class="fa fa-times"></i></button>
              </div>
            </div>
            `;
          }
        }

        $("#tbl_subfamilia>#content_dbody").html(tbody);
      });
    });
    // #######     SHOW     ########
    document.getElementById("container_subfamilia").style.display = 'block';
    document.getElementById("caption_subfamilia").innerHTML = familia_value;
  }
  function act_familia (familia_id) {
    data = {"a":familia_id,"b":'activate'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/clasification_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_familia').click();
      });
    });
  }
  function des_familia (familia_id) {
    data = {"a":familia_id,"b":'desactivate'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/clasification_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        document.getElementById('btn_config_familia').click();
      });
    });
  }
  //   ####################     SUBFAMILIA     #################
  function add_subfamilia () {
    var description = document.getElementById('txt_clasification_subfamilia').value;
    var subprefijo = document.getElementById('txt_clasification_subprefijo').value;
    description = description.toUpperCase();
    description = url_replace_regular_character(description);
    subprefijo = url_replace_regular_character(subprefijo);
    var familia_id = document.getElementById('txt_clasification_subfamilia').getAttribute('alt');
    var myRequest = new Request(`attached/get/clasification_method.php?a=${description}&b=add_subfamilia&c=${familia_id}&d=${subprefijo}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        set_subfamilia(familia_id,'');
        // document.getElementById('btn_config_familia').click();
      });
    });
  }
  function des_subfamilia (subfamilia_id) {
    data = {"a":subfamilia_id,"b":'desactivate_subfamilia'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/clasification_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        var familia_id = document.getElementById('txt_clasification_subfamilia').getAttribute('alt');
        set_subfamilia(familia_id,'');
      });
    });
  }
  function act_subfamilia (subfamilia_id) {
    data = {"a":subfamilia_id,"b":'activate_subfamilia'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/clasification_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        var familia_id = document.getElementById('txt_clasification_subfamilia').getAttribute('alt');
        set_subfamilia(familia_id,'');
      });
    });
  }


  //    #######################   CONFIGURAR IMPRESORAS   ###################

  function set_printer (printer_id) {
    data = {"a":printer_id,"b":'get_printer'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/printer_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        raw_data = JSON.parse(text);
        document.getElementById("txt_printer_client").value = raw_data['client'];
        document.getElementById("txt_printer_serial").value = raw_data['serial'];
        document.getElementById("txt_printer_serial").setAttribute('alt',printer_id);
        document.getElementById("txt_printer_serial").setAttribute('disabled','disabled');
        document.getElementById("txt_printer_seudonim").value = raw_data['seudonim'];
        document.getElementById("txt_printer_recipient").value = raw_data['recipient'];
        document.getElementById("txt_printer_return").value = raw_data['return'];
        document.getElementById("txt_printer_till").value = raw_data['till'];
      });
    });
  }

  function save_printer () {
    var client = document.getElementById("txt_printer_client").value;
    var serial = document.getElementById("txt_printer_serial").value;
    var printer_id = document.getElementById("txt_printer_serial").getAttribute('alt');
    var seudonim = document.getElementById("txt_printer_seudonim").value;
    var recipient = document.getElementById("txt_printer_recipient").value;
    var return_folder = document.getElementById("txt_printer_return").value;
    var till = document.getElementById("txt_printer_till").value;

    data = {"a":printer_id,"b":'save_printer',"client":client,"serial":serial,"seudonim":seudonim,"recipient":recipient,"return_folder":return_folder,"till":till}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/printer_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        raw_data = JSON.parse(text);
        document.getElementById("txt_printer_client").value = '';
        document.getElementById("txt_printer_serial").value = '';
        document.getElementById("txt_printer_serial").setAttribute('alt','');
        document.getElementById("txt_printer_seudonim").value = '';
        document.getElementById("txt_printer_recipient").value = '';
        document.getElementById("txt_printer_return").value = '';
        document.getElementById("txt_printer_till").value = '';
        console.log(raw_data['message']);
        document.getElementById("btn_config_printer").click();        
      });
    });
  }
  function del_printer (printer_id) {
    data = {"a":printer_id,"b":'del_printer'}
    url_data = data_fetch(data);
    var myRequest = new Request(`attached/get/printer_method.php${url_data}`);
    fetch(myRequest)
    .then(function(response) {
      return response.text()
      .then(function(text) {
        raw_data = JSON.parse(text);
        console.log(raw_data['message']);
        document.getElementById("btn_config_printer").click();
      });
    });
  }
