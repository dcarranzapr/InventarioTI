<?php
/* @var $this ResguardoController */
/* @var $model Resguardo */

$this->breadcrumbs = array(
    'Resguardos' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de resguardo', 'url' => array('index')),
);
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/Js/ocultarResguardo.js', CClientScript::POS_END);
?>

<h1>Crear resguardo</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>