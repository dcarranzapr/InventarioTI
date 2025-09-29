<?php

class ExcelReport {

    private $creador;
    private $titulo;
    private $objPHPExcel;
    private $ubicacion_inicial;

    public function ExcelReport($titulo = null, $inicial = 'A3') {
        $this->creador = 'Sistema DATA CENTER';
        $this->titulo = $titulo;

        $this->objPHPExcel = new PHPExcel();
        $this->objPHPExcel->getProperties()
                ->setCreator($this->creador)->setTitle($titulo);
        $this->objPHPExcel->setActiveSheetIndex(0);

        $this->ubicacion_inicial = $inicial;
    }

    public function setHojaPrincipal($titulo_hoja, $datos) {
        $this->objPHPExcel->getActiveSheet()->setTitle($titulo_hoja);
        $this->objPHPExcel->getActiveSheet()->fromArray($datos, NULL, $this->ubicacion_inicial);
    }

    public function setCabeceraBold($inferior, $superior, $autosize = false, $hoja = 0) {
        $this->objPHPExcel->getSheet($hoja)->getStyle($inferior . '3:' . $superior . '3')->getFont()->setBold(true);
        if ($autosize) {
            for ($col = $inferior; $col <= $superior; $col++)
                $this->objPHPExcel->getSheet($hoja)->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function generar() {
        ob_end_clean();
        ob_start();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $this->titulo . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        #unset($objWriter);
        exit();
    }

    public function nuevaHoja($datos, $titulo, $numero = 1) {
        $workSheet = new PHPExcel_Worksheet($this->objPHPExcel, $titulo);

        $this->objPHPExcel->addSheet($workSheet, $numero);
        $this->objPHPExcel->getSheet($numero)->fromArray($datos, NULL, $this->ubicacion_inicial);
    }

}
