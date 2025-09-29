<?php
/* @var $this ProcesadoresController */
/* @var $model Procesadores */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'procesadores-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
<?php echo $form->errorSummary($model); ?>



    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'nombreProcesador'); ?>
<?php echo $form->textField($model, 'nombreProcesador', array('size' => 45, 'maxlength' => 45)); ?>
            <?php echo $form->error($model, 'nombreProcesador'); ?>
        </div>
        <div class="span4">
            <?php echo $form->labelEx($model, 'especificaciones'); ?>
<?php echo $form->textField($model, 'especificaciones', array('size' => 45, 'maxlength' => 100)); ?>
<?php echo $form->error($model, 'especificaciones'); ?>
        </div>
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