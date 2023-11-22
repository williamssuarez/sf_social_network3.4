$ (document).ready(function(){

    //IAS DE INFINITE AJAX SCROLL
    //ESTO PARA LA PAGINACION INFINITA, LE ESPECIFICAMOS DONDE SE APLICARA DICHA PAGINACION
    var ias = jQuery.ias({
        container: '#timeline .box-content',
        item: '.publication-item',
        pagination: '#timeline .pagination',
        next: '#timeline .pagination .next_link',
        triggerPageThreshold: 5
    });

    //ESTO DEJAR DE CARGAR LA LISTA AL PASAR LAS 3 CARGAS, DESPUES DE LE PIDE AL USUARIO SI QUIERE VER MAS CON EL VER MAS
    ias.extension(new IASTriggerExtension({
        text: 'Ver mas publicaciones',
        offset: 3
    }));

    //MOSTRANDO UNA IMAGEN CUANDO CARGA LA SOLICITUD
    ias.extension(new IASSpinnerExtension({
        src: URL+'/../assets/images/ajax-loader.gif'
    }));

    //MOSTRANDO TEXTO CUANDO YA NO HAY MAS QUE MOSTRAR
    ias.extension(new IASNoneLeftExtension({
        text: 'No hay mas publicaciones'
    }));

    ias.on('ready', function (event) {
        buttons();
    });

    ias.on('rendered', function (event){
        buttons();
    });
});

//ESTA FUNCION SERVIRA DESPUES PARA LOS BOTONES DE LIKE DE PUBLICACIONES Y DOCUMENTOS, ETC
function buttons(){

    $('[data-toggle="tooltip"]').tooltip();

    $(".btn-img").unbind("click").click(function (){
        $(this).parent().find('.pub-image').fadeToggle();
    })

    $(".btn-delete-pub").unbind('click').click(function (){
        $(this).parent().parent().addClass('hidden');

        $.ajax({
            url: URL+'/publication/remove/'+$(this).attr("data-id"),
            type: 'GET',
            success: function (response){
                console.log(response);
                alertify.error(response).position('top-right');
            }
        })
    })

    $(".btn-like").unbind('click').click(function (){
        $.ajax({
            url: URL+'/like/'+$(this).attr("data-id"),
            type: 'GET',
            success: function (response){
                console.log(response);
            }
        })
    })
}