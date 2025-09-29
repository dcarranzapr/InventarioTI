<?php
/* @var $this ProcesadoresController */
/* @var $model Procesadores */

$this->breadcrumbs = array(
    'Procesadores' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de Procesadores', 'url' => array('index')),
);
?>

<h1>Crear procesador</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>