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
            "header" => "Estatus",
            "value" => '$data["status"]',
        ),
        array(
            "header" => "Equipo",
            "value" => '$data["equipo"]',
        ),
        array(
            "header" => "Serie",
            "value" => '$data["serie"]',
        ),
        array(
            "header" => "Memoria",
            "value" => '$data["memoria"]',
        ),
        array(
            "header" => "Procesador",
            "value" => '$data["procesador"]',
        ),
        array(
            "header" => "Disco duro",
            "value" => '$data["discoDuro"]',
        ),
        array(
            "header" => "Colaborador",
            "value" => '$data["usuarioNombre"]',
        ),
        array(
            "header" => "Nombre equipo",
            "value" => '$data["nombreEquipo"]',
        ),
        array(
            "header" => "Departamento",
            "value" => '$data["departamento"]',
        ),
        array(
            "header" => "Gerencia",
            "value" => '$data["gerencia"]',
        ),
        array(
            "header" => "Dirección",
            "value" => '$data["direccion"]',
        ),
    ),
)); ?>
