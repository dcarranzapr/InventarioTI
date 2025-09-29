<?php
/* @var $this ResguardoController */
/* @var $data Resguardo */
?>

<div class="view">

    <b><?php echo CHtml::encode($data->getAttributeLabel('id_resguardo')); ?>:</b>
    <?php echo CHtml::link(CHtml::encode($data->id_resguardo), array('view', 'id' => $data->id_resguardo)); ?>
    <br />



</div>