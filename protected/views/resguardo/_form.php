<?php
/* @var $this ResguardoController */
/* @var $model Resguardo */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'resguardo-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
    <?php echo $form->errorSummary($model); ?>

    <?php
        $this->renderPartial('colaborador', array(
            'model' => $model,
        ));
    ?>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'nombreEquipo'); ?>
            <?php echo $form->textField($model, 'nombreEquipo', array('size' => 35, 'maxlength' => 35, 'style' => 'text-transform:uppercase')); ?>
            <?php echo $form->error($model, 'nombreEquipo'); ?>
        </div>
        <div class="span4">
            <?php echo $form->labelEx($model, 'nombre_plataforma'); ?>
            <?php echo $form->dropdownList($model, 'Plataforma_idPlataforma', Equipogeneral::getListPlataforma(), array('empty' => 'SELECCIONE PLATAFORMA')); ?>
            <?php echo $form->error($model, 'nombre_plataforma'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span">
            <?php echo $form->labelEx($model, 'comentarios'); ?>
            <?php echo $form->textArea($model, 'comentarios', array('rows' => 3, 'cols' => 50, 'style' => 'text-transform:uppercase; width:92%;')); ?>
            <?php echo $form->error($model, 'comentarios'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="form-actions">
            <?php
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'label' => $model->isNewRecord ? 'Crear' : 'Guardar',
            ));
            ?>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
