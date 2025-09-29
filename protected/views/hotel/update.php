<?php
/* @var $this HotelController */
/* @var $model Hotel */

$this->breadcrumbs = array(
    'Hotel' => array('index'),
    'vista' => array('update', 'id' => $model->id),
    'Actualizar',
);

$this->menu = array(
    array('label' => 'Control de hoteles', 'url' => array('index')),
    array('label' => 'Crear hotelL', 'url' => array('create')),
);
?>

<h1>Actualizar hotel <?php echo $model->nombreHotel; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model, 'model1' => $model1)); ?>