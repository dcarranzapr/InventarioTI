<?php
/* @var $this PrestamosController */
/* @var $model Prestamos */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <div class="row">
<?php echo $form->label($model, 'id'); ?>
<?php echo $form->textField($model, 'id'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'colaborador_id_usuario'); ?>
<?php echo $form->textField($model, 'colaborador_id_usuario'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'equipoGeneral_id'); ?>
<?php echo $form->textField($model, 'equipoGeneral_id'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'estatus_idEstatus'); ?>
<?php echo $form->textField($model, 'estatus_idEstatus'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'fecha_devolucion'); ?>
<?php echo $form->textField($model, 'fecha_devolucion'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'fecha_prestamo'); ?>
<?php echo $form->textField($model, 'fecha_prestamo'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'proroga'); ?>
<?php echo $form->textField($model, 'proroga'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'descripcion'); ?>
<?php echo $form->textArea($model, 'descripcion', array('rows' => 6, 'cols' => 50)); ?>
    </div>

    <div class="row buttons">
    <?php echo CHtml::submitButton('Search'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->