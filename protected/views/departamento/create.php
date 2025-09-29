<?php
/* @var $this DepartamentoController */
/* @var $model Departamento */

$this->breadcrumbs = array(
    'Departamentos' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de departamentos', 'url' => array('index')),
);
?>

<h1>Crear departamento</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>