function verificar(){
    var p1 = $('#password').val()
    var p2 = $('#password2').val()

    if (p1 == p2){
        $('#prueba').html(<h1>password iguales</h1>)
    }
}