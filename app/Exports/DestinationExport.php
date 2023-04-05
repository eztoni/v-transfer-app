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

            $data_array = array();

            if ($this->isPartnerReport) {

                $data_array['partner'] = $item['name'];
                $data_array['kontigent'] = $item['transfer'];
                $data_array['prodajno_mjesto'] = 'VEC';
                $data_array['vrsta_plaćanja'] = 'REZERVACIJA NA SOBU';
                $data_array['broj_računa'] = $item['invoice_number'];
                $data_array['porezna_grupa'] = $item['tax_level'];
                $data_array['postupak'] = $item['status'] == 'confirmed' ? 'RP' : 'CF';
                $data_array['datum_prodaje'] = $item['voucher_date'];
                $data_array['bruto_prihod'] = $item['price_eur'];
                $data_array['ugovorena_provizija'] = $item['commission'].'%';
                $data_array['trošak_ulaznog_računa'] = $item['invoice_charge'];
                $data_array['bruto_profit'] = $item['commission_amount'];
                $data_array['pdv'] = $item['pdv'];
                $data_array['neto_profit'] = $item['net_income'];

            }else{
                $data_array['partner'] = $item['name'];
                $data_array['kontigent'] = $item['transfer'];
                $data_array['datum_vouchera'] = $item['voucher_date'];
                $data_array['prodajno_mjesto'] = 'VEC';
                $data_array['voucher_id'] = $item['id'];
                $data_array['porezna_grupa'] = $item['tax_level'];
                $data_array['nosite_vouchera'] = $item['name'];
                $data_array['odrasli'] = $item['adults'];
                $data_array['djeca'] = (int)$item['children']+(int)$item['infants'];
                $data_array['bruto_prihod'] = $item['price_eur'];
                $data_array['trošak_ulaznog_računa'] = $item['invoice_charge'];
                $data_array['bruto_profit'] = $item['commission_amount'];
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
            ['Datum Od', 'Datum Do', 'Partner', 'Destinacija','Ukupni Prihodi','Ukupna Provizija'],
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
