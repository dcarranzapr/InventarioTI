<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */

$this->breadcrumbs = array(
    'Equipogenerals' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Captura de inventario', 'url' => array('create')),
    array('label' => 'Transferencia entre hoteles', 'url' => array('cambio')),
    array('label' => 'Aceptar transferencia entre hoteles', 'url' => array('autorizaciones')),
    array('label' => 'Creación multiple', 'url' => array('createMultiple')),
);
?>

<h1>Control de equipos en inventario</h1>




<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'equipogeneral-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'columns' => array(
        array(
            'name' => 'nombre_hotel',
            'value' => '$data->idHotel0->nombreHotel',
            'filter' => Equipogeneral::getListHotel(),
            'htmlOptions' => array('style' => 'width:90px'),
        ),
        array(
            'name' => 'nombre_tipo',
            'value' => '$data->idTipoEquipo0->nombreTipoEquipo',
            'filter' => Equipogeneral::getListTipo1(),
            'htmlOptions' => array('style' => 'width:80px'),
        ),
        array(
            'name' => 'nombre_marca',
            'value' => '$data->idMarca0->nombremarca',
            'filter' => $model->nombre_tipo ?
                    Equipogeneral::getListprueba($model->nombre_tipo) :
                    Equipogeneral::getListprueba(),
            'htmlOptions' => array('style' => 'width:70px'),
        ),
        array(
            'name' => 'nombre_modelo',
            'value' => '$data->idModelo0->nombremodelo',
            'filter' => $model->nombre_marca ?
                    CHtml::listData(Modelo::model()->with('fkidMarca0')->findAllByAttributes(
                                    array(), "nombremarca = :marca", array(':marca' => $model->nombre_marca)), 'nombremodelo', 'nombremodelo') :
                    CHtml::listData(Modelo::model()->with('fkidMarca0')->findAll(), 'nombremodelo', 'nombremodelo'),
            'htmlOptions' => array('style' => 'width:90px'),
        ),
        array(
            'name' => 'nombre_estado',
            'value' => '$data->idEstatus0->descripcion',
            'filter' => Equipogeneral::getListEstado1(),
            'htmlOptions' => array('style' => 'width:60px'),
        ),
        array(
            'name' => 'numeroSerie',
            'htmlOptions' => array('style' => 'width:60px'),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
            'htmlOptions' => array('style' => 'width:40px'),
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>

