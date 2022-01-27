<x-app-layout>
    <div x-data="data()">
        <div class="grid grid-cols-1   md:grid-cols-6 gap-4 mb-4  rounded-box"
             @step-change.window="step = $event.detail.step">
            <div class="md:col-span-2 col-span-6">
                <x-bg-text> Add New Service:
                    <x-slot name="description">
                        Complete the wizard steps to import your service
                    </x-slot>
                </x-bg-text>

            </div>
            <div class="md:col-span-4 col-span-6">
                <x-ez-card>
                    <x-slot name="body">
                        <ul class="w-full steps">
                            <li class="step  " x-bind:class=" (step>0) ? 'step-secondary' : ''">Service Information</li>
                            <li class="step " x-bind:class=" (step>1) ? 'step-secondary' : ''">Rate Plans</li>
                            <li class="step " x-bind:class=" (step>2) ? 'step-secondary' : ''">Optional Data</li>
                        </ul>
                    </x-slot>
                </x-ez-card>
            </div>

            <hr class="col-span-6">


        </div>
        <div x-show="step===1" x-transition.duration.600ms>

            @livewire('new-service-wizard-first-step')
        </div>
        <div x-show="step===2" x-transition.duration.600ms>

            @livewire('new-service-wizard-second-step')
        </div>

    </div>


    <script>
        function data() {
            return {
                step:1,
            }
        }
    </script>

</x-app-layout>
