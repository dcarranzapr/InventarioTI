<?php
$this->breadcrumbs = array(
    'Ingenieros' => array('index'),
    'vista' => 'Create',
    'Crear',
);

$this->menu = array(
    array('label' => 'Ingenieros', 'url' => array('index')),
);
?>

<h2>Crear nuevo ingeniero de soporte</h2>

<?php echo $this->renderPartial('_form', array('model' => $model, 'model1' => $model1)); ?>