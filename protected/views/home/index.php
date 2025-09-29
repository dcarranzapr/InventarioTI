<?php
/* @var $this HomeController */

$this->pageTitle = Yii::app()->name;
?>

<div class="page-header">
    <h1>INVENTARIO TI<small>&nbsp;&nbsp;&nbsp;&nbsp;Préstamos de equipos</small>
    </h1>
</div>




<div class="row-fluid">
    <ul class="thumbnails">

        <li class="span10">
            <div class="thumbnail">
                <center>
                    <h4>Préstamos proximos a vencer</h4>
                    <hr>
                </center>
                <?php
                $this->renderPartial('_menuPrestamo', array(
                    'model' => $model,
                ));
                ?>
            </div>
        </li>
    </ul>
</div>



