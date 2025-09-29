<?php
/* @var $this GerenciaController */
/* @var $model Gerencia */

$this->breadcrumbs = array(
    'Gerencias' => array('index'),
    $model->id => array('view', 'id' => $model->id),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Ccontrol de gerencias', 'url' => array('index')),
    array('label' => 'Crear gerencia', 'icon' => 'pencil', 'url' => array('create')),
);
?>

<h1>Actualizar gerencia <?php echo $model->nombregerencia; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>