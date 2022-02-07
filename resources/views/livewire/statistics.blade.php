<div>
    <x-ez-card class="mb-4 " x-data="app()">
        <x-slot name="body" >
            <div class="flex gap-4 ">

                <select @change="changeYear($el.value)"
                        class="select select-primary flex-grow">
                    @php
                        $now = Carbon\Carbon::today();
                        $currentYear =  $now->format('Y');
                        $now->subYears(2);
                          for($i=0; $i<5; $i++){
                              $selected='';
                              if($currentYear == $now->format('Y')){
                                  $selected = 'selected';
                              }
                              echo '<option '.$selected.' value="'.$now->format('Y').'">'.$now->format('Y').'</option>';
                              $now->addYear();
                          }
                    @endphp
                </select>
            </div>
        </x-slot>
    </x-ez-card>
    <x-ez-card class="mb-4">
        <x-slot name="body" class="p-1">
            <canvas id="myChart"></canvas>
        </x-slot>
    </x-ez-card>
</div>

@include('packages.chartjs')
<script>
    function app(){
        return {
            chart: {},
            data: {},
            init(){
                this.$wire.buildDataSet()
                    .then(result => {
                        this.data.datasets = result
                        this.buildChart()
                    })
            },

            changeYear(year){
                this.$wire.buildDataSet(year)
                    .then(result => {
                        this.chart.destroy()
                        this.data.datasets = result
                        this.buildChart()
                    })
            },

            buildChart(){
                this.data.labels = [
                    'Siječanj',
                    'Veljača',
                    'Ožujak',
                    'Travanj',
                    'Svibanj',
                    'Lipanj',
                    'Srpanj',
                    'Kolovoz',
                    'Rujan',
                    'Listopad',
                    'Studeni',
                    'Prosinac',
                ];

                var graphAspectRatio;

                if (window.innerWidth < 600) {
                    graphAspectRatio = 1;
                }
                else {
                     graphAspectRatio = 2;

                }
                const config = {
                    type: 'bar',
                    data: this.data,
                    options: {
                        aspectRatio:graphAspectRatio,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Odrađeni poslovi'
                            },
                        },
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true,
                            },
                            y: {
                                stacked: true
                            }
                        }
                    }
                };
                this.chart = new Chart(
                    document.getElementById('myChart'),
                    config
                );
            }
        }
    }


</script>
