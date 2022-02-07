<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/7/2021
 */

namespace App\Http\Livewire;

use App\Models\Partner;
use App\Models\Worker;
use Livewire\Component;

class PartnersOverview extends Component
{
    public $search = '';
    public function softDelete($id){
        Partner::find($id)->delete();
        $this->showToast('Partner Izbrisan','',);
    }
    public function render()
    {
        $partners = Partner::search('business_name',$this->search)->withCount('tasks')->withCount('comments')->paginate(10);
        return view('livewire.partners-overview', compact('partners'));
    }
}
