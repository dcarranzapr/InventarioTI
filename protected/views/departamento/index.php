<?php
/* @var $this DepartamentoController */
/* @var $model Departamento */

$this->breadcrumbs = array(
    'Departamentos' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear departamento', 'url' => array('create')),
);
?>

<h1>Control de departamentos</h1>





<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'departamento-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; '),
    'columns' => array(
        'nombredepartamento',
        array(
            'name' => 'nombre_gerencia',
            'value' => '$data->gerencia_id==null?"No tiene definido gerencia":$data->fkgerencia->nombregerencia',
        ),
        array(
            'name' => 'nombre_direccion',
            'value' => '$data->gerencia_id==null?"No tiene definido direccion":$data->fkgerencia->fkdireccion->nombredireccion',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{view}{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
