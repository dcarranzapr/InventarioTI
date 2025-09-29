<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */

$this->breadcrumbs = array(
    'Equipogenerals' => array('index'),
    $model->id,
);

$this->menu = array(
    array('label' => 'Control de inventario', 'url' => array('index')),
    array('label' => 'Transferencia entre hoteles', 'url' => array('cambio')),
    array('label' => 'Aceptar transferencia entre hoteles', 'url' => array('autorizaciones')),
    array('label' => 'Creación multiple', 'url' => array('createMultiple')),
);
?>

<h1>Equipo generado con éxito #<?php echo $model->id; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'numeroSerie',
        'fechaCompra',
        'factura',
        'fechaIngreso',
        'memoria',
        'nombrePC',
    ),
));
?>
