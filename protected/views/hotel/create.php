<?php
/* @var $this HotelController */
/* @var $model Hotel */

$this->breadcrumbs = array(
    'Hotels' => array('index'),
    'vista' => 'create',
    'Crear'
);

$this->menu = array(
    array('label' => 'Control de hoteles', 'url' => array('index')),
);
?>

<h1>Crear hotel</h1>

<?php $this->renderPartial('_form', array('model' => $model, 'model1' => $model1)); ?>