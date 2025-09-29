<?php

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'equipogeneral-grid',
    'selectableRows' => 1,
    'selectionChanged' => 'mostrarDetalles',
    'dataProvider' => $model3->searchEquipoDesasignado(),
    'filter' => $model3,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word;'),
    'columns' => array(
        array(
            'name' => 'nombre_tipo',
            'value' => '$data->idTipoEquipo0->nombreTipoEquipo',
            'filter' => Equipogeneral::getListTipo1(),
            'htmlOptions' => array('style' => 'width:80px'),
        ),
        array(
            'name' => 'nombre_marca',
            'value' => '$data->idMarca0->nombremarca',
            'filter' => $model3->nombre_tipo ?
                    Equipogeneral::getListprueba($model3->nombre_tipo) :
                    Equipogeneral::getListprueba(''),
            'htmlOptions' => array('style' => 'width:70px'),
        ),
        array(
            'name' => 'nombre_modelo',
            'value' => '$data->idModelo0->nombremodelo',
            'filter' => $model3->nombre_marca ?
                    CHtml::listData(Modelo::model()->with('fkidMarca0')->findAllByAttributes(
                                    array(), "nombremarca = :marca", array(':marca' => $model3->nombre_marca)), 'nombremodelo', 'nombremodelo') :
                    CHtml::listData(Modelo::model()->with('fkidMarca0')->findAll(), 'nombremodelo', 'nombremodelo'),
            'htmlOptions' => array('style' => 'width:90px'),
        ),
        array(
            'name' => 'numeroSerie',
            'htmlOptions' => array('style' => 'width:60px'),
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));


