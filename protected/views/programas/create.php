<?php
/* @var $this ProgramasController */
/* @var $model Programas */

$this->breadcrumbs = array(
    'Programases' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de Programas', 'url' => array('index')),
);
?>

<h1>Crear programa</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>