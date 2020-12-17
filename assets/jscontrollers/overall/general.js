function deleteElement(controller, id) {


    swal({
        title: '¿Estás seguro?',
        text: "¡Usted no podrá revertir ésto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Eliminar',
        cancelButtonText: 'Cancelar',
    }, (result) => {
        if (result) {
            location.href = controller + '/delete/' + id;

            swal(
                'Eliminado!',
                'Elemento eliminado con éxito.',
                'success'
            );
        }
    });
}


function uniqid(a = "",b = false){
    var c = Date.now()/1000;
    var d = c.toString(16).split(".").join("");
    while(d.length < 14){
        d += "0";
    }
    var e = "";
    if(b){
        e = ".";
        var f = Math.round(Math.random()*100000000);
        e += f;
    }
    return a + d + e;
}



function is_empty(str){
    str = str.replace(/ /g, '+');
    if (str.length == 0 || str == '') return true;
    return false;
}

function ucfirst(str){
    return str.charAt(0).toUpperCase() + str.slice(1);
}


function isJSON(str){
    var jsonData = false;
    try {
        jsonData = JSON.parse(str);
    } catch (e) {
        return false;
    }
    return jsonData;
}