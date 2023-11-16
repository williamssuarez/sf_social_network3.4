$ (document).ready(function(){

    //IAS DE INFINITE AJAX SCROLL
    //ESTO PARA LA PAGINACION INFINITA, LE ESPECIFICAMOS DONDE SE APLICARA DICHA PAGINACION
    var ias = jQuery.ias({
        container: '.box-users',
        item: '.user-item',
        pagination: '.pagination',
        next: '.pagination .next_link',
        triggerPageThreshold: 5
    });

    //ESTO DEJAR DE CARGAR LA LISTA AL PASAR LAS 3 CARGAS, DESPUES DE LE PIDE AL USUARIO SI QUIERE VER MAS CON EL VER MAS
    ias.extension(new IASTriggerExtension({
        text: 'Ver mas',
        offset: 3
    }));

    //MOSTRANDO UNA IMAGEN CUANDO CARGA LA SOLICITUD
    ias.extension(new IASSpinnerExtension({
        src: URL+'/../assets/images/ajax-loader.gif'
    }));

    //MOSTRANDO TEXTO CUANDO YA NO HAY MAS QUE MOSTRAR
    ias.extension(new IASNoneLeftExtension({
        text: 'No hay mas personas'
    }));

    ias.on('ready', function (event) {
        followButtons();
    });

    ias.on('rendered', function (event){
        followButtons();
    });
});

function followButtons(){
    $(".btn-follow").unbind("click").click(function () {
        $.ajax({
           url: URL+'/follow',
            type: 'POST',
            data: {followed: $(this).attr("data-followed")},
            success: function (response) {
               console.log(response);
            }
        });
    })
}