<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">

	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<?php Yii::app()->bootstrap->register(); ?>
</head>

<body>
    <div class="container">
    		<div id='titulo'><h3>SISTEMA DE INVENTARIO TI<small style='margin-left:450px;'>MÓDULO DE ACCESO</small></h3> </div>
    		<hr> </hr>
    			<div id='titulo'><h4>TECNOLOGÍA DE LA INFORMACIÓN</h4></div>
        <div class="span5 offset3">
        	<?php echo $content; ?>
        </div>
    </div>
</body>
</html>
<style type="text/css">
body{

	
}
</style>