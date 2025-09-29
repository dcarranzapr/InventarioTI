<?php
/* @var $this SistemaoperativoController */
/* @var $model Sistemaoperativo */

$this->breadcrumbs = array(
    'Sistemaoperativos' => array('index'),
    $model->idSitemaOperativo => array('view', 'id' => $model->idSitemaOperativo),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de sistemas operativos', 'url' => array('index')),
    array('label' => 'Crear sistema operativo', 'url' => array('create')),
);
?>

<h1>Actualizar sistema operativo <?php echo $model->nombreSistema; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>