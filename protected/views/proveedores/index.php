<?php
/* @var $this ProveedoresController */
/* @var $model Proveedores */

$this->breadcrumbs = array(
    'Proveedores' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear Proveedor', 'url' => array('create')),
);
?>

<h1>Control de proveedores</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'proveedores-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word;  '),
    'columns' => array(
        array(
            'name' => 'nombreProveedor',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
