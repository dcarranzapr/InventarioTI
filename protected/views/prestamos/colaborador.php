

<?php
$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'get',
        ));
?>



<div class="row-fluid">
    <div class="span4">

        <?php echo $form->label($model, 'numeroColaborador'); ?>
        <?php
        echo $form->textField($model, 'numeroColaborador', array(
            'ajax' => array(
                'type' => 'POST',
                'dataType' => 'Json',
                'url' => CController::createUrl('busquedaColaborador'),
                'success' => 'function(data) {
                    if(data.error) {
                        alert(data.msg);
                        $("#Prestamos_usuarioNombre").val("");
                    } else {
                        $("#Prestamos_usuarioNombre").val(data.colaborador.usuarioNombre);
                    }
                }',
                'error' => 'function(data) {
                    alert("No se ha encontrado ningún usuario con ese número de colaborador");
                    document.all.Prestamos_numeroColaborador.focus();
                    $("#Prestamos_usuarioNombre").val("");
                }'),
            'size' => 45, 'maxlength' => 60)
        );
        ;
        ?>
    </div>
    <div class="span4">
<?php echo $form->label($model, 'usuarioNombre'); ?>
<?php echo $form->textField($model, 'usuarioNombre', array('size' => 60, 'maxlength' => 80, 'readonly' => true)); ?>
    </div>
</div>
<div class="row-fluid">


<?php $this->endWidget(); ?>