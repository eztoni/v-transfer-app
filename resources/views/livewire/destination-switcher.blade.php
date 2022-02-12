<div>
    <div  class="dropdown dropdown-end">
        <div tabindex="0" class="btn btn-ghost btn-sm rounded-btn flex items-center font-thin">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="hidden md:inline ">
          {{$userDestinationName}}
        </span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1792 1792"
                 class="inline-block w-4 h-4 ml-1 fill-current">
                <path
                    d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z"></path>
            </svg>
        </div>
        <div
            class="mt-16 overflow-y-auto shadow-2xl top-px dropdown-content h-72 w-52 rounded-b-box bg-base-200 text-base-content">
            <ul class="p-4 menu compact">
                @foreach($destinations as $destination)
                    <li><a tabindex="0"
                           data-set-theme="valamar"
                           wire:click="changeDestination({{$destination->id}})"
                            @class(['active'=>$destination->id == Auth::user()->destination_id])
                        >

                            #{{$destination->id}} - {{$destination->name}}

                        </a>
                    </li>
                @endforeach


            </ul>
        </div>
    </div>



</div>
