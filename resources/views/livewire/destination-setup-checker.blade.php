<div x-data="{selectedLanguage:'en'}">

<!-- Points -->
<x-card title="Destination - {{$destination->name}} - Partner Mapping Check">

    <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50" title="naziv tablice">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Partner Name</th>
                <th>Cancellation Package ID</th>
                <th>Missing Cancellation Package ID activations</th>
                <th>No Show Package ID</th>
                <th>Missing No Show Package ID activations</th>
            </tr>
        </thead>
        @if($partners)
            @foreach($partners as $partner)
                <tr>
                    <td>#{{$partner->id}}</td>
                    <td>{{$partner->name}}</td>
                    <td style="text-align: center">
                        {{$partner->cancellation_package_id}} @if($partner->cancellation_package_id > 0) <x-button  sm positive icon="check"></x-button> @else <x-button sm  negative icon="check"></x-button> @endif
                    </td>
                    <td style="text-align: center">
                        @if($partner->cancellation_package_id > 0)
                            @if($this->getPackagePropertyMapping($partner->cancellation_package_id) > 0)
                                @foreach($this->getPackagePropertyMapping($partner->cancellation_package_id) as $code)
                                    <small>{{$code}}  <x-button xs negative icon="check"></x-button> </small><br/>
                                @endforeach
                            @else
                                <small>Mapped Properly <x-button xs positive icon="check"></x-button></small>
                            @endif
                        @else
                            <small>No information.</small>
                        @endif

                    </td>
                    <td style="text-align: center">
                        {{$partner->no_show_package_id}} @if($partner->no_show_package_id > 0) <x-button sm  positive icon="check"></x-button> @else <x-button sm negative icon="check"></x-button> @endif
                    </td>
                    <td style="text-align: center">
                        @if($partner->no_show_package_id > 0)
                            @if($this->getPackagePropertyMapping($partner->no_show_package_id) > 0)
                                @foreach($this->getPackagePropertyMapping($partner->no_show_package_id) as $code)
                                    <small>{{$code}}  <x-button xs negative icon="check"></x-button> </small><br/>
                                @endforeach
                            @else
                                <small>Mapped Properly <x-button xs positive icon="check"></x-button></small>
                            @endif
                        @else
                            <small>No information.</small>
                        @endif

                    </td>
                </tr>
            @endforeach
        @endif

    </table>



</x-card>

    <br/>
<!-- Accommodation -->
<x-card title="Destination - {{$destination->name}} - Accomodation Setup Check">

    <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50" title="naziv tablice">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Property Name</th>
                <th>Address</th>
                <th>Reception Email</th>
                <th style="text-align: center;">Code Mapping ( Live MDP Comparison )</th>
                <th>Invoice No</th>
                <th>Bus. Est.</th>
                <th>Device</th>
            </tr>
        </thead>
        @if($points)
            @foreach($points as $point)
                <tr>
                    <td>#{{$point->id}}</td>
                    <td style="text-align: center">
                        {{$point->name}} <br/>@if($point->name) @else <x-button sm  negative icon="check"></x-button> @endif
                    </td>
                    <td style="text-align: center">
                        {{$point->address}} <br/>@if($point->address)  @else <x-button sm  negative icon="check"></x-button> @endif
                    </td>
                    <td style="text-align: center">
                        {{$point->reception_email}} <br/>@if($point->reception_email)  @else <x-button sm  negative icon="check"></x-button> @endif
                    </td>
                    <td style="text-align: center">
                        @if($propertyMapTest[strtoupper($point->pms_code.'|'.$point->pms_class)] == 1)
                            <p>Property Mapped Correctly <x-button sm positive icon="check"></x-button><br/><b>PMS Code:</b> {{$point->pms_code}}<br/><b>PMS Class: </b>{{$point->pms_class}}</p>
                        @endif
                        @if($propertyMapTest[strtoupper($point->pms_code.'|'.$point->pms_class)] == 0)
                                <p>Property Could Not Be Found in MDP with these codes <x-button sm negative icon="check"></x-button><br/><b>PMS Code: </b>{{$point->pms_code}}<br/><b>PMS Class: </b>{{$point->pms_class}}</p>
                        @endif

                        @if($propertyMapTest[strtoupper($point->pms_code.'|'.$point->pms_class)] == 2)
                                <p>Property Mapping Possibly inverted?? <x-button sm negative icon="check"></x-button><br/><b>PMS Code: </b>{{$point->pms_code}}<br/><b>PMS Class: </b>{{$point->pms_class}}</p>
                        @endif
                    </td>
                    <td style="text-align: center">
                        {{$point->fiskal_invoice_no}} <br/>@if($point->fiskal_invoice_no)  @else <x-button sm  negative icon="check"></x-button> @endif
                    </td>
                    <td style="text-align: center">
                        {{$point->fiskal_establishment}} <br/>@if($point->fiskal_establishment)  @else <x-button sm  negative icon="check"></x-button> @endif
                    </td>
                    <td style="text-align: center">
                        {{$point->fiskal_device}} <br/>@if($point->fiskal_device)  @else <x-button sm  negative icon="check"></x-button> @endif
                    </td>
                </tr>
            @endforeach
        @endif

    </table>

</x-card>
    <br/>
    <!-- Accommodation -->
    <x-card title="Route Package Codes" >

        <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50" title="naziv tablice">
            <thead>
            <tr>
                <th>#PackageID</th>
                <th style="text-align: center">Missing Mapping Activation</th>
            </tr>
            </thead>
            @if($this->route_packages)
                @foreach($this->route_packages as $code)
                    <tr>
                        <td>#{{$code}}</td>
                        <td style="text-align: center">
                            @if($this->getPackagePropertyMapping($code))
                                @foreach($this->getPackagePropertyMapping($code) as $missing)
                                    <small>{{$missing}} <x-button xs negative icon="check"></x-button></small><br/>
                                @endforeach
                            @else
                                <small>Mapped on every destination property <x-button xs positive icon="check"></x-button></small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif

        </table>

    </x-card>
</div>
