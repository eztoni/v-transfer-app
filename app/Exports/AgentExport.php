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

class AgentExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{

    use Exportable;

    private array $filterData=[];

    private Collection $data;

    public function __construct(private array $array)
    {

        $this->data =  collect($array);
        $this->format();

    }

    public function format_excel_price($price){

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

            $data_array['id'] = $item['id'];
            $data_array['agent'] = $item['agent_name'];
            $data_array['login_email'] = $item['agent_mail'];
            $data_array['partner'] = $item['partner'];
            $data_array['dvosmijerno?'] = $item['round_trip'] ? 'Da' : 'Ne';
            $data_array['datum_rezervacije'] = $item['created_at'];
            $data_array['datum_realizacije'] = $item['date_time'];
            $data_array['transfer'] = $item['transfer'];
            $data_array['gost'] = $item['name'];
            $data_array['iznos_u_EUR'] = $this->format_excel_price($item['price']);

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
            ['Datum Od', 'Datum Do','Ukupno Rezervacija','Bruto Profit u EUR'],
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
