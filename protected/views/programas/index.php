<?php
/* @var $this ProgramasController */
/* @var $model Programas */

$this->breadcrumbs = array(
    'Programases' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear Programa', 'url' => array('create')),
);
?>

<h1>Control de programas</h1>




<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'programas-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word;'),
    'columns' => array(
        'id',
        'nombre',
        'observaciones',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
