<?php
/* @var $this PrestamosController */
/* @var $model Prestamos */

$this->breadcrumbs = array(
    'Prestamo' => array('index'),
    'Crear',
);


$this->menu = array(
    array('label' => 'Control de prestamo', 'url' => array('index')),
);
?>

<h1>Crear nuevo préstamo</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>