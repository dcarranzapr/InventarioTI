<?php
/* @var $this HotelController */
/* @var $model Hotel */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'hotel-form',
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
            <?php echo $form->labelEx($model, 'nombreHotel'); ?>
            <?php echo $form->textField($model, 'nombreHotel', array('size' => 35, 'maxlength' => 35, 'style' => 'text-transform:uppercase',)); ?>
            <?php echo $form->error($model, 'nombreHotel'); ?>
        </div>


    </div>



    <div class="row-fluid">
        <div class="form-actions">
            <div class="checkboxgroup">
                <?php if ($this->breadcrumbs['vista'] == 'Crear') { ?>
                    <?php echo $form->labelEx($model1, 'departamento_iddepartamento'); ?>
                    <?php
                    echo $form->checkBoxList($model1, 'departamento_iddepartamento', Hotel::getListDepartamentos(), array(
                        'separator' => '',
                        'template' => '<div>{input}&nbsp;{label}</div>'
                    ));
                    ?>
                    <?php echo $form->error($model1, 'departamento_iddepartamento'); ?>
                <?php } else { ?>
                    <?php echo $form->labelEx($model1, 'departamento_iddepartamento'); ?>
                    <?php
                    $departamentos = CHtml::listData(Departamento::model()->findAll(), 'iddepartamento', 'nombredepartamento');
                    $selected_keys = array_keys(CHtml::listData($model->hotelhasdepartamento, 'departamento_iddepartamento', 'departamento_iddepartamento'));
                    echo CHtml::checkBoxList('Hotelhasdepartamento[departamento_iddepartamento][]', $selected_keys, $departamentos, array(
                        'separator' => '',
                        'template' => '<div>{input}&nbsp;{label}</div>'
                    ));
                    ?>
                    <?php echo $form->error($model1, 'departamento_iddepartamento'); ?>
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

</div><!-- form -->
<style>
    .checkboxgroup{
        overflow:auto;
    }
    .checkboxgroup div{
        width:200px;
        float:left;
    }
</style>