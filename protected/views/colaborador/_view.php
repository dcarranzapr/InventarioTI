<?php
/* @var $this ColaboradorController */
/* @var $data Colaborador */
?>

<div class="view">

    <b><?php echo CHtml::encode($data->getAttributeLabel('id_usuario')); ?>:</b>
    <?php echo CHtml::link(CHtml::encode($data->id_usuario), array('view', 'id' => $data->id_usuario)); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('hotel_id')); ?>:</b>
    <?php echo CHtml::encode($data->hotel_id); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('numeroColaborador')); ?>:</b>
    <?php echo CHtml::encode($data->numeroColaborador); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('usuarioNombre')); ?>:</b>
    <?php echo CHtml::encode($data->usuarioNombre); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('departamento_iddepartamento')); ?>:</b>
    <?php echo CHtml::encode($data->departamento_iddepartamento); ?>
    <br />


</div>