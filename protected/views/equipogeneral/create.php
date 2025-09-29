<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */

$this->breadcrumbs = array(
    'Equipogenerals' => array('index'),
    'Captura de inventario',
);

$this->menu = array(
    array('label' => 'Control de inventario', 'url' => array('index')),
    array('label' => 'Transferencia entre hoteles', 'url' => array('cambio')),
    array('label' => 'Aceptar transferencia entre hoteles', 'url' => array('autorizaciones')),
    array('label' => 'Creación multiple', 'url' => array('createMultiple')),
);
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/Js/crearEquipoGeneral.js', CClientScript::POS_END);
?>

<h1>Captura de inventario</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>

