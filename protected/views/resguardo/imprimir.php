<html>
    <head>
        <title>SISTEMA DE INVENTARIO TI</title>
    </head>

    <?php

    function quitar_tildes($cadena) {

        $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
        $permitidas = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&Aacute;", "&Eacute;", "&Iacute;", "&Oacute;", "&Uacute;", "&ntilde;", "&Ntilde;", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        $texto = str_replace($no_permitidas, $permitidas, $cadena);
        return $texto;
    }

    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/Js/imprimirResguardo.js', CClientScript::POS_END);
    ?>

    <body>
        <table cellpadding="0" cellspacing="0" border="0" width="900" align="center" datasrc="#myxml" id="resguardos" style="font-family: sans-serif;">
            <thead>
                <tr class="texto" style="text-transform:uppercase;font-weight:bold;" >
                    <td id="encabezado" width="250px" style="border-bottom:2px #dddddd solid;border-top:2px #dddddd solid;padding-left:5;">
                        <img  width="70" height="50" style="padding-top: 5px; padding-bottom: 5px"src="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
                    </td>
                    <td id="encabezado"  style="border-bottom:2px #dddddd solid;border-top:2px #dddddd solid;padding-left:5;" >
                        <span style="font-size: large;  padding-LEFT:10px "><?php echo quitar_tildes("tecnologia de la información") ?></span><br>
                        <span style="font-size: large; "><?php echo quitar_tildes("Resguardo de equipo de cómputo") ?></span><br>

                    </td>

                </tr>

                <tr style="height:15px;"><td></td></tr>
                <tr class="texto" style="text-transform:uppercase;" >
                    <td id="hoteldepto" colspan="2" >
                        <span style="font-size:normal;"><?php echo $model->idColaboradorEmpleado0->usuarioNombre; ?>
                            &nbsp;&nbsp; <?php echo $model->idColaboradorEmpleado0->hotel->nombreHotel; ?>  <span id="HotelNombre" style="font-weight:normal;"></span></span>

                        &nbsp;&nbsp;  <span><?php echo $model->idColaboradorEmpleado0->departamentoIddepartamento->nombredepartamento ?><span id="DeptoNombre" style="font-weight:normal;"></span></span>
                        &nbsp;&nbsp;  <span><strong>Nombre equipo: </strong> <?php echo $model->nombreEquipo ?><span id="DeptoNombre" style="font-weight:normal;"></span></span>
                    </td>
                </tr>
                <tr style="height:15;"><td></td></tr>
                <tr>
                    <td colspan="2" >
                        <span style="font-size:small;font-family: sans-serif;"><?php echo quitar_tildes("Recibí en resguardo los equipos que a continuación se describen:") ?></span>
                    </td>
                </tr>
            </thead>
            <tbody id="Cuerpo">

                <tr class="texto" style="font-size:small;text-transform:uppercase; ">


                    <?php
                    $this->widget('bootstrap.widgets.TbGridView', array(
                        'id' => 'equipo',
                        'dataProvider' => $dataProviderEquipo,
                        'type' => 'striped bordered condensed hover',
                        'enablePagination' => true,
                        'ajaxUpdate' => true,
                        'htmlOptions' => array('style' => 'word-wrap:break-word;'),
                        'columns' => array(
                            array(
                                'value' => '$data->idTipoEquipo0->nombreTipoEquipo',
                                'htmlOptions' => array('style' => 'width:150px'),
                            ),
                            array(
                                'value' => '$data->idMarca0->nombremarca',
                                'htmlOptions' => array('style' => 'width:150px'),
                            ),
                            array(
                                'value' => '$data->idModelo0->nombremodelo',
                                'htmlOptions' => array('style' => 'width:150px'),
                            ),
                            array(
                                'value' => '"No.Serie:"',
                                'htmlOptions' => array('style' => 'width:10px'),
                            ),
                            array(
                                'value' => '$data->numeroSerie',
                                'htmlOptions' => array('style' => 'width:150px'),
                            ),
                        ),
                        'emptyText' => 'Este resguardo no contiene equipos asignados',
                    ))
                    ?>
            <p align="justify" style="font-size:small; font-family: sans-serif;">
            <td><?php echo $model->comentarios; ?></td>
        </p>
    </tr>
</tbody>
<tfoot>
    <tr><td style="height:20;"></td></tr>
    <tr>
        <td id="piepagina" class="texto">

            <p align="justify" style="font-size:small; font-family: sans-serif;">        <?php
                    echo quitar_tildes(
                            "Acepto que he recibido equipo y accesorios que se describen, 
                                estoy obligado a conservarlos en buen estado, deberé utilizarlo
                                exclusivamente para actividades laborales asignadas e informar al 
                                área de Soporte Tecnico cualquier avería o falla que pueda 
                                presentarse durante la operación normal del equipo, cuando la empresa
                                así lo requiera podrá revisarlo para validar su estado por lo que
                                deberé mantenerlo y resguardarlo adecuadamente en mi centro de trabajo,
                                aceptare la responsabilidad por mal uso o daño que pueda ocasionarle.")
                    ?>
            </p>
            <p align="justify" style="font-size:small; font-family: sans-serif;">
                <?php echo quitar_tildes(
                    "Hereby, I accept that I have received the equipment  and accessories described herein, 
                    and I am obliged to maintain them in good condition, and I must use them exclusively for 
                    the assigned work activities and inform the Technical Support area about any breakdown or 
                    failure that may occur during the regular operation of them. 
                    The company can request an inspection of the equipment anytime, in order to validate its 
                    status and hence, I must maintain it and keep it safely in my work place. I will accept 
                    any liability resulting from the misuse or damage to it."
                ); ?>
            </p>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="texto" style="font-weight:bold;">

                <tr style="height:70;vertical-align:bottom;text-align:center;">
                <?php if ($model->idColaboradorEmpleado0->isInternal()) { ?>
                    <td width="40%"></td>
                <?php } else { ?>
                    <td width="40%"><span ><?php echo $model->idColaboradorEmpleado0->numeroColaborador ?></span>&nbsp;&nbsp;/&nbsp;&nbsp;<span id="NombreC"><?php echo $model->idColaboradorEmpleado0->usuarioNombre; ?></span></td>
                <?php } ?>
                    <td width="40%"><span ><?php echo Yii::app()->user->name ?></span></td>
                </tr>
                <tr>
                    <td width="40%" align="center" style="border-top:1px solid #dddddd;">Firma del Colaborador</td>
                    <td width="40%" align="center" style="border-top:1px solid #dddddd;"><?php echo quitar_tildes("Firma de Soporte Técnico") ?>  </td>
                </tr>
            </table>
        </td>
    </tr>
</tfoot>
</table>
    <div style="position:absolute; bottom:0;">
        <span><strong>Fecha: </strong> <?php echo date('d-m-Y'); ?></span>  
    </div>
</body>
</html>
<style>
    #equipo_c0,#equipo_c1,#equipo_c2,#equipo_c3
    {color:#0088cc;}

    .table {

        margin-bottom: 20px;
    }
    table {

        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
        border-color: gray;
    }
    user agent stylesheettable {
        display: table;
        border-collapse: separate;
        border-spacing: 2px;
        border-color: gray;
    }


    Inherited from html

    .table thead th {
        vertical-align: bottom;
    }

    .table-condensed th, .table-condensed td {
        padding: 4px 5px;
    }
    .table th {
        font-weight: bold;
    }
    .table th, .table td {
        padding: 6px;

        text-align: left;
        vertical-align: top;

    }

    th {
        display: table-cell;
        vertical-align: inherit;
    }
    .summary{display: none;}
</style>   