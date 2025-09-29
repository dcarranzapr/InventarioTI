<?php
/* @var $this ModeloController */
/* @var $model Modelo */

$this->breadcrumbs = array(
    'Modelos' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de Modelos', 'url' => array('index')),
);
?>

<h1>Crear modelo</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>