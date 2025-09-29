<?php
/* @var $this ProgramasController */
/* @var $model Programas */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'programas-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
        <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'nombre'); ?>
<?php echo $form->textField($model, 'nombre', array('size' => 45, 'maxlength' => 45)); ?>
<?php echo $form->error($model, 'nombre'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'observaciones'); ?>
<?php echo $form->textArea($model, 'observaciones', array('rows' => 6, 'cols' => 50)); ?>
<?php echo $form->error($model, 'observaciones'); ?>
    </div>

    <div class="form-actions">
        <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type' => 'primary',
            'label' => $model->isNewRecord ? 'Crear' : 'Guardar',
        ));
        ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->