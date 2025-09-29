<?php
/* @var $this ProgramasController */
/* @var $model Programas */

$this->breadcrumbs = array(
    'Programases' => array('index'),
    $model->id => array('view', 'id' => $model->id),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Crear Programa', 'url' => array('create')),
    array('label' => 'Control de Programas', 'url' => array('index')),
);
?>

<h1>Actualizar programa <?php echo $model->nombre; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>