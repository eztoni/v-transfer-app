<x-app-layout>

    <div x-data="loadServices()">

        <div class=" card      bg-base-100">
            <div class="card-body p-4 flex   sm:flex-row justify-between">

                <h2 class="font-bold card-title  m-0 mr-5">Your services</h2>

                <div class="flex-1 max-w-xl flex gap-4">
                    <button @click="tableView=!tableView" class="btn btn-ghost btn-sm rounded-btn">
                        @include('components.icons.table')
                        Change View

                    </button>
                    <input
                        type="search"
                        placeholder="Search"
                        class="input  w-full   input-sm input-bordered"
                        x-ref="searchField"
                        x-model="search"
                        @keyup="filteredServices"
                    >
                </div>
            </div>
        </div>


        <div  x-show="!tableView" x-transition class="mt-8 grid lg:grid-cols-4 md:grid-cols-2  gap-5">

            <template x-for="item in filteredServices" :key="item.id">

                <div class="bg-base-100 card shadow  hover:shadow-xl  transition duration-150 ease-in-out transform  ">
                    <img src="https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg"
                         class="w-full rounded-t-lg h-32 sm:h-48 object-cover">
                    <div class="flex  p-4 justify-between">
                        <div class="">
                            <span class="font-bold" x-text="item.service_name"></span>
                            <span class="block text-gray-500 text-sm">Category: <span
                                    x-text="item.service_category"></span> </span>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="badge">
                                <span>25 Mins</span>
                            </div>

                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div  x-show="tableView" x-transition class="overflow-x-auto">
            <table class="ds-table  w-full">
                <thead>
                <tr>
                    <th class="w-64">Service Img</th>
                    <th>Service Name</th>
                    <th>Service Category</th>
                    <th>Duration</th>
                    <th>Message</th>
                </tr>
                </thead>
                <tbody>
                <template x-for="item in filteredServices" :key="item.id">

                    <tr>
                        <td><img
                                src="https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg"
                                class="w-64 rounded-lg h-32  object-cover"></td>
                        <td> <span
                                x-text="item.service_category"></span></td>
                        <td x-text="item.service_category"></td>
                        <td>
                            <div class="badge">
                                <span>25 Mins</span>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-primary">Edit</button>
                        </td>
                    </tr>
                </template>


                </tbody>

            </table>
        </div>


    </div>

    <script>
        function loadServices() {
            return {
                tableView: false,
                search: "",
                myForData: sourceData,
                get filteredServices() {
                    if (this.search === "") {
                        return this.myForData;
                    }
                    return this.myForData.filter((item) => {
                        return item.service_name
                            .toLowerCase()
                            .includes(this.search.toLowerCase());
                    });
                },
            };
        }

        var sourceData = [
            {
                id: "1",
                service_name: "Boat Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },
            {
                id: "2",
                service_name: "Party Boat Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },
            {
                id: "3",
                service_name: "Brodica Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },
            {
                id: "4",
                service_name: "Jahta Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },
            {
                id: "5",
                service_name: "Gliser Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },
            {
                id: "6",
                service_name: "Jedrilica Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },
            {
                id: "7",
                service_name: "Motocikli Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },
            {
                id: "8",
                service_name: "Boat Tour",
                service_category: "Boat Tour",
                profile_image: "https://admin.freetour.com/images/tours/27915/half-day-boat-tour-to-kleftiko-milos-01.jpg",
            },

        ];
    </script>


</x-app-layout>
