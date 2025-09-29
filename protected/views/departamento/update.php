<?php
/* @var $this DepartamentoController */
/* @var $model Departamento */

$this->breadcrumbs = array(
    'Departamentos' => array('index'),
    $model->iddepartamento => array('view', 'id' => $model->iddepartamento),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de departamentos', 'url' => array('index')),
    array('label' => 'Crear departamento', 'url' => array('create')),
    array('label' => 'detalle de departamento', 'url' => array('view', 'id' => $model->iddepartamento)),
);
?>

<h1>Actualizar departamento <?php echo $model->nombredepartamento; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>