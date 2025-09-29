<?php
/* @var $this ResguardoController */
/* @var $model Resguardo */

$this->breadcrumbs = array(
    'Resguardos' => array('index'),
    $model->id_resguardo => array('view', 'id' => $model->id_resguardo),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Crear resguardo', 'url' => array('create')),
    array('label' => 'Control de resguardos', 'url' => array('index')),
    array('label' => 'Ver datos del resguardo', 'url' => array('view', 'id' => $model->id_resguardo)),
);
?>

<h1>Actualizar Resguardo <?php echo $model->idColaboradorEmpleado0->usuarioNombre; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>