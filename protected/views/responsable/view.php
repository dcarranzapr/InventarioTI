<?php
$this->breadcrumbs = array(
    'Ingenieros' => array('index'),
    $model->id_responsable,
);

$this->menu = array(
    array('label' => 'Ingenieros', 'url' => array('index')),
    array('label' => 'Crear ingeniero de soporte', 'url' => array('create')),
    array('label' => 'Actualizar Responsable', 'url' => array('update', 'id' => $model->id_responsable)),
    array('label' => 'Eliminar Responsable', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id_responsable), 'confirm' => 'Are you sure you want to delete this item?')),
);
?>

<h1>Vista del ingeniero de soporte #<?php echo $model->id_responsable; ?></h1>

<?php
$this->widget('bootstrap.widgets.TbDetailView', array(
    'data' => $model,
    'attributes' => array(
        'id_responsable',
        'usuario',
        'nombre',
        'password',
        'tipo',
        'acceso',
    ),
));
?>
