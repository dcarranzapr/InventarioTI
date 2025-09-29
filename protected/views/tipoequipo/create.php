<?php
/* @var $this TipoequipoController */
/* @var $model Tipoequipo */

$this->breadcrumbs = array(
    'Tipoequipos' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de tipos de equipo', 'url' => array('index')),
);
?>

<h1>Crear tipo de equipo</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>