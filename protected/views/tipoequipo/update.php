<?php
/* @var $this TipoequipoController */
/* @var $model Tipoequipo */

$this->breadcrumbs = array(
    'Tipoequipos' => array('index'),
    $model->idTipoEquipo => array('view', 'id' => $model->idTipoEquipo),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de tipos de equipo', 'url' => array('index')),
    array('label' => 'Crear tipo de equipo', 'url' => array('create')),
);
?>

<h1>Actualizar el tipo <?php echo $model->nombreTipoEquipo; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>