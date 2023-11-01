<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Language;
use App\Models\Owner;
use App\Models\Partner;
use App\Models\User;
use App\Scopes\CompanyScope;
use App\Scopes\DestinationScope;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class PartnersOverview extends Component
{
    use Actions;

    use WithPagination;

    public $search = '';
    public $partner;
    public $partnerModal;
    public $copyPartnerModal;
    public $softDeleteModal;
    public $deleteId = '';
    public $selectedDestinations = [];
    public $destinationCopyOwnerId = 0;
    public $otherOwners = array();
    public $cf_types = array(
        'percent' => 'Percentage ( % )',
        'nominal' => 'Nominal ( € )'
    );

    public $copyTermsModal = false;

    public $companyLanguages = ['en'];

    public $partnerPreviewId = 0;

    public $terms = [
        'en' => null
    ];

    public function getPartnersWithTermsProperty()
    {
        if($this->partner->id > 0){
            return Partner::whereNotNull('terms')->where('id','!=',$this->partner->id)->get();
        }else{
            return Partner::whereNotNull('terms')->get();
        }

    }

    public function openCopyTermsModal(){
        $this->copyTermsModal = true;
    }
    public function closeCopyPartnerModal(){
        $this->copyPartnerModal = false;
    }


    public function getTermsPreviewProperty(){
        return \Arr::get($this->partnersWithTerms->where('id',$this->partnerPreviewId)->first()?->getTranslations(),'terms');
    }

    public function copyPartnerTerms()
    {
        $this->terms = $this->termsPreview;
        $this->closeCopyTermsModal();
        $this->partnerPreviewId = 0;
    }

    public function closeCopyTermsModal(){
        $this->copyTermsModal = false;
    }

    protected function rules()
    {
        $ruleArray= [
            'terms.en' => 'required|min:3',
            'partner.name'=>'required|max:255|min:2',
            'partner.email'=>'required|email',
            'partner.address'=>'required|min:3',
            'selectedDestinations'=>'required',
            'partner.phone'=>'required|max:255',
            'partner.cancellation_package_id' => 'required|int',
            'partner.no_show_package_id' => 'required|int',
            'partner.cf_amount_12' => 'required|int',
            'partner.cf_amount_24' => 'required|int',
            'partner.cf_type' => 'required|in:percent,nominal'
        ];

        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['terms.' . $lang] = 'nullable|min:3';
            }
        }
    return $ruleArray;
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openPartnerModal(){
        $this->partnerModal = true;
    }

    public function openCopyPartnerModal(){
        $this->copyPartnerModal = true;
    }

    public function closePartnerModal(){
        $this->partnerModal = false;
    }

    public function mount(){
        $this->instantiateComponentValues();
    }

    public function instantiateComponentValues()
    {
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
        $this->partner = new Partner();

        $this->otherOwners = \DB::table('owners')->where('id','!=',Auth::user()->owner_id)->get();

        if(!empty($this->otherOwners)){
            $this->destinationCopyOwnerId = $this->otherOwners->first()->id;
        }

        foreach ($this->companyLanguages as $lang) {
            $this->terms[$lang] = $this->partner->getTranslation('terms', $lang, false);
        }

    }


    public function updatedPartnerDestinationId(){
        $this->partner->starting_point_id = null;
        $this->partner->ending_point_id = null;
    }

    public function addPartner(){
        $this->terms = [];
        $this->openPartnerModal();
        $this->partner = new Partner();

        #Set Default Cancellation Fee Type to be percentage
        $this->partner->cf_type = 'percent';
        #Set Default Value of Cancellation Fee Amount for < 12 hours
        $this->partner->cf_amount_12 = 100;
        #Set Default Value of Cancellation Fee Amount for < 24 hours
        $this->partner->cf_amount_24 = 50;

        $this->selectedDestinations = [];
    }

    public function copyPartner($partnerId){

        $this->partner = Partner::withoutGlobalScope(DestinationScope::class)->find($partnerId);
        $this->openCopyPartnerModal();
    }
    public function updatePartner($partnerId){

        $this->openPartnerModal();
        $this->partner = Partner::withoutGlobalScope(DestinationScope::class)->find($partnerId);
        $this->terms = \Arr::get($this->partner->getTranslations(),'terms',['en'=>'']);
        $this->selectedDestinations = $this->partner?->destinations()?->pluck('id')->toArray();

    }

    public function savePartnerData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->partner->setTranslations('terms', $this->terms);

        $this->partner->owner_id = Auth::user()->owner_id;
        $this->validate();
        $this->partner->save();
        $this->partner->destinations()->sync($this->selectedDestinations);
        $this->notification()->success('Saved','Partner Saved');
        $this->terms = [];
        $this->closePartnerModal();

    }

    //------------ Soft Delete ------------------
    public function openSoftDeleteModal($id){
        $this->deleteId = $id;
        $this->softDeleteModal = true;
    }

    public function closeSoftDeleteModal(){
        $this->deleteId = '';
        $this->softDeleteModal = false;
    }

    public function softDelete(){
        Partner::find($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->notification()->success('Partner deleted','',);
    }
    //------------- Soft Delete End ---------

    public function copyPartnerToOwner(){

        if($this->destinationCopyOwnerId > 0){

            $partnerCopy = $this->partner->replicate();

            $partnerCopy->owner_id = $this->destinationCopyOwnerId;

            $partnerCopy->save();

            $ownerDestinations = \DB::table('destinations')->where('owner_id',$this->destinationCopyOwnerId)->get();

            $partnerCopy->destinations()->sync($ownerDestinations->pluck('id')->toArray());

            $this->notification()->success('Partner copied to destination company','',);
        }

        $this->closeCopyPartnerModal();
    }
    public function render()
    {
        $destinations = Destination::all();
        $partners = Partner::withoutGlobalScope(DestinationScope::class)->with('destinations')->search('name',$this->search)->paginate(10);
        return view('livewire.partners-overview',compact('partners','destinations'));
    }
}
