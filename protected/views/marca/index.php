<?php
/* @var $this MarcaController */
/* @var $model Marca */

$this->breadcrumbs = array(
    'Marcas' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear Marca', 'url' => array('create')),
);
?>

<h1>Control de marcas</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'marca-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; '),
    'columns' => array(
        'nombremarca',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
