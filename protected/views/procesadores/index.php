<?php
/* @var $this ProcesadoresController */
/* @var $model Procesadores */

$this->breadcrumbs = array(
    'Procesadores' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear Procesador', 'url' => array('create')),
);
?>

<h1>Control de procesadores</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'procesadores-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word;'),
    'columns' => array(
        'nombreProcesador',
        'especificaciones',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
