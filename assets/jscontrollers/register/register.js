/**
 * Ajax action to api rest
*/
function register(){
    var $ocrendForm = $(this), __data = {};
    $('#register_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/register",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    alert(json.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert(json.message);
                }
            },
            error : function(xhr, status) {
                alert('Ha ocurrido un problema interno');
            },
            complete: function(){ 
                $ocrendForm.data('locked', false);
            } 
        });
    }
} 

/**
 * Events
 */
$('#register').click(function(e) {
    e.defaultPrevented;
    register();
});
$('form#register_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        register();

        return false;
    }
});