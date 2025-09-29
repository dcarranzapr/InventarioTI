<?php
/* @var $this MarcaController */
/* @var $model Marca */

$this->breadcrumbs = array(
    'Marcas' => array('index'),
    $model->idMarca => array('view', 'id' => $model->idMarca),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de Marcas', 'url' => array('index')),
    array('label' => 'Crear Marca', 'url' => array('create')),
);
?>

<h1>Modificar marca <?php echo $model->nombremarca; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>