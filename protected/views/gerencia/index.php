<?php
/* @var $this GerenciaController */
/* @var $model Gerencia */

$this->breadcrumbs = array(
    'Control',
);

$this->menu = array(
    array('label' => 'Crear gerencia', 'url' => array('create')),
);
?>
<h1>Control de gerencias</h1>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'gerencia-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; width:500px '),
    'columns' => array(
        array(
            'name' => 'nombregerencia',
            'value' => '$data->nombregerencia',
            'htmlOptions' => array('style' => 'max-width:40px'),
        ),
        array(
            'name' => 'nombre_direccion',
            'value' => '$data->fkdireccion->nombredireccion',
            'htmlOptions' => array('style' => 'max-width:40px'),
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

