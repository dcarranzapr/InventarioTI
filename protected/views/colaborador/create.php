<?php
/* @var $this ColaboradorController */
/* @var $model Colaborador */

$this->breadcrumbs = array(
    'Colaboradors' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de colaboradores', 'url' => array('index')),
);
?>

<h1>Crear colaborador</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>