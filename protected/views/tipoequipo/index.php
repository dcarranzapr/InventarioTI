<?php
/* @var $this TipoequipoController */
/* @var $model Tipoequipo */

$this->breadcrumbs = array(
    'Tipoequipos' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear tipo de equipo', 'url' => array('create')),
);
?>

<h1>Control de tipos de equipos</h1>


<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'tipoequipo-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; '),
    'columns' => array(
        'nombreTipoEquipo',
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
