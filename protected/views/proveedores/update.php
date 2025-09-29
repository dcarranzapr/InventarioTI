<?php
/* @var $this ProveedoresController */
/* @var $model Proveedores */

$this->breadcrumbs = array(
    'Proveedores' => array('index'),
    $model->idProveedores => array('view', 'id' => $model->idProveedores),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de Proveedores', 'url' => array('index')),
    array('label' => 'Crear Proveedor', 'url' => array('create')),
);
?>

<h1>Actualizar proveedor <?php echo $model->nombreProveedor; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>