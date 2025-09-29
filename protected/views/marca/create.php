<?php
/* @var $this MarcaController */
/* @var $model Marca */

$this->breadcrumbs = array(
    'Marcas' => array('index'),
    'Crear',
);

$this->menu = array(
    array('label' => 'Control de Marcas', 'url' => array('index')),
);
?>

<h1>Crear marca</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>