<?php
/* @var $this PlataformaController */
/* @var $model Plataforma */

$this->breadcrumbs = array(
    'Plataformas' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear plataforma', 'url' => array('create')),
);
?>

<h1>Control de plataformas</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'plataforma-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; '),
    'columns' => array(
        'nombrePlataforma',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
