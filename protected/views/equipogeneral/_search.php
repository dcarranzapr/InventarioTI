<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */
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
<?php echo $form->label($model, 'nombre_modelo'); ?>
<?php echo $form->textField($model, 'nombre_modelo'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'nombre_proveedor'); ?>
<?php echo $form->textField($model, 'nombre_proveedor'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'idEstatus'); ?>
<?php echo $form->textField($model, 'idEstatus'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'idTipoEquipo'); ?>
<?php echo $form->textField($model, 'idTipoEquipo'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'idHotel'); ?>
<?php echo $form->textField($model, 'idHotel'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'idMarca'); ?>
<?php echo $form->textField($model, 'idMarca'); ?>
    </div>







    <div class="row">
<?php echo $form->label($model, 'idSitemaOperativo'); ?>
<?php echo $form->textField($model, 'idSitemaOperativo'); ?>
    </div>



    <div class="row">
<?php echo $form->label($model, 'numeroSerie'); ?>
<?php echo $form->textField($model, 'numeroSerie', array('size' => 50, 'maxlength' => 50)); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'fechaCompra'); ?>
<?php echo $form->textField($model, 'fechaCompra'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'factura'); ?>
<?php echo $form->textField($model, 'factura', array('size' => 50, 'maxlength' => 50)); ?>
    </div>



    <div class="row">
<?php echo $form->label($model, 'fechaIngreso'); ?>
<?php echo $form->textField($model, 'fechaIngreso'); ?>
    </div>



    <div class="row">
<?php echo $form->label($model, 'memoria'); ?>
<?php echo $form->textField($model, 'memoria', array('size' => 15, 'maxlength' => 15)); ?>
    </div>






    <div class="row buttons">
    <?php echo CHtml::submitButton('Search'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->