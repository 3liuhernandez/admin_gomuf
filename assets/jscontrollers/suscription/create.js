/**
 * Ajax action to api rest
*/
function suscription(){
    var $ocrendForm = $('#suscription_form'), __data = {};
    $ocrendForm.serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {

        var l = Ladda.create(document.querySelector('#suscription_btn'));
        l.start();

        $.ajax({
            type : "POST",
            url : "api/suscription/create",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    success_toastr('Éxito', json.message);
                    setTimeout(function() {
                        location.href = json.url;
                    }, 1000);
                } else {
                    error_toastr('Ups!', json.message);
                }
            },
            error : function(xhr, status) {
                error_toastr('Ups!', 'Ha ocurrido un problema interno');
            },
            complete: function(){ 
                $ocrendForm.data('locked', false);
                l.stop();
            } 
        });
    }
} 

/**
 * Events
 */

$('form#suscription_form').submit(function(e) {
    e.defaultPrevented;
    suscription();
    return false;
    
});
