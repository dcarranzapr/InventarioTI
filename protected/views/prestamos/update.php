<?php
/* @var $this PrestamosController */
/* @var $model Prestamos */

$this->breadcrumbs = array(
    'Prestamo' => array('index'),
    $model->id => array('view', 'id' => $model->id),
    'Actualizar',
);


$this->menu = array(
    array('label' => 'Crear prestamo', 'url' => array('create')),
    array('label' => 'Ver equipos del prestamo', 'url' => array('view', 'id' => $model->id)),
    array('label' => 'Control de prestamo', 'url' => array('index')),
);
?>

<h1>Actualizar préstamo del colaborador <?php echo $model->colaboradorIdUsuario->usuarioNombre; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>