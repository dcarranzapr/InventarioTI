<?php
/* @var $this GerenciaController */
/* @var $model Gerencia */

$this->breadcrumbs = array(
    'Gerencias' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de gerencias', 'url' => array('index')),
);
?>

<h1>Crear gerencias</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>