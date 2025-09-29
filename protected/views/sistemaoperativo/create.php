<?php
/* @var $this SistemaoperativoController */
/* @var $model Sistemaoperativo */

$this->breadcrumbs = array(
    'Sistemaoperativos' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de sistemas operativos', 'url' => array('index')),
);
?>

<h1>Crear sistema operativo</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>