$(document).ready(function() {

    ocultar();
    $("input[name='yt0'] ").click(function() {

        $("#btnadjun").show();
        $("#equipo").show();


    });
});


var ocultar = function() {

    $("#btnadjun").hide();
    $("#equipo").hide();

};
          