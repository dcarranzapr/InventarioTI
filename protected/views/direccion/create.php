<?php
/* @var $this DireccionController */
/* @var $model Direccion */

$this->breadcrumbs = array(
    'Direccions' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de direcciones', 'url' => array('index')),
);
?>

<h1>Crear dirección</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>