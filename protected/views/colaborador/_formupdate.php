<?php
/* @var $this ColaboradorController */
/* @var $model Colaborador */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'COLABORADOR-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
<?php echo $form->errorSummary($model); ?>


    <div class="row-fluid">

        <div class="span4">
            <?php echo $form->labelEx($model, 'usuarioNombre'); ?>
<?php echo $form->textField($model, 'usuarioNombre', array('size' => 60, 'maxlength' => 80)); ?>
<?php echo $form->error($model, 'usuarioNombre'); ?>
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