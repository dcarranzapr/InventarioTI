<?php
/* @var $this PlataformaController */
/* @var $model Plataforma */

$this->breadcrumbs = array(
    'Plataformas' => array('index'),
    $model->idPlataforma => array('view', 'id' => $model->idPlataforma),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de Plataformas', 'url' => array('index')),
    array('label' => 'Crear Plataforma', 'url' => array('create')),
);
?>

<h1>Actualizar plataforma <?php echo $model->nombrePlataforma; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>