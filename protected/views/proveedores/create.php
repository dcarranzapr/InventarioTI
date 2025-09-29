<?php
/* @var $this ProveedoresController */
/* @var $model Proveedores */

$this->breadcrumbs = array(
    'Proveedores' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de Proveedores', 'url' => array('index')),
);
?>

<h1>Crear proveedor</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>