<div x-data="app()">

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Lista Kandidata
            <a class="float-right" href="{{ route('dodaj-partnera') }}"><button class="btn btn-sm btn-primary">Dodaj Partnera</button></a>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Traži partnera">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Ime Poslovanja</th>
                    <th>Kontakt Ime</th>
                    <th>Kontakt Broj</th>
                    <th>Email</th>
                    <th>Brisanje</th>
                    <th>Komentari</th>
                    <th class="text-center">Broj poslova</th>
                    <th>Poslovi</th>
                    <th>Uredi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($partners as $partner)


                    <tr>
                        <th>{{ $partner->id }}</th>
                        <td>{{ $partner->business_name }}</td>
                        <td>{{ $partner->contact_name }}</td>
                        <td>{{ $partner->contact_number }}</td>
                        <td >{{ $partner->email }}</td>
                        <td ><button x-on:click="showModal({{$partner->id}})" class="btn btn-sm btn-warning">Izbriši</button></td>
                        <td>
                            <a href="{{ route('komentari',$partner->id) }}"><button class="btn btn-sm btn-primary">Komentari ({{ $partner->comments_count }})</button></a>
                        </td>
                        <td class="text-center">{{ $partner->tasks_count }}</td>
                        <td>
                            <a href="{{ route('poslovi',$partner->id) }}"><button class="btn btn-sm btn-primary">Poslovi</button></a>
                        </td>
                        <td>
                            <a href="{{ route('uredi-partnera',$partner->id) }}"><button class="btn btn-sm btn-primary">Uredi</button></a>
                        </td>


                    </tr>

                @empty
                    <tr>
                        <td colspan="999">
                            <div class="alert alert-warning">
                                <div class="flex-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         class="w-6 h-6 mx-2 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <label>Nema Unesenih Partnera</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>

            {{$partners->links()}}
            <x-ez-modal id="modal" >
                Potvrdi brisanje?
                <hr class="my-4">
                <div class="flex justify-between">
                    <button x-on:click="closeModal()" for="dashboardModal" class="btn btn-sm ">Zatvori</button>
                    <button x-on:click="deleteModel()" for="dashboardModal" class="btn btn-sm ">Potvrdi</button>
                </div>

            </x-ez-modal>
        </x-slot>

    </x-ez-card>



</div>
<script>
    function app(){
        return {
            deleteId:'',
            showModal(id){
                this.deleteId=id
                this.$refs.modal.checked = true
            },
            closeModal(){
                this.$refs.modal.checked = false
            },
            deleteModel(){
                this.$wire.softDelete(this.deleteId)
                this.closeModal()
                this.deleteId=''
            },

        }
    }
</script>
