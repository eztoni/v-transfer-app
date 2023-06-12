<?php

namespace App\Exports;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DestinationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{

    use Exportable;

    private array $filterData=[];

    private Collection $data;

    public function __construct(private array $array, private string $reportType)
    {

        $this->data =  collect($array);
        $this->format();

    }

    private function format_excel_price($price){

        $return = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $price);

        if($price > 999){
            $return = str_replace('.','',$return);
        }

        $return = str_replace(',','.',$return);

        return number_format($return,2,'.','');
    }

    private function format()
    {

        $this->data =  $this->data->map(function ($item) {

            $data_array = array();

            if($this->reportType == 'partner-report'){
                $data_array['datum_vouchera'] = $item['voucher_date'];
                $data_array['prodajno_mjesto'] = 'VEC Valamar';
                $data_array['voucher_id'] = $item['id'];
                $data_array['nositelj_vouchera'] = $item['name'];
                $data_array['postupak'] = $item['status'] == 'confirmed' ? 'RP' : 'CF';
                $data_array['odrasli'] = $item['adults'];
                $data_array['djeca'] = (int)$item['children']+(int)$item['infants'];
                $data_array['bruto_prihod'] = $this->format_excel_price($item['price_eur']);
                $data_array['trošak_ulaznog_računa'] = $this->format_excel_price($item['invoice_charge']);
                $data_array['bruto_profit'] = $this->format_excel_price($item['commission_amount']);
                $data_array['ugovorena_provizija'] = $item['commission'].'%';
                $data_array['vrsta_proizvoda'] = 'Transfer';
            }

            return $data_array;
        });
    }

    public function headings(): array
    {
        $headings = [];

        //get the first because all of them have all of the keys
        foreach (array_keys($this->data->first()) as $key) {
            $headings[] = Str::of($key)->replace('_', ' ')->ucfirst()->value();
        }

        return [
            ['Datum Od', 'Datum Do', 'Bruto Prihod','Bruto Profit'],
            $this->filterData,
            [],
            $headings
        ];


    }

    public function collection()
    {
        return $this->data;
    }

    public function setFilterData(array $filterData ){
        $this->filterData = $filterData;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => Color::COLOR_BLUE],
                ],
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['argb' => Color::COLOR_WHITE]
                ]
            ],
            4 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => Color::COLOR_BLUE],
                ],
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['argb' => Color::COLOR_WHITE]
                ],
            ]
        ];
    }
}
