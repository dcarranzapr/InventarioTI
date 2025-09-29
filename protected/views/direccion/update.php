<?php
/* @var $this DireccionController */
/* @var $model Direccion */

$this->breadcrumbs = array(
    'Direccions' => array('index'),
    $model->id => array('view', 'id' => $model->id),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de direcciones', 'url' => array('index')),
    array('label' => 'Crear Direccion', 'url' => array('create')),
);
?>

<h1>Actualizar direccion <?php echo $model->nombredireccion; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>