<?php
/* @var $this SistemaoperativoController */
/* @var $model Sistemaoperativo */

$this->breadcrumbs = array(
    'Sistemaoperativos' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear Sistema operativo', 'url' => array('create')),
);
?>

<h1>Control de  sistemas operativos</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'sistemaoperativo-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; '),
    'columns' => array(
        'nombreSistema',
        'descripcion',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
