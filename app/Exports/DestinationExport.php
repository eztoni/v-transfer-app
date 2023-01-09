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

    public function __construct(private array $array, private bool $isPartnerReport)
    {

        $this->data =  collect($array);
        $this->format();

    }

    private function format()
    {
        $this->data =  $this->data->map(function ($item) {
            if (!$this->isPartnerReport) {
                unset($item['tax_level']);
                unset($item['commission']);
                unset($item['commission_amount']);
            }

            foreach ($item as $key => $value) {
                if (!$value) {
                    $item[$key] = '0';
                }
            }


            return $item;
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
            ['Date from', 'Date to', 'Partner', 'Destination','Total Revenue','Total commission'],
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
