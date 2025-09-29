<?php
$this->widget('bootstrap.widgets.TbGridView',array(
    'id'=>'result-grid',
    'dataProvider'=>$dataProvider,
    'htmlOptions' => array('style' => 'word-wrap:break-word;'),
    'type' => 'striped bordered condensed hover',
    'pagerCssClass' => 'pagination pagination-centered',
    'columns'=>array(
        array(
            "header" => "Hotel",
            "value" => '$data["nombreHotel"]',
        ),
        array(
            "header" => "Marca",
            "value" => '$data["nombremarca"]',
        ),
        array(
            "header" => "Modelo",
            "value" => '$data["nombremodelo"]',
        ),
        array(
            "header" => "Proveedor",
            "value" => '$data["nombreProveedor"]',
        ),
        array(
            "header" => "Tipo",
            "value" => '$data["nombreTipoEquipo"]',
        ),
        array(
            "header" => "Colaborador",
            "value" => '$data["usuarioNombre"]',
        ),
        array(
            "header" => "Estado",
            "value" => '$data["estado"]',
        ),
        array(
            "header" => "Fecha préstamo",
            "value" => '$data["fecha_prestamo"]',
        ),
        array(
            "header" => "Fecha devolución",
            "value" => '$data["fecha_devolucion"]',
        ),
    ),
)); ?>
