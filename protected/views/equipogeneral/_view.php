<?php
/* @var $this EquipogeneralController */
/* @var $data Equipogeneral */
?>

<div class="view">

    <b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
    <?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('idModelo')); ?>:</b>
    <?php echo CHtml::encode($data->idModelo); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('idProveedores')); ?>:</b>
    <?php echo CHtml::encode($data->idProveedores); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('idEstatus')); ?>:</b>
    <?php echo CHtml::encode($data->idEstatus); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('idTipoEquipo')); ?>:</b>
    <?php echo CHtml::encode($data->idTipoEquipo); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('idHotel')); ?>:</b>
    <?php echo CHtml::encode($data->idHotel); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('idMarca')); ?>:</b>
    <?php echo CHtml::encode($data->idMarca); ?>
    <br />

    <?php /*
      <b><?php echo CHtml::encode($data->getAttributeLabel('idProcesador')); ?>:</b>
      <?php echo CHtml::encode($data->idProcesador); ?>
      <br />

      <b><?php echo CHtml::encode($data->getAttributeLabel('idTamano')); ?>:</b>
      <?php echo CHtml::encode($data->idTamano); ?>
      <br />



      <b><?php echo CHtml::encode($data->getAttributeLabel('idSitemaOperativo')); ?>:</b>
      <?php echo CHtml::encode($data->idSitemaOperativo); ?>
      <br />


      <b><?php echo CHtml::encode($data->getAttributeLabel('numeroSerie')); ?>:</b>
      <?php echo CHtml::encode($data->numeroSerie); ?>
      <br />

      <b><?php echo CHtml::encode($data->getAttributeLabel('fechaCompra')); ?>:</b>
      <?php echo CHtml::encode($data->fechaCompra); ?>
      <br />

      <b><?php echo CHtml::encode($data->getAttributeLabel('factura')); ?>:</b>
      <?php echo CHtml::encode($data->factura); ?>
      <br />



      <b><?php echo CHtml::encode($data->getAttributeLabel('fechaIngreso')); ?>:</b>
      <?php echo CHtml::encode($data->fechaIngreso); ?>
      <br />



      <b><?php echo CHtml::encode($data->getAttributeLabel('memoria')); ?>:</b>
      <?php echo CHtml::encode($data->memoria); ?>
      <br />

      <b><?php echo CHtml::encode($data->getAttributeLabel('nombrePC')); ?>:</b>
      <?php echo CHtml::encode($data->nombrePC); ?>
      <br />

      <b><?php echo CHtml::encode($data->getAttributeLabel('capturaColaboradorId')); ?>:</b>
      <?php echo CHtml::encode($data->capturaColaboradorId); ?>
      <br />

      <b><?php echo CHtml::encode($data->getAttributeLabel('Plataforma_idPlataforma')); ?>:</b>
      <?php echo CHtml::encode($data->Plataforma_idPlataforma); ?>
      <br />

      <b><?php echo CHtml::encode($data->getAttributeLabel('idHotelCambio')); ?>:</b>
      <?php echo CHtml::encode($data->idHotelCambio); ?>
      <br />

     */ ?>

</div>