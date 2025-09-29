<?php
/* @var $this HotelhasdepartamentoController */
/* @var $model Hotelhasdepartamento */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'hotelhasdepartamento-_form-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // See class documentation of CActiveForm for details on this,
        // you need to use the performAjaxValidation()-method described there.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'hotel_id'); ?>
<?php echo $form->textField($model, 'hotel_id'); ?>
<?php echo $form->error($model, 'hotel_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'departamento_iddepartamento'); ?>
<?php echo $form->textField($model, 'departamento_iddepartamento'); ?>
<?php echo $form->error($model, 'departamento_iddepartamento'); ?>
    </div>


    <div class="row buttons">
    <?php echo CHtml::submitButton('Submit'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->