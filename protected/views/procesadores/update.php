<?php
/* @var $this ProcesadoresController */
/* @var $model Procesadores */

$this->breadcrumbs = array(
    'Procesadores' => array('index'),
    $model->idProcesadores => array('view', 'id' => $model->idProcesadores),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de Procesadores', 'url' => array('index')),
    array('label' => 'Crear Procesador', 'url' => array('create')),
);
?>

<h1>Actualizar procesador<?php echo $model->nombreProcesador; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>