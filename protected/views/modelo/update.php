<?php
/* @var $this ModeloController */
/* @var $model Modelo */

$this->breadcrumbs = array(
    'Modelos' => array('index'),
    $model->idModelo => array('view', 'id' => $model->idModelo),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de Modelos', 'url' => array('index')),
    array('label' => 'Crear Modelo', 'url' => array('create')),
);
?>

<h1>Actualizar modelo <?php echo $model->nombremodelo; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>