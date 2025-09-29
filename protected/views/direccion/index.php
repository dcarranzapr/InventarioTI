<?php
/* @var $this DireccionController */
/* @var $model Direccion */

$this->breadcrumbs = array(
    'Control',
);

$this->menu = array(
    array('label' => 'Crear Direccion', 'url' => array('create')),
);
?>

<h1>Ccontrol de direcciones</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'direccion-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word; width:500px;'),
    'columns' => array(
        array(
            'name' => 'nombredireccion',
            'htmlOptions' => array('style' => 'width:40px'),
        ),
        array(
            'name' => 'descripciondireccion',
            'htmlOptions' => array('style' => 'width:40px'),
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



