<?php

namespace App\Exports\correspondence;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
class RequestExportCorrespondence implements FromView, WithEvents {

    private $view;
    private $data;

    /**
     * Constructor de la clase
     *
     * @author Carlos Moises Garcia T. - Abri. 18 - 2021
     * @version 1.0.0
     * 
     * @param $view vista que se desea exportar
     * @param $data datos que se le envian a la vista
     */
    public function __construct($view ,$data, $count, $column='l') {
        $this->view = $view;
        $this->data = $data;
        $this->count = $count;
        $this->column = $column;

    }

    /**
     * Regstro de eventos
     *
     * @author Carlos Moises Garcia T. - Abri. 18 - 2021
     * @version 1.0.0
     *
     * @return array
     */
    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:'.$this->column.'1'; // All headers

                
 

                $event->sheet->getStyle('A1:'.strtoupper($this->column).($this->count+1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],

                ])->getAlignment()->setWrapText(true)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);


        
                foreach(range('A',strtoupper($this->column)) as $column){
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth(155, 'px');
                }
            }
        ];
    }

    // public function drawings() {
    //     $drawing = new Drawing();
    //     $drawing->setName('Logo');
    //     $drawing->setDescription('Logo');
    //     $drawing->setPath(public_path('/assets/img/logo_epa.png'));
    //     $drawing->setHeight(90);
    //     $drawing->setCoordinates('A1');

    //     return $drawing;
    // }

    /**
     * Obtiene la letra del abecedario dependiendo de la posicion
     *
     * @author Jhoan Sebastian Chilito S. - May. 29 - 2020
     * @version 1.0.0
     */
    function getByPosition($index): string {
        // Crea un array con las letras de la A a la Z
        $alphabet = range('A', 'Z');
        // Seteamos la posición restando 1 porque los índices comienzan en 0
        $pos = $index - 1;
        // Retornamos la letra, o NULL, si $index desborda el array
        // Para evitar que true sea tratado como índice 1, controlamos con is_bool también
        return ( !empty($alphabet[$pos]) && !is_bool($index) ) ? $alphabet[$pos] : NULL ;
    }


//     public function registerEvents() : array
// {
//     return [
//         AfterSheet::class    => function(AfterSheet $event) {
//             $cellRange = 'A1:L1'; // All headers
//             $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
//             $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
//                          ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
//             $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
//                          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
//                          ->getStartColor()->setARGB('FF17a2b8');
//             $event->sheet->setAutoFilter($cellRange);

//             $event->sheet->styleCells(
//                 'A2:'.$to['column'].$to['row'],
//                 [
//                     'borders' => [
//                         'allBorders' => [
//                             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                         ],
//                     ]
//                 ]
//             );
//         },
//     ];
// }

    /**
     * Exporta los datos desde una vista
     *
     * @author Carlos Moises Garcia T. - Abri. 18 - 2021
     * @version 1.0.0
     * 
     */
    public function view(): View {
        // Retorna la vista enviando los datos
        return view($this->view)->with('data', $this->data);
    }
}
