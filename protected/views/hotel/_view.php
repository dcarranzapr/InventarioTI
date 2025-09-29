<?php
/* @var $this HotelController */
/* @var $data Hotel */
?>

<div class="view">

    <b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
    <?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('nombreHotel')); ?>:</b>
    <?php echo CHtml::encode($data->nombreHotel); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('descripcion')); ?>:</b>
    <?php echo CHtml::encode($data->descripcion); ?>
    <br />




</div>