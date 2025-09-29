<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'responsable-form',
    'enableAjaxValidation' => false,
        ));
?>

<p class="help-block">Los campos son <span class="required">*</span> son requeridos.</p>

<?php echo $form->errorSummary($model); ?>

<div class="row-fluid">
    <div class="span4">
        <?php echo $form->textFieldRow($model, 'usuario', array('maxlength' => 50)); ?>
    </div>
    <div class="span4">
        <?php echo $form->textFieldRow($model, 'nombre', array('maxlength' => 50)); ?>
    </div>
    <div class="span4">
<?php echo $form->passwordFieldRow($model, 'password', array('maxlength' => 100)); ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span4">
<?php echo $form->dropDownListRow($model, 'tipo', Responsable::getListTipo(), array('empty' => 'Seleccione...')); ?>
    </div>

    <div class="span4">
        <?php echo $form->labelEx($model, 'acceso'); ?>
<?php echo $form->checkBox($model, 'acceso'); ?>
<?php echo $form->error($model, 'acceso'); ?>
    </div>
</div>

<div class="row-fluid">
    <div class="form-actions">
        <div class="checkboxgroup">
            <?php if ($this->breadcrumbs['vista'] == 'Create') { ?>
                <?php echo $form->labelEx($model1, 'hotel_id'); ?>
                <?php
                echo $form->checkBoxList($model1, 'hotel_id', Hotel::getListHoteles(), array(
                    'separator' => '',
                    'template' => '<div>{input}&nbsp;{label}</div>'
                ));
                ?>
                <?php echo $form->error($model1, 'hotel_id'); ?>
            <?php } else { ?>
                <?php echo $form->labelEx($model1, 'hotel_id'); ?>
                <?php
                $hoteles = CHtml::listData(Hotel::model()->findAll(), 'id', 'nombreHotel');
                $selected_keys = array_keys(CHtml::listData($model->userPrivilegioHotels, 'hotel_id', 'hotel_id'));
                echo CHtml::checkBoxList('UserPrivilegioHotel[hotel_id][]', $selected_keys, $hoteles, array(
                    'separator' => '',
                    'template' => '<div>{input}&nbsp;{label}</div>'
                ));
                ?>
    <?php echo $form->error($model1, 'hotel_id'); ?>
<?php } ?>

        </div>
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
<style>
    .checkboxgroup{
        overflow:auto;
    }
    .checkboxgroup div{
        width:200px;
        float:left;
    }
</style>