<?php
/* @var $this PrestamosController */
/* @var $data Prestamos */
?>

<div class="view">

    <b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
    <?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('colaborador_id_usuario')); ?>:</b>
    <?php echo CHtml::encode($data->colaborador_id_usuario); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('equipoGeneral_id')); ?>:</b>
    <?php echo CHtml::encode($data->equipoGeneral_id); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('estatus_idEstatus')); ?>:</b>
    <?php echo CHtml::encode($data->estatus_idEstatus); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('fecha_devolucion')); ?>:</b>
    <?php echo CHtml::encode($data->fecha_devolucion); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('fecha_prestamo')); ?>:</b>
    <?php echo CHtml::encode($data->fecha_prestamo); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('proroga')); ?>:</b>
    <?php echo CHtml::encode($data->proroga); ?>
    <br />

    <?php /*
      <b><?php echo CHtml::encode($data->getAttributeLabel('descripcion')); ?>:</b>
      <?php echo CHtml::encode($data->descripcion); ?>
      <br />

     */ ?>

</div>