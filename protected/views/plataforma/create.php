<?php
/* @var $this PlataformaController */
/* @var $model Plataforma */

$this->breadcrumbs = array(
    'Plataformas' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de plataformas', 'url' => array('index')),
);
?>

<h1>Crear plataforma</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>