<?php
/* @var $this ModeloController */
/* @var $model Modelo */

$this->breadcrumbs = array(
    'Modelos' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear Modelo', 'url' => array('create')),
);
?>
<h1>Control de modelos</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'modelo-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; '),
    'columns' => array(
        array('name' => 'nombre_tipo',
            'filter' => Modelo::getListTipo1(),
            'value' => '$data->fkidTipoEquipo0->nombreTipoEquipo',
        ),
        array('name' => 'nombre_marca',
            'filter' => Modelo::getListMarca1(),
            'value' => '$data->fkidMarca0->nombremarca',
        ),
        'nombremodelo',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
