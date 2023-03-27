<div class=" my-4">
    <p class="text-lg"> Issue an invoice for this reservation ( Fiskalizacija )?</p>
    <div class="ds-divider"></div>


    <div class="flex justify-end">
        <x-button label="Yes"  wire:click="issueInvoice"/>

        <x-button label="No"  negative wire:click="close"/>
    </div>


</div>
