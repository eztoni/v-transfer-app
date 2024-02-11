<?php

namespace App\Models;

use App\Casts\ModelCast;
use App\Scopes\DestinationScope;
use App\Services\Api\ValamarOperaApi;
use Carbon\Carbon;
use Database\Seeders\TransferExtrasPriceSeeder;
use FontLib\Table\Type\maxp;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Cknow\Money\Money;
use Illuminate\Validation\Rules\In;

class Reservation extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    const  STATUS_ARRAY = [
        self::STATUS_CONFIRMED,
        self::STATUS_PENDING,
        self::STATUS_CANCELLED,
    ];

    protected $casts = [
        'child_seats' => 'array',
        'price_breakdown' => 'array',
        'date_time' => 'datetime',
    ];

    public const  CONFIRMATION_LANGUAGES = [
        'en' => 'English',
        'hr' => 'Hrvatski',
        'de' => 'German',
        #'fr' => 'French',
        'it' => 'Italian',
    ];

    public const TRAVELLER_TITLES = [
        'mr' => 'Mr',
        'mrs' => 'Mrs',
        'ms' => 'Ms',
    ];

    public const MOD_FIELD_TRANSLATIONS = array(
      'first_name' => 'Ime',
      'last_name' => 'Prezime',
      'email' => 'Email',
      'phone' => 'Telefon',
      'date' => 'Datum',
      'time' => 'Vrijeme',
      'adults'=> 'Odrasli',
      'children' => 'Djeca',
      'infants' => 'Dojenčad',
      'remark' => 'Popratni komentar',
      'flight_number' => 'Broj Leta',
      'luggage' => 'Prtljaga'
    );

    public function isCancelled(){
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPending(){
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(){
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isRoundTrip()
    {

        return !empty($this->round_trip_id);
    }

    public function isTotalRoundTrip(){

        $return = false;

        if(!empty($this->round_trip_id)){
            if($this->status == Reservation::STATUS_CANCELLED || $this->returnReservation->status == Reservation::STATUS_CANCELLED){
                if($this->status == Reservation::STATUS_CANCELLED && $this->returnReservation->status == Reservation::STATUS_CANCELLED){
                    $return = true;
                }else{
                    $return = false;
                }
            }else{
                $return = true;
            }
        }

        return $return;

    }

    public function isSyncedWithOpera(){
        return (bool)$this->opera_sync;
    }

    public function isDocumentConnectedSync(){
        return (bool)$this->connected_document_sync;
    }

    public function getIsRoundTripAttribute()
    {
        return !empty($this->round_trip_id);
    }

    public function getNumPassangersAttribute()
    {
        return (int)$this->adults + (int)$this->children + (int)$this->infants;
    }

    public function getLeadTravellerAttribute()
    {
        return $this->leadTraveller()->first();
    }

    public function getPrice(): Money
    {

        return Money::EUR($this->price);
    }

    public function getDisplayPrice(): Money{

        $display_price = $this->price;

        if($this->isRoundTrip && $this->returnReservation->status == 'confirmed'){
            $display_price = $this->price_breakdown[0]['price_data']['price_round_trip'];
        }

        if($this->isRoundTrip && $this->status == 'cancelled' && $this->returnReservation->status == 'confirmed'){
            $display_price = $this->returnReservation->price;
        }

        if($this->isRoundTrip && $this->status == 'confirmed' && $this->returnReservation->status == 'confirmed'){
            $display_price = $this->returnReservation->price+$this->price;
        }

        return Money::EUR($display_price);
    }

    public function getCancellationFeeAmountHRK() {

        $price = $this->getCancellationFeeAmount()*7.53450;

        return number_format($price,2);
    }

    public function hasCancellationFee() : bool{

        $return = false;

        if($this->status == Reservation::STATUS_CANCELLED){
            if($this->cancellation_fee > 0){
                $return = true;
            }
        }

        return $return;
    }

    public function getPriceHRK(): Money
    {
        $price = $this->price*7.53450;
        return Money::HRK($price);
    }

    public function getVatAmount()
    {
        return Money::EUR($this->price)->multiply($this->included_in_accommodation_reservation ?'0':'0.25');
    }
    public function getPriceWithoutVat()
    {
        return Money::EUR($this->price)->multiply($this->included_in_accommodation_reservation ?'1':'0.75');
    }

    public function leadTraveller()
    {
        return $this->belongsToMany(Traveller::class, 'reservation_traveller')->withPivot(['lead', 'comment'])->where('lead', '=', true);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    public function travellers()
    {
        return $this->belongsToMany(Traveller::class)->withPivot(['lead', 'comment']);
    }

    public function otherTravellers()
    {
        return $this->belongsToMany(Traveller::class)->withPivot(['lead', 'comment'])->where('lead', '=', false);
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function extras()
    {
        return $this->belongsToMany(Extra::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Point::class, 'pickup_location','id');
    }
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(Point::class, 'dropoff_location','id' );
    }

    public function pickupAddress()
    {
        return $this->belongsTo(Point::class, 'pickup_address_id','id');
    }

    public function dropoffAddress()
    {
        return $this->belongsTo(Point::class, 'dropoff_address_id','id' );
    }

    public function returnReservation()
    {
        return $this->hasOne(Reservation::class, 'id', 'round_trip_id')->where('is_main', false);
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    protected function getTransferPriceCommissionAttribute(): Money
    {

        $total = (int) \Arr::get($this->transfer_price_state,'amount.amount');
        $totalWithCommission = (int) \Arr::get($this->transfer_price_state,$this->is_round_trip?'price_data.round_trip_price_with_commission':  'price_data.price_with_commission');

        $commission =Money::EUR(0);

        if($total && $totalWithCommission){
            $commission=Money::EUR($totalWithCommission );
        }

        return $commission;
    }

    public function getRouteTransferTaxLevel(){

        $return = 0;

        ##One Way Transfer
        $route = Route::query()
            ->where('destination_id', $this->destination_id)
            ->where('starting_point_id', $this->pickup_location)
            ->where('ending_point_id', $this->dropoff_location)
            ->get()->first();

        $route_transfer = \DB::table('route_transfer')
            ->where('route_id',$route->id)
            ->where('partner_id',$this->partner_id)
            ->where('transfer_id',$this->transfer_id)
            ->get()->first();

        if($route_transfer){
            $return = $route_transfer->tax_level;
        }

        return $return;
    }

    public function getTotalCommissionAmountAttribute():Money
    {
        return $this->transfer_price_commission->add($this->extras_summed_commission);
    }

    public function getOperaLog(){

            $log = ValamarOperaApi::getSyncOperaLog($this->id);

            return $log;
    }

    public function getOverallReservationStatus(){

        $status = $this->status;

        if($this->is_roundtrip && $this->returnReservation->status == 'cancelled' && $this->status == 'confirmed'){
            $status = self::STATUS_CONFIRMED;
        }



        if($this->is_roundtrip && $this->returnReservation->status == 'confirmed' && $this->status == 'cancelled'){
            $status = self::STATUS_CONFIRMED;
        }

        return $status;
    }

    public function get_extras_list(){

        $extras = array();

        if($this->extras){
            foreach($this->extras as $extra){
                $extras[] = (string)$extra->name;
            }
        }

        return implode(',',$extras);
    }

    protected function getExtrasSummedCommissionAttribute(): Money
    {
        $commission =Money::EUR(0);

        $extraPriceData = $this->extras_price_states;

        foreach ($extraPriceData as $extraArray){
            $total = (int) \Arr::get($extraArray,'amount.amount');
            $totalWithCommission = (int) \Arr::get($extraArray,'price_data.price_with_commission');
            if($total && $totalWithCommission){
                $extraCommission =Money::EUR($totalWithCommission );
                $commission = $commission->add($extraCommission);
            }
        }

        return $commission;
    }

    public function getFormattedOtherTravellers(){

        $return = array();

        if($this->otherTravellers()){
            foreach ($this->otherTravellers as $traveller){
                $return[] = $traveller->full_name;
            }
        }

        $return = implode(',',$return);

        return $return;
    }

    public function getCancellationFeeAmount($cf = false){

        if($cf){
            return number_format($this->cancellation_fee,2);
        }else{

            $price = $this->getPrice()->formatByDecimal();

            return '-'.$price;
        }
    }

    public function getCancellationVatAmount($cf = false){

        if($cf){
            if($this->getCancellationVATPercentage() > 0){
                return number_format($this->getCancellationFeeAmount('cf')*($this->getCancellationVATPercentage('cf')/100),2);
            }else{
                return 0;
            }
        }else{
            if($this->included_in_accommodation_reservation){
                return 0;
            }else{
                return '-'.$this->getVatAmount()->formatByDecimal();
            }
        }

    }

    public function getCancellationWithoutVat(){

        if($this->hasCancellationFee()){
            if($this->getCancellationVATPercentage() > 0){
                return number_format($this->getCancellationFeeAmount()-$this->getCancellationVatAmount(),2);
            }else{
                return number_format($this->getCancellationFeeAmount(),2);
            }
        }else{
            return '-'.$this->getPriceWithoutVat()->formatByDecimal();
        }


    }

    /**
     * @param $segment
     * @return void
     */
    public function getConfirmationItemBreakdown($segment){

        $return['items'] = array();

        ##One Way Transfer
        $route = Route::query()
            ->where('destination_id', $this->destination_id)
            ->where('starting_point_id', $this->pickup_location)
            ->where('ending_point_id', $this->dropoff_location)
            ->get()->first();


        $route_transfer = \DB::table('route_transfer')
            ->where('route_id',$route->id)
            ->where('partner_id',$this->partner_id)
            ->where('transfer_id',$this->transfer_id)
            ->get()->first();


        $price = Money::EUR($route_transfer->price)->formatByDecimal();
        $vat = $this->included_in_accommodation ? 0 : 25;
        $vat_amount = number_format($price*($vat/100),2);

        $operaPackageID = $route_transfer->opera_package_id;

        $item = array(
          'code' => $operaPackageID,
          'transfer' => $this->pickupLocation->name.' - '.$this->dropoffLocation->name,
          'amount' => $price,
          'vat' => $vat,
          'vat_amount' => $vat_amount,
          'price' => $price
        );

        $return['items'][] = $item;

        if($this->status == 'cancelled'){

            $price = number_format($price*(-1),2);
            $vat_amount = number_format($vat_amount*(-1),2);

            $item = array(
                'code' => $operaPackageID,
                'transfer' => $this->pickupLocation->name.' - '.$this->dropoffLocation->name,
                'amount' => $price,
                'vat' => $vat,
                'vat_amount' => $vat_amount,
                'price' => $price
            );
            $return['items'][] = $item;
        }

        if($this->round_trip_id){

            $round_trip_reservation = Reservation::findOrFail($this->round_trip_id);

            $return_route = Route::query()
                ->where('destination_id', $round_trip_reservation->destination_id)
                ->where('starting_point_id', $round_trip_reservation->pickup_location)
                ->where('ending_point_id', $round_trip_reservation->dropoff_location)
                ->get()->first();


            $return_route_transfer = \DB::table('route_transfer')
                ->where('route_id',$return_route->id)
                ->where('partner_id',$round_trip_reservation->partner_id)
                ->where('transfer_id',$round_trip_reservation->transfer_id)
                ->get()->first();


                $returnOperaPackageID = $return_route_transfer->opera_package_id;

                $price = Money::EUR($return_route_transfer->price)->formatByDecimal();
                $vat = $this->included_in_accommodation ? 0 : 25;
                $vat_amount = number_format($price*($vat/100),2);


                $item = array(
                    'code' => $returnOperaPackageID,
                    'transfer' => $round_trip_reservation->pickupLocation->name.' - '.$round_trip_reservation->dropoffLocation->name,
                    'amount' => $price,
                    'vat' => $vat,
                    'vat_amount' => $vat_amount,
                    'price' => $price
                );

                $return['items'][] = $item;

                if($round_trip_reservation->status == 'cancelled'){

                    $price = number_format($price*(-1),2);
                    $vat_amount = number_format($vat_amount*(-1),2);

                    $item = array(
                        'code' => $returnOperaPackageID,
                        'transfer' => $round_trip_reservation->pickupLocation->name.' - '.$round_trip_reservation->dropoffLocation->name,
                        'amount' => $price,
                        'vat' => $vat,
                        'vat_amount' => $vat_amount,
                        'price' => $price
                    );

                    $return['items'][] = $item;
                }
        }

            #Item Summary
            if(!empty($return['items'])){

                $return['items_total'] = 0;
                $return['items_vat_total'] = 0;

                foreach($return['items'] as $item){

                    $return['items_total'] = $return['items_total']+$item['amount'];
                    $return['items_vat_total'] = $return['items_vat_total']+$item['vat_amount'];
                }

                #Items Total
                $return['items_total'] = number_format($return['items_total'],2);

                #Vat Total
                $return['items_vat_total'] = number_format($return['items_vat_total'],2);

                $return['items_total_hrk'] = $return['items_total']*7.53450;

                $return['items_total_hrk'] = number_format($return['items_total_hrk'],2);

            }

            $return['tax_group'] = $this->included_in_accommodation ? 0 : 25;
            $return['items_total_base'] = number_format($return['items_total']*((100-$return['tax_group'])/100),2);

        return $return[$segment];
    }

    public function saveConfirmationDocument(){

        $document_name = 'res_'.$this->id.'_booking_confirmation.pdf';

        PDF::loadView('attachments.booking_confirmation', ['reservation'=>Reservation::find($this->id)])->save(storage_path().'/app/public/temp_pdf/'.$document_name);

    }
    public function saveCancellationDocument(){

        $document_name = 'res_'.$this->id.'_booking_cancellation.pdf';

        PDF::loadView('attachments.booking_cancellation', ['reservation'=>Reservation::find($this->id)])->save(storage_path().'/app/public/temp_pdf/'.$document_name);

    }

    public function saveModificationDocument(){

        $document_name = 'res_'.$this->id.'_booking_modification_'.time().'.pdf';

        PDF::loadView('attachments.booking_confirmation', ['reservation'=>Reservation::find($this->id)])->save(storage_path().'/app/public/temp_pdf/'.$document_name);

    }

    public function saveCancellationFeeDocument(){

        $document_name = 'res_'.$this->id.'_booking_cancellation_fee.pdf';

        PDF::loadView('attachments.booking_cancellation_fee', ['reservation'=>Reservation::find($this->id)])->save(storage_path().'/app/public/temp_pdf/'.$document_name);

    }

    public function getCancellationItemBreakDown($segment){

        $return['items'] = array();

        if($this->status == Reservation::STATUS_CANCELLED){

            $code = $this->getCancellationPackageId();

            ##One Way Transfer
            $route = Route::query()
                ->where('destination_id', $this->destination_id)
                ->where('starting_point_id', $this->pickup_location)
                ->where('ending_point_id', $this->dropoff_location)
                ->get()->first();


            $route_transfer = \DB::table('route_transfer')
                ->where('route_id',$route->id)
                ->where('partner_id',$this->partner_id)
                ->where('transfer_id',$this->transfer_id)
                ->get()->first();

            #Basic Route
            $price = Money::EUR($route_transfer->price)->formatByDecimal();
            $vat = $this->included_in_accommodation ? 0 : 25;
            $vat_amount = number_format($price*($vat/100),2);

            $operaPackageID = $route_transfer->opera_package_id;

            #Original Route - added for the calculation
            $item = array(
                'code' => $operaPackageID,
                'transfer' => $this->pickupLocation->name.' - '.$this->dropoffLocation->name,
                'amount' => $price,
                'vat' => $vat,
                'vat_amount' => $vat_amount,
                'price' => $price
            );

            #Add Original Route To the Invoice
            $return['items'][] = $item;

            $item_name = $this->confirmation_language == 'hr' ? 'Otkaz' : 'Cancellation';

            #No Cancellation Fee - everything goes in negative
            $item['transfer'] = $item_name.' - '.$item['transfer'];
            $item['amount'] = '-'.$item['amount'];
            $item['vat_amount'] = '-'.$item['vat_amount'];
            $item['price'] = '-'.$item['price'];

            $return['items'][] = $item;

            #Return Route
            if($this->is_main && $this->isRoundTrip()){

                $round_trip_res = Reservation::findOrFail($this->round_trip_id);

                if($this->cancellation_type == 'no_show'){
                    $code = $round_trip_res->partner->no_show_package_id;
                }else{
                    $code = $round_trip_res->partner->cancellation_package_id;
                }

                ##One Way Transfer
                $route = Route::query()
                    ->where('destination_id', $round_trip_res->destination_id)
                    ->where('starting_point_id', $round_trip_res->pickup_location)
                    ->where('ending_point_id', $round_trip_res->dropoff_location)
                    ->get()->first();


                $route_transfer = \DB::table('route_transfer')
                    ->where('route_id',$route->id)
                    ->where('partner_id',$round_trip_res->partner_id)
                    ->where('transfer_id',$round_trip_res->transfer_id)
                    ->get()->first();

                #Basic Route
                $price = Money::EUR($route_transfer->price)->formatByDecimal();
                $vat = $this->included_in_accommodation ? 0 : 25;
                $vat_amount = number_format($price*($vat/100),2);

                $operaPackageID = $route_transfer->opera_package_id;

                #Original Route - added for the calculation
                $item = array(
                    'code' => $operaPackageID,
                    'transfer' => $round_trip_res->pickupLocation->name.' - '.$round_trip_res->dropoffLocation->name,
                    'amount' => $price,
                    'vat' => $vat,
                    'vat_amount' => $vat_amount,
                    'price' => $price
                );

                #Add Original Route To the Invoice
                $return['items'][] = $item;

                #No Cancellation Fee - everything goes in negative
                $item['transfer'] = 'Cancellation - '.$item['transfer'];
                $item['amount'] = '-'.$item['amount'];
                $item['vat_amount'] = '-'.$item['vat_amount'];
                $item['price'] = '-'.$item['price'];

                $return['items'][] = $item;

            }

            #Item Summary
            if(!empty($return['items'])){

                $return['items_total'] = 0;
                $return['items_vat_total'] = 0;

                foreach($return['items'] as $item){
                    $return['items_total'] = $return['items_total']+$item['amount'];
                    $return['items_vat_total'] = $return['items_vat_total']+$item['vat_amount'];
                }

                #Items Total
                $return['items_total'] = number_format($return['items_total'],2);

                #Vat Total
                $return['items_vat_total'] = number_format($return['items_vat_total'],2);

                $return['items_total_hrk'] = $return['items_total']*7.53450;

                $return['items_total_hrk'] = number_format($return['items_total_hrk'],2);

            }

            $return['tax_group'] = $this->included_in_accommodation ? 0 : 25;
            $return['items_total_base'] = number_format($return['items_total']*((100-$return['tax_group'])/100),2);
        }

        return $return[$segment];
    }

    public function getCancellationFeeItemBreakDown($segment){

        $return['items'] = array();

        if($this->status == Reservation::STATUS_CANCELLED || ($this->isRoundTrip && $this->returnReservation->status == 'cancelled' && $this->returnReservation->hasCancellationFee())){

            $item_name = 'Cancellation Fee';
            $code = $this->getCancellationPackageId();

            if($this->cancellation_type == 'no_show'){
                $item_name = 'No Show';
                $code = $this->partner->no_show_package_id;
            }else{
                $code = $this->partner->cancellation_package_id;
            }

            #If there is a cancellation fee involved
            if($this->hasCancellationFee()){
                $item = array(
                    'code' => $code,
                    'transfer' => $item_name.' - ('.$this->getCancellationPercentage().'%) - '.$this->pickupLocation->name.' - '.$this->dropoffLocation->name,
                    'amount' => $this->getCancellationFeeAmount('cf'),
                    'vat' => $this->getCancellationVATPercentage('cf'),
                    'vat_amount' => $this->getCancellationVatAmount('cf'),
                    'price' => $this->getCancellationFeeAmount('cf'),
                );

                $return['items'][] = $item;
            }

            #Return Route
            if($this->is_main && $this->isRoundTrip()){

                $round_trip_res = Reservation::findOrFail($this->round_trip_id);

                if($this->cancellation_type == 'no_show'){
                    $item_name = 'No Show';
                    $code = $round_trip_res->partner->no_show_package_id;
                }else{
                    $code = $round_trip_res->partner->cancellation_package_id;
                }

                #If there is a cancellation fee involved
                if($round_trip_res->hasCancellationFee()){
                    $item = array(
                        'code' => $code,
                        'transfer' => $item_name.' - ('.$round_trip_res->getCancellationPercentage().'%) - '.$round_trip_res->pickupLocation->name.' - '.$round_trip_res->dropoffLocation->name,
                        'amount' => $round_trip_res->getCancellationFeeAmount('cf'),
                        'vat' => $round_trip_res->getCancellationVATPercentage('cf'),
                        'vat_amount' => $round_trip_res->getCancellationVatAmount('cf'),
                        'price' => $round_trip_res->getCancellationFeeAmount('cf'),
                    );

                    $return['items'][] = $item;
                }

            }

            #Item Summary
            if(!empty($return['items'])){

                $return['items_total'] = 0;
                $return['items_vat_total'] = 0;

                foreach($return['items'] as $item){
                    $return['items_total'] = $return['items_total']+$item['amount'];
                    $return['items_vat_total'] = $return['items_vat_total']+$item['vat_amount'];
                }

                #Items Total
                $return['items_total'] = number_format($return['items_total'],2);

                #Vat Total
                $return['items_vat_total'] = number_format($return['items_vat_total'],2);

                $return['items_total_hrk'] = $return['items_total']*7.53450;

                $return['items_total_hrk'] = number_format($return['items_total_hrk'],2);

            }

            $return['tax_group'] = $this->included_in_accommodation ? 0 : 25;
            $return['items_total_base'] = number_format($return['items_total']*((100-$return['tax_group'])/100),2);
        }

        return $return[$segment];
    }

    public function getCancellationPackageId($no_show = false){

        if($no_show){
            return $this->partner->no_show_package_id;
        }else{
            return $this->partner->cancellation_package_id;
        }

    }

    public function getCancellationVATPercentage($cf = false){

        if($this->included_in_accommodation_reservation){
            return 0;
        }else{
            if($cf){
                return 0;
            }
            return 25;
        }
    }

    public function getCancellationPercentage(){

        $return = 0;
        #Calculate Percentage
        $percentage = ($this->cancellation_fee/$this->getPrice()->formatByDecimal())*100;
        $percentage = (int)$percentage;

        $return = $percentage;

        return $return;
    }


    public function ConfirmationSentMailToGuest() : bool{

        $return = false;

        $mail_logs = \DB::table('reservationmails')->where('reservation_id','=',$this->id)->where('email_type','guest_confirmation')->get()->first();

        if(!empty($mail_logs)){
            $return = true;
        }

        return $return;
    }

    public function ConfirmationSentMailToPartner() : bool{

        $return = false;

        $mail_logs = \DB::table('reservationmails')->where('reservation_id','=',$this->id)->where('email_type','partner_confirmation')->get()->first();

        if(!empty($mail_logs)){
            $return = true;
        }

        return $return;
    }

    public function getAccommodationReservationCode(){
        return $this->leadTraveller()->first()->reservation_number != null ? $this->leadTraveller()->first()->reservation_number : '-';
    }

    public function getAccommodationData($property){

        #Case - Pickup Address is type of Accommodation
        if($this->pickupAddress->type == Point::TYPE_ACCOMMODATION){
            $return =  $this->pickupAddress->$property;
        }

        #Case - Dropoff Address is type of Accommodation
        if($this->dropoffAddress->type == Point::TYPE_ACCOMMODATION){
            $return = $this->dropoffAddress->$property;
        }

        return $return;
    }

    public function getOperatorName(){

        $return = 'Valamar User';

        $user_data = User::findOrFail($this->created_by);

        if($user_data){
            $return = $user_data->name;
        }

        return $return;
    }

    public function getLatestInvoiceError(){

        $error = 'Unknown error has occurred';

        $invoice_log_data = \DB::table('opera_fiskal_log')->where('reservation_id',$this->id)->where('log_type','fiskal')->where('status','error')->orderBy('id','desc')->get()->first();

        if(!empty($invoice_log_data)){
           $log = json_decode($invoice_log_data->response,true);

           if(!empty($log['error'])){
               $error = trim($log['error']);
           }
        }

        return $error;
    }
    public function getInvoiceData($param,$invoice_type = 'reservation'){

        $invoice_data = \DB::table('invoices')->where('reservation_id','=',$this->id)->where('invoice_type',$invoice_type)->latest()->first();

        $return = '-';

        if(!empty($invoice_data)){
            switch ($param){
                case 'invoice_number':
                    $return = $invoice_data->invoice_id.'/'.$invoice_data->invoice_establishment.'/'.$invoice_data->invoice_device;
                    break;
                case 'zki':
                    $return = $invoice_data->zki;
                    break;
                case 'jir':
                    $return = $invoice_data->jir;
                    break;
                case 'amount':
                    $return = $invoice_data->amount.' €';
                    break;
            }
        }

        return $return;
    }

    public function getExtrasPriceStatesAttribute(){
        return \Arr::where($this->price_breakdown, fn($i) => $i['item']==='extra');
    }

    public function getTransferPriceStateAttribute():array
    {
        return \Arr::first(\Arr::where($this->price_breakdown, fn($i) => $i['item']==='transfer_price'));
    }

    protected static function booted()
    {
        static::addGlobalScope(new DestinationScope());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->isDirty('created_by') && auth()->user()) {
                $model->created_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by') && auth()->user()) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }

    public function getReservationReceptionEmail(){

        $return = false;

        $email = $this->getAccommodationData('reception_email');

        if(!empty($email) && filter_var($email,FILTER_VALIDATE_EMAIL)){
            $return = trim($email);
        }

        return $return;
    }

    protected function priceBreakdown(): Attribute
    {
        return Attribute::make(
            get: function ($breakdown){
                $breakdown = json_decode($breakdown, true);
                foreach ($breakdown as $key => $breakdownItem){
                    if(\Arr::get($breakdownItem,'item') === 'extra'){
                        if(\Arr::get($breakdownItem,'model')){
                            $breakdown[$key]['model'] = Extra::make(\Arr::get($breakdownItem,'model'));
                        }
                    }
                }
                return $breakdown;
            },
        );
    }

    public function hasModifications(){

        $return = false;

        if($this->is_main && $this->status == 'confirmed'){

            $modification_logs = \DB::table('reservation_modification')->where('reservation_id','=',$this->id)->where('sent','=',0)->get();

            if(!empty($modification_logs)){

                foreach($modification_logs as $log){
                    foreach($log as $parameter => $value){

                        if($value == 1 && $parameter != 'updated_by'){
                            if($return === false){
                                $return = array();
                            }


                            if(isset($return[$this->id])){
                                if(!in_array(self::MOD_FIELD_TRANSLATIONS[$parameter],$return[$this->id]['modifications'])){
                                    $return[$this->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                                }
                            }else{
                                $return[$this->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                            }

                        }

                        if(!empty($return[$this->id])){
                            $return[$this->id]['direction'] = $this->pickupLocation->name.' => '.$this->dropoffLocation->name;
                        }
                    }

                }
            }

            if($this->isRoundTrip() && $this->returnReservation->status == 'confirmed'){
                $modification_logs = \DB::table('reservation_modification')->where('reservation_id','=',$this->returnReservation->id)->where('sent','=',0)->get();

                if(!empty($modification_logs)){

                    foreach($modification_logs as $log){
                        foreach($log as $parameter => $value){

                            if($value == 1 && $parameter != 'updated_by'){
                                if($return === false){
                                    $return = array();
                                }


                                if(isset($return[$this->returnReservation->id])){
                                    if(!in_array(self::MOD_FIELD_TRANSLATIONS[$parameter],$return[$this->returnReservation->id]['modifications'])){
                                        $return[$this->returnReservation->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                                    }
                                }else{
                                    $return[$this->returnReservation->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                                }

                            }

                            if(!empty($return[$this->returnReservation->id])){
                                $return[$this->returnReservation->id]['direction'] = $this->returnReservation->pickupLocation->name.' => '.$this->returnReservation->dropoffLocation->name;
                            }
                        }

                    }
                }
            }
        }

        if(!$this->is_main){

            $main_res = Reservation::where('round_trip_id','=',$this->id)->get()->first();

            $modification_logs = \DB::table('reservation_modification')->where('reservation_id','=',$main_res->id)->where('sent','=',0)->get();

            if(!empty($modification_logs)){

                foreach($modification_logs as $log){
                    foreach($log as $parameter => $value){

                        if($value == 1 && $parameter != 'updated_by'){
                            if($return === false){
                                $return = array();
                            }


                            if(isset($return[$main_res->id])){
                                if(!in_array(self::MOD_FIELD_TRANSLATIONS[$parameter],$return[$main_res->id]['modifications'])){
                                    $return[$main_res->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                                }
                            }else{
                                $return[$main_res->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                            }

                        }

                        if(!empty($return[$main_res->id])){

                            $return[$main_res->id]['direction'] = $main_res->pickupLocation->name.' => '.$main_res->dropoffLocation->name;
                        }
                    }

                }
            }

            if($main_res->isRoundTrip() && $main_res->returnReservation->status == 'confirmed'){

                $modification_logs = \DB::table('reservation_modification')->where('reservation_id','=',$main_res->returnReservation->id)->where('sent','=',0)->get();

                if(!empty($modification_logs)){

                    foreach($modification_logs as $log){
                        foreach($log as $parameter => $value){

                            if($value == 1 && $parameter != 'updated_by'){
                                if($return === false){
                                    $return = array();
                                }


                                if(isset($return[$main_res->returnReservation->id])){
                                    if(!in_array(self::MOD_FIELD_TRANSLATIONS[$parameter],$return[$main_res->returnReservation->id]['modifications'])){
                                        $return[$main_res->returnReservation->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                                    }
                                }else{
                                    $return[$main_res->returnReservation->id]['modifications'][] = self::MOD_FIELD_TRANSLATIONS[$parameter];
                                }

                            }

                            if(!empty($return[$main_res->returnReservation->id])){
                                $return[$main_res->returnReservation->id]['direction'] = $main_res->pickupLocation->name.' => '.$main_res->returnReservation->dropoffLocation->name;
                            }
                        }

                    }
                }
            }
        }

        return $return;
    }

    public function setModificationsAsSent(){

        $mods = $this->hasModifications();

        if(!empty($mods)){

            $res = array_unique(array_keys($mods));

            if(!empty($res)){
                foreach($res as $res_id){
                    \DB::table('reservation_modification')->where('reservation_id','=',$res_id)->update(['sent' => 1]);
                }
            }
        }
    }

}
