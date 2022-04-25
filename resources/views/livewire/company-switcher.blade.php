<div>
    <div  class="dropdown dropdown-end">
        <div tabindex="0" class="btn btn-ghost btn-sm rounded-btn flex items-center font-thin">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
            </svg>
            <span class="hidden md:inline ">
          {{$userCompanyName}}
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
                @foreach($companies as $company)
                    <li><a tabindex="0"
                           data-set-theme="valamar"
                           wire:click="changeCompany({{$company->id}})"
                           @class(['active'=>$company->id == Auth::user()->company_id])
                            >

                            #{{$company->id}} - {{$company->name}}

                        </a>
                    </li>
                @endforeach


            </ul>
        </div>
    </div>



</div>
