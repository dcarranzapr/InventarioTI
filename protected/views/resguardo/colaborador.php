

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
                            $("#Resguardo_usuarioNombre").val("");
                            $("#Resguardo_direccion").val("");
                            $("#Resguardo_gerencia").val("");
                        } else {
                            $("#Resguardo_usuarioNombre").val(data.colaborador.usuarioNombre);
                            $("#Resguardo_direccion").val(data.colaborador.direccion);
                            $("#Resguardo_gerencia").val(data.colaborador.gerencia);
                        }
                    }',
                    'error' => 'function(data) {
                        alert("No se ha encontrado ningún usuario con ese número de colaborador");
                        document.all.Resguardo_numeroColaborador.focus();
                        $("#Resguardo_usuarioNombre").val("");
                        $("#Resguardo_direccion").val("");
                        $("#Resguardo_gerencia").val("");
                    }'),
                'size' => 45, 'maxlength' => 60)
            );
        ?>
    </div>
    <div class="span4">
        <?php echo $form->label($model, 'usuarioNombre'); ?>
        <?php echo $form->textField($model, 'usuarioNombre', array('size' => 60, 'maxlength' => 80, 'readonly' => true)); ?>
    </div>
    <div class="span4">
        <?php echo $form->label($model, 'direccion'); ?>
        <?php echo $form->textField($model, 'direccion', array('size' => 200, 'maxlength' => 200)); ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span4">
        <?php echo $form->label($model, 'gerencia'); ?>
        <?php echo $form->textField($model, 'gerencia', array('size' => 200, 'maxlength' => 200)); ?>
    </div>
    
</div>
<div class="row-fluid">
<?php $this->endWidget(); ?>