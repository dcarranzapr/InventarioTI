$(document).ready(function() {
    ocultar();
    $("select[name='Equipogeneral\\[idTipoEquipo\\]'] ").change(function() {
        var valor = $('#Equipogeneral_idTipoEquipo').children('option:selected').text()
        if (valor == 'CPU') { // Muestra los campos de CPU y oculta los demas campos.
            ocultar();
            mostrarDatoGeneral();
            $('#datosFactura').show();
            $('#comboFactura').show();
            $('#datosEquipo').show();
            $('#memoria').show();
            $('#sistemaOperativo').show();
            $('#procesador').show();
            $('#discoDuro').show();
            $('#tarjetaRed').show();
            $('#nombreCD').show();
            $('#plataforma').show();
            return;
        }
        if (valor == 'MONITOR') { // Muestra los campos de MONITOR y oculta los demas campos
            ocultar();
            mostrarDatoGeneral();

            return;
        }
        if (valor == 'LAPTOP') { // Muestra los campos de LAPTOP y oculta los demas campos
            $('#datosEquipo').show();
            $('#tamano').show();
            $('#memoria').show();
            $('#sistemaOperativo').show();
            $('#procesador').show();
            $('#discoDuro').show();
            $('#tarjetaRed').show();
            $('#nombreCD').show();
            $('#plataforma').show();
            mostrarDatoGeneral();
            return;
        }
        if (valor == 'SELECCIONE TIPO') { // Solo deja visible el campo de tipo los demas campos los oculta
            ocultar();
            return;
        }
        else
        {
            ocultar();
            mostrarDatoGeneral();
        }
    });
    var valor = $('#Equipogeneral_idTipoEquipo').children('option:selected').text()
    if (valor == 'CPU') { // Muestra los campos de CPU y oculta los demas campos.
        ocultar();
        mostrarDatoGeneral();
        $('#memoria').show();
        $('#sistemaOperativo').show();
        $('#procesador').show();
        $('#discoDuro').show();
        $('#tarjetaRed').show();
        $('#nombreCD').show();
        $('#plataforma').show();
        return;
    }
    if (valor == 'MONITOR') { // Muestra los campos de MONITOR y oculta los demas campos
        ocultar();
        mostrarDatoGeneral();
        $("#tamano").css("margin-left", "-7px");
        $('#tamano').show();
        return;
    }
    if (valor == 'LAPTOP') { // Muestra los campos de LAPTOP y oculta los demas campos
        $('#tamano').show();
        $('#memoria').show();
        $('#sistemaOperativo').show();
        $('#procesador').show();
        $('#discoDuro').show();
        $('#tarjetaRed').show();
        $('#nombreCD').show();
        $('#plataforma').show();
        mostrarDatoGeneral();
        return;
    }
    if (valor == 'SELECCIONE TIPO') { // Solo deja visible el campo de tipo los demas campos los oculta
        ocultar();
    }
    else
    {
        ocultar();
        mostrarDatoGeneral();
    }
});

//oculta todos los datos del formulario exepto tipo
var ocultar = function() {
    $('#memoria').hide();
    $('#datosFactura').hide();
    $('#comboFactura').hide();


    $('#datosEquipo').hide();
    $('#sistemaOperativo').hide();
    $('#procesador').hide();
    $('#discoDuro').hide();
    $('#tarjetaRed').hide();
    $('#nombreCD').hide();
    $('#hotel').hide();
    $('#marca').hide();
    $('#noSerie').hide();
    $('#factura').hide();
    $('#garantia').hide();
    $('#proveedor').hide();

    $('#modelo').hide();
    $('#plataforma').hide();
    $('#fechaCompra').hide();
    $('#tamano').hide();
};

//muestra los datos generales del formulario
var mostrarDatoGeneral = function() {
    $('#hotel').show();
    $('#marca').show();
    $('#noSerie').show();
    $('#modelo').show();
    $('#datosFactura').show();
    $('#comboFactura').show();
    if ($("#Equipogeneral_factura").val() == '0')
        $("#checkbox").prop("checked", "checked");
    if ($("#checkbox").prop("checked"))
    {
        $('#factura').hide();
        $('#proveedor').hide();
        $('#fechaCompra').hide();
    }
    else {
        $('#factura').show();
        $('#proveedor').show();
        $('#fechaCompra').show();
    }
};

         