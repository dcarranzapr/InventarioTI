<?php
/* @var $this ColaboradorController */
/* @var $model Colaborador */

$this->breadcrumbs = array(
    'Colaboradors' => array('index'),
    $model->id_usuario => array('view', 'id' => $model->id_usuario),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de colaboradores', 'url' => array('index')),
    array('label' => 'Crear colaborador', 'url' => array('create')),
);
?>

<h1>Actualizar colaborador <?php echo $model->usuarioNombre; ?></h1>

<?php $this->renderPartial('_formupdate', array('model' => $model)); ?>