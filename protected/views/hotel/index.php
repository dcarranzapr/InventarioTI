<?php
/* @var $this HotelController */
/* @var $model Hotel */

$this->breadcrumbs = array(
    'Hotels' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear hotel', 'url' => array('create')),
);
?>

<h1>Control de hoteles</h1>





<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'hotel-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'columns' => array(
        'nombreHotel',
        'descripcion',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update} {delete}',
            'htmlOptions' => array('style' => 'width: 50px,height:30px'),
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>

