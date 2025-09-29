<?php
/* @var $this ModeloController */
/* @var $model Modelo */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'modelo-form',
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
            <?php echo $form->labelEx($model, 'nombremodelo'); ?>

            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'model' => $model,
                'attribute' => 'nombremodelo',
                'id' => 'modelo',
                'name' => 'Modelo[nombremodelo]',
                'source' => $this->createUrl('listarnombremodelos'),
                'options' => array(
                    'showAnim' => 'fold',
                    'select' => "js:function(event, ui) {
    $('#" . CHtml::activeId($model, 'nombremodelo') . "').val(ui.item.id);
    $('#nombremodelo').val(ui.item.label);
    return false;
  }",
                ),
                'htmlOptions' => array(
                    'style' => 'text-transform:uppercase',
                ),
            ));
            ?>
<?php echo $form->error($model, 'nombremodelo'); ?>
        </div>

        <div class="span4">
            <?php echo $form->labelEx($model, 'descripcion'); ?>
            <?php echo $form->textArea($model, 'descripcion', array('rows' => 1, 'cols' => 50, 'style' => 'text-transform:uppercase')); ?>
<?php echo $form->error($model, 'descripcion'); ?>
        </div>
        <div class="span4">
            <?php echo $form->labelEx($model, 'fkidMarca'); ?>
            <?php echo $form->dropdownList($model, 'fkidMarca', Modelo::getListMarca(), array('empty' => 'SELECCIONE MARCA')); ?>

<?php echo $form->error($model, 'fkidMarca'); ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'fkidTipoEquipo'); ?>
            <?php echo $form->dropdownList($model, 'fkidTipoEquipo', Modelo::getListTipo(), array('empty' => 'SELECCIONE TIPO')); ?>

<?php echo $form->error($model, 'fkidTipoEquipo'); ?>
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