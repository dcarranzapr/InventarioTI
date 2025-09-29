<?php
/* @var $this HotelController */
/* @var $model Hotel */

$this->breadcrumbs = array(
    'Hotels' => array('index'),
    $model->id,
);

$this->menu = array(
    array('label' => 'Control de hoteles', 'url' => array('index')),
    array('label' => 'Creat hotel', 'url' => array('create')),
    array('label' => 'Actualizar hotel', 'url' => array('update', 'id' => $model->id)),
    array('label' => 'Eliminar hotel', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Está seguro que desea eliminar este hotel?')),
);
?>

<h1>View Hotel #<?php echo $model->id; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'id',
        'nombreHotel',
    ),
));
?>
