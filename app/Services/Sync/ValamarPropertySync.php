<?php

namespace App\Services\Sync;

use App\Services\Api;
use App\Models\Point;
use Illuminate\Database\Eloquent\Model;

class ValamarPropertySync{

    private $valamar_api;
    private $local_properties = array();
    private $live_feed_properties = array();
    private $sync_property = array();

    function __construct(){
        #Init Valamar Interface
        $this->valamar_api = new Api\ValamarClientApi();
    }

     public function sync() : void{
        #Fetching all the propertly mapped local properties
        $this->fetchLocalProperties();

        #If there are properly mapped properties in the Transfer App System - call live CRM\PMS Feed
        if(!empty($this->local_properties)){
            #Fetch Live Properties from Valamar API
            $this->fetchLiveProperties();

            #Comparing values and updating the values if changes are present
            if(!empty($this->live_feed_properties)){
                $this->extractUpdatableProperties();
            }
            #Sync Properties
            if(!empty($this->sync_property)){
                $this->sync_properties();
            }
        }
    }

    /**
     * Function used to sync the needed properties if any changes are present
     * @return void
     */
     private function sync_properties() : void{
        if(!empty($this->sync_property)){
            foreach ($this->sync_property as $composite_code => $property_data){
                #Update the database
                $point = Point::find($property_data['id']);
                #Update the name
                $point->name = $property_data['name'];

                $point->save();
            }
        }
    }


    /**
     * Function used to compare the values of the live data vs local data in order go extract the properties that need to be updated
     * @return void
     */
     private function extractUpdatableProperties() : void{

        if(!empty($this->local_properties)){
            foreach($this->local_properties as $code => $local_property){
                if(key_exists($code,$this->live_feed_properties)){

                    #Name Comparison
                    if($local_property['name'] != $this->live_feed_properties[$code]['name']){
                        $local_property['name'] = $this->live_feed_properties[$code]['name'];
                        $this->sync_property[$local_property['pms_code'].'|'.$local_property['pms_class']] = $local_property;
                    }
                }
            }
        }
    }

    /**
     * Fetch Live Properties From Valamar Feed - sort them by key and filter the valid results
     * @return void
     */
     private function fetchLiveProperties() : void{

        $live_properties = $this->valamar_api->getPropertiesList();

        #Export Only Valid Properties - the ones including PMS Code and PMS Class
        if(!empty($live_properties)){
            foreach($live_properties as $l_prop){
               if(!empty($l_prop['propertyOperaCode']) && !empty($l_prop['class'])){
                   $this->live_feed_properties[$l_prop['propertyOperaCode'].'|'.$l_prop['class']] = $l_prop;
               }
            }
        }
    }
    /**
     * Fetch DB properties mapped correctly
     * Properties Defined correctly - Points type - accommodation with valid PMS Class and PMS Code
     * @return void
     */
     private function fetchLocalProperties() : void{
        $local_properties = Point::query()
            ->where('type',Point::TYPE_ACCOMMODATION)
            ->whereNotNull('pms_code')
            ->whereNotNull('pms_class')
            ->where('active',1)->get()->toArray();

        if(!empty($local_properties)){
            foreach($local_properties as $lp_data){
                $this->local_properties[$lp_data['pms_code'].'|'.$lp_data['pms_class']] = $lp_data;
            }
        }

    }
}
