<?php
$this->breadcrumbs = array(
    'Cambiar password',
);
?>

<h1>Cambiar password</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'cambiarPassword-form',
    'enableAjaxValidation' => true,
        ));
?>

<p class="help-block">Los campos son <span class="required">*</span> son requeridos.</p>

<?php echo $form->errorSummary($model); ?>

<div class="row-fluid">
    <div class="span4">
<?php echo $form->passwordFieldRow($model, 'password_actual', array('maxlength' => 50)); ?>
    </div>
    <div class="span4">
<?php echo $form->passwordFieldRow($model, 'nuevo_password', array('maxlength' => 50)); ?>
    </div>
    <div class="span4">
<?php echo $form->passwordFieldRow($model, 'confirmar_nuevo_password', array('maxlength' => 100)); ?>
    </div>
</div>

<div class="form-actions">
    <?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => 'Crear',
    ));
    ?>
</div>

<?php $this->endWidget(); ?>
