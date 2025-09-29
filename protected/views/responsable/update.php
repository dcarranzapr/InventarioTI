<?php
$this->breadcrumbs = array(
    'Ingenieros' => array('index'),
    'vista' => array('update', 'id' => $model->id_responsable),
    'Editar',
);

$this->menu = array(
    array('label' => 'Ingenieros', 'url' => array('index')),
    array('label' => 'Crear ingeniero de soporte', 'url' => array('create')),
);
?>

<h2>Actualizar ingeniero de soporte</h2>

<?php echo $this->renderPartial('_form', array('model' => $model, 'model1' => $model1)); ?>