<?php
/* @var $this HotelController */
/* @var $model Hotel */
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
<?php echo $form->label($model, 'id'); ?>
<?php echo $form->textField($model, 'id'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'nombreHotel'); ?>
<?php echo $form->textField($model, 'nombreHotel', array('size' => 35, 'maxlength' => 35)); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'descripcion'); ?>
<?php echo $form->textArea($model, 'descripcion', array('rows' => 6, 'cols' => 50)); ?>
    </div>



    <div class="row buttons">
    <?php echo CHtml::submitButton('Search'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->