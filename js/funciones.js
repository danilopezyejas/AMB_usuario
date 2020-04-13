function paraVer(id_pelicula){
    $.ajax(
        {url: "/tip/framework/pelicula/agregar_lista/",
        data: { id_pelicula: id_pelicula},
        method: "POST",
         dataType: "json", 
         success: function(json) {
            if(json.res==1){
                //todo ok
                alert(json.msj);
                $('#pver_'+id_pelicula).hide(); //display:none
                $('#nover_'+id_pelicula).show();
            }else{
                alert(json.msj);
            }
        }
    });
    //cargarParaVer();

    //ejecutar el llamado ajax a pelicula/agregar_lista/ -> demora 1 min
    //ejecuta cargarParaVer();
    // cuando pasa 1 min, ejecutar success()
}
function noVer(id_pelicula){
	$.ajax(
    	{url: "/tip/framework/pelicula/quitar_lista/",
    	data: { id_pelicula: id_pelicula},
    	method: "POST",
     	dataType: "json", 
     	success: function(json) {
    		if(json.res==1){
    			//todo ok
    			alert(json.msj);
                $('#pver_'+id_pelicula).show();
                $('#nover_'+id_pelicula).hide();
    		}else{
				alert(json.msj);
    		}
    	}
    });
    //cargarParaVer();

    //ejecutar el llamado ajax a pelicula/agregar_lista/ -> demora 1 min
    //ejecuta cargarParaVer();
    // cuando pasa 1 min, ejecutar success()
}

function cargarParaVer(id_usuario){
	$('.modal-body').html("Id Usuario "+id_usuario);

	// Display Modal
	$('#pverModal').modal('show');

}


