<x-guest-layout>


    <div class="min-h-screen grid grid-cols-3 items-center bg-base-200">

        <!-- GRID #1 -->
        <div
            class=" lg:col-span-1 lg:block hidden "
            style="height:100vh; animation: fade-in 0.75s cubic-bezier(0.39,0.575,0.565,1) forwards;
    background-size: cover;
    background-position: bottom;
    background-repeat: no-repeat;
    background-image: url('{{URL('/img/bg_image.jpg')}}');
    background-image: linear-gradient(
180deg,hsla(0,0%,100%,0) 42.52%,#0d3151),"
        >
        </div>

        <!-- GRID #2 -->
        <div class=" lg:col-span-2 col-span-3 h-screen lg:px-20 px-5">
            <!-- Nav bar -->
            <div class="flex sm:justify-between flex-col  items-center sm:flex-row gap-5  py-8 mb-auto">
                <div class="w-40 flex-shrink-0">
                    <img src="https://www.ez-booker.com/wp-content/uploads/2019/06/ez-booker-logo-color.svg">
                </div>
                <p class="pt-3">Already a member?
                    <b><a
                            class=" underline text-primary  hover:text-gray-900"
                            href="{{ route('login') }}">
                            {{ __('Login Now') }}
                        </a></b>
                </p>
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div x-data="register()" x-cloak class="flex flex-col items-center w-full mt-18  md:my-48">

                <h1 class="font-bold text-4xl text-center  my-8">Free trial for <span class="text-primary">EZ Booker</span>
                </h1>


                <form autocomplete="off" method="POST" @input="change" @focusout="change"  class="w-9/12 max-w-screen-sm" action="{{ route('register') }}">
                        @csrf

                        <div x-show="step === 1" x-transition.duration.200ms>
                            <div class="mt-4">
                                <x-jet-label for="name" value="{{ __('Name') }}"/>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" name="name" type="text"  data-server-errors='[]' id="name" x-bind:class="{'invalid':email.errorMessage && email.blurred}" data-server data-rules='["required"]'>
                                <div
                                    x-show="name.errorMessage && name.blurred"
                                    x-text="name.errorMessage"
                                    class="text-red-700 py-2 rounded relative" role="alert">
                                </div>

                            </div>

                            <div class="mt-4">
                                <x-jet-label for="email" value="{{ __('Email') }}"/>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" name="email" type="email"  data-server-errors='[]' id="email" x-bind:class="{'invalid':email.errorMessage && email.blurred}" data-server data-rules='["required","email"]'>
                                <div
                                    x-show="email.errorMessage && email.blurred"
                                    x-text="email.errorMessage"
                                    class="text-red-700 py-2 rounded relative" role="alert">
                                </div>

                            </div>

                            <div class="mt-4">
                                <x-jet-label for="password" value="{{ __('Password') }}"/>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" name="password" type="password" id="password" x-bind:class="{'invalid':password.errorMessage}" data-rules='["required","minimum:8"]' data-server-errors='[]'>
                                <div
                                    x-show="password.errorMessage"
                                    x-text="password.errorMessage"
                                    x-transition:enter
                                    class="text-red-700 py-2 rounded relative" role="alert">
                                </div>

                            </div>

                            <div class="mt-4" x-data>
                                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}"/>
                                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" name="password_confirmation" type="password" id="passwordConf" x-bind:class="{'invalid':passwordConf.errorMessage}" data-rules='["required","minimum:8","matchingPassword"]' data-server-errors='[]'>
                                <div
                                    x-show="passwordConf.errorMessage"
                                    x-text="passwordConf.errorMessage"
                                    x-transition:enter
                                    class="text-red-700 py-2 rounded relative" role="alert">
                                </div>
                            </div>

                            <div class="flex justify-end ">
                                <button @click="next_step(2)" type="button" style="background-color:rgb(0,168,203); border:none;" class="my-2 btn btn-blue btn-sm">Next</button>
                            </div>

                        </div>

                        <!-- STEP TWO -->
                        <div x-show="step === 2" x-transition.duration.200ms>

                            <div class="mt-4">
                                <x-jet-label for="country" value="{{ __('Country') }}"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                    rounded-md shadow-sm block mt-1 w-full"
                                    name="country_code"
                                    type="text"
                                    data-server data-rules='["required"]'
                                >
                                    <template x-for="id in Object.keys(countryList)" :key="id">
                                        <option :value="id" x-text="countryList[id]"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="zip" value="{{ __('Zip') }}"/>
                                <input
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full"
                                    name="zip"
                                    type="text"
                                    data-server-errors='[]'
                                    id="zip"
                                    x-bind:class="{'invalid':zip.errorMessage && zip.blurred}"
                                    data-server data-rules='["required"]'
                                >
                                <div
                                    x-show="zip.errorMessage && zip.blurred"
                                    x-text="zip.errorMessage"
                                    class="text-red-700 py-2 rounded relative" role="alert">
                                </div>

                            </div>

                            <div class="mt-4">
                                <x-jet-label for="city" value="{{ __('City') }}"/>
                                <input
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full"
                                    name="city"
                                    type="text"
                                    data-server-errors='[]'
                                    id="city"
                                    x-bind:class="{'invalid':city.errorMessage && city.blurred}"
                                    data-server data-rules='["required"]'
                                >
                                <div
                                    x-show="zip.errorMessage && zip.blurred"
                                    x-text="zip.errorMessage"
                                    class="text-red-700 py-2 rounded relative" role="alert">
                                </div>

                            </div>


                            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                                <div class="mt-4">
                                    <x-jet-label for="terms">
                                        <div class="flex items-center">
                                            <x-jet-checkbox name="terms" id="terms"/>

                                            <div class="ml-2">
                                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </x-jet-label>
                                </div>
                            @endif

                            <div class="flex items-center justify-end mt-4">

                                <button style="background-color:rgb(0,168,203); border:none;" @click="step = 1" type="button" class="ml-4 my-2 btn btn-blue btn-sm">Back</button>

                                <button style="background-color:rgb(0,168,203); border:none;" @click="submit" class="ml-4 my-2 btn btn-blue btn-sm">    {{ __('Register') }}</button>

                            </div>
                        </div>
                    </form>

            </div>


        </div>

    </div>
</x-guest-layout>
<script>
    function register() {
        return {
            step:1,
            countryList: {
                "AF": "Afghanistan",
                "AL": "Albania",
                "DZ": "Algeria",
                "AS": "American Samoa",
                "AD": "Andorra",
                "AO": "Angola",
                "AI": "Anguilla",
                "AQ": "Antarctica",
                "AG": "Antigua and Barbuda",
                "AR": "Argentina",
                "AM": "Armenia",
                "AW": "Aruba",
                "AU": "Australia",
                "AT": "Austria",
                "AZ": "Azerbaijan",
                "BS": "Bahamas (the)",
                "BH": "Bahrain",
                "BD": "Bangladesh",
                "BB": "Barbados",
                "BY": "Belarus",
                "BE": "Belgium",
                "BZ": "Belize",
                "BJ": "Benin",
                "BM": "Bermuda",
                "BT": "Bhutan",
                "BO": "Bolivia (Plurinational State of)",
                "BQ": "Bonaire, Sint Eustatius and Saba",
                "BA": "Bosnia and Herzegovina",
                "BW": "Botswana",
                "BV": "Bouvet Island",
                "BR": "Brazil",
                "IO": "British Indian Ocean Territory (the)",
                "BN": "Brunei Darussalam",
                "BG": "Bulgaria",
                "BF": "Burkina Faso",
                "BI": "Burundi",
                "CV": "Cabo Verde",
                "KH": "Cambodia",
                "CM": "Cameroon",
                "CA": "Canada",
                "KY": "Cayman Islands (the)",
                "CF": "Central African Republic (the)",
                "TD": "Chad",
                "CL": "Chile",
                "CN": "China",
                "CX": "Christmas Island",
                "CC": "Cocos (Keeling) Islands (the)",
                "CO": "Colombia",
                "KM": "Comoros (the)",
                "CD": "Congo (the Democratic Republic of the)",
                "CG": "Congo (the)",
                "CK": "Cook Islands (the)",
                "CR": "Costa Rica",
                "HR": "Croatia",
                "CU": "Cuba",
                "CW": "Curaçao",
                "CY": "Cyprus",
                "CZ": "Czechia",
                "CI": "Côte d'Ivoire",
                "DK": "Denmark",
                "DJ": "Djibouti",
                "DM": "Dominica",
                "DO": "Dominican Republic (the)",
                "EC": "Ecuador",
                "EG": "Egypt",
                "SV": "El Salvador",
                "GQ": "Equatorial Guinea",
                "ER": "Eritrea",
                "EE": "Estonia",
                "SZ": "Eswatini",
                "ET": "Ethiopia",
                "FK": "Falkland Islands (the) [Malvinas]",
                "FO": "Faroe Islands (the)",
                "FJ": "Fiji",
                "FI": "Finland",
                "FR": "France",
                "GF": "French Guiana",
                "PF": "French Polynesia",
                "TF": "French Southern Territories (the)",
                "GA": "Gabon",
                "GM": "Gambia (the)",
                "GE": "Georgia",
                "DE": "Germany",
                "GH": "Ghana",
                "GI": "Gibraltar",
                "GR": "Greece",
                "GL": "Greenland",
                "GD": "Grenada",
                "GP": "Guadeloupe",
                "GU": "Guam",
                "GT": "Guatemala",
                "GG": "Guernsey",
                "GN": "Guinea",
                "GW": "Guinea-Bissau",
                "GY": "Guyana",
                "HT": "Haiti",
                "HM": "Heard Island and McDonald Islands",
                "VA": "Holy See (the)",
                "HN": "Honduras",
                "HK": "Hong Kong",
                "HU": "Hungary",
                "IS": "Iceland",
                "IN": "India",
                "ID": "Indonesia",
                "IR": "Iran (Islamic Republic of)",
                "IQ": "Iraq",
                "IE": "Ireland",
                "IM": "Isle of Man",
                "IL": "Israel",
                "IT": "Italy",
                "JM": "Jamaica",
                "JP": "Japan",
                "JE": "Jersey",
                "JO": "Jordan",
                "KZ": "Kazakhstan",
                "KE": "Kenya",
                "KI": "Kiribati",
                "KP": "Korea (the Democratic People's Republic of)",
                "KR": "Korea (the Republic of)",
                "KW": "Kuwait",
                "KG": "Kyrgyzstan",
                "LA": "Lao People's Democratic Republic (the)",
                "LV": "Latvia",
                "LB": "Lebanon",
                "LS": "Lesotho",
                "LR": "Liberia",
                "LY": "Libya",
                "LI": "Liechtenstein",
                "LT": "Lithuania",
                "LU": "Luxembourg",
                "MO": "Macao",
                "MG": "Madagascar",
                "MW": "Malawi",
                "MY": "Malaysia",
                "MV": "Maldives",
                "ML": "Mali",
                "MT": "Malta",
                "MH": "Marshall Islands (the)",
                "MQ": "Martinique",
                "MR": "Mauritania",
                "MU": "Mauritius",
                "YT": "Mayotte",
                "MX": "Mexico",
                "FM": "Micronesia (Federated States of)",
                "MD": "Moldova (the Republic of)",
                "MC": "Monaco",
                "MN": "Mongolia",
                "ME": "Montenegro",
                "MS": "Montserrat",
                "MA": "Morocco",
                "MZ": "Mozambique",
                "MM": "Myanmar",
                "NA": "Namibia",
                "NR": "Nauru",
                "NP": "Nepal",
                "NL": "Netherlands (the)",
                "NC": "New Caledonia",
                "NZ": "New Zealand",
                "NI": "Nicaragua",
                "NE": "Niger (the)",
                "NG": "Nigeria",
                "NU": "Niue",
                "NF": "Norfolk Island",
                "MP": "Northern Mariana Islands (the)",
                "NO": "Norway",
                "OM": "Oman",
                "PK": "Pakistan",
                "PW": "Palau",
                "PS": "Palestine, State of",
                "PA": "Panama",
                "PG": "Papua New Guinea",
                "PY": "Paraguay",
                "PE": "Peru",
                "PH": "Philippines (the)",
                "PN": "Pitcairn",
                "PL": "Poland",
                "PT": "Portugal",
                "PR": "Puerto Rico",
                "QA": "Qatar",
                "MK": "Republic of North Macedonia",
                "RO": "Romania",
                "RU": "Russian Federation (the)",
                "RW": "Rwanda",
                "RE": "Réunion",
                "BL": "Saint Barthélemy",
                "SH": "Saint Helena, Ascension and Tristan da Cunha",
                "KN": "Saint Kitts and Nevis",
                "LC": "Saint Lucia",
                "MF": "Saint Martin (French part)",
                "PM": "Saint Pierre and Miquelon",
                "VC": "Saint Vincent and the Grenadines",
                "WS": "Samoa",
                "SM": "San Marino",
                "ST": "Sao Tome and Principe",
                "SA": "Saudi Arabia",
                "SN": "Senegal",
                "RS": "Serbia",
                "SC": "Seychelles",
                "SL": "Sierra Leone",
                "SG": "Singapore",
                "SX": "Sint Maarten (Dutch part)",
                "SK": "Slovakia",
                "SI": "Slovenia",
                "SB": "Solomon Islands",
                "SO": "Somalia",
                "ZA": "South Africa",
                "GS": "South Georgia and the South Sandwich Islands",
                "SS": "South Sudan",
                "ES": "Spain",
                "LK": "Sri Lanka",
                "SD": "Sudan (the)",
                "SR": "Suriname",
                "SJ": "Svalbard and Jan Mayen",
                "SE": "Sweden",
                "CH": "Switzerland",
                "SY": "Syrian Arab Republic",
                "TW": "Taiwan",
                "TJ": "Tajikistan",
                "TZ": "Tanzania, United Republic of",
                "TH": "Thailand",
                "TL": "Timor-Leste",
                "TG": "Togo",
                "TK": "Tokelau",
                "TO": "Tonga",
                "TT": "Trinidad and Tobago",
                "TN": "Tunisia",
                "TR": "Turkey",
                "TM": "Turkmenistan",
                "TC": "Turks and Caicos Islands (the)",
                "TV": "Tuvalu",
                "UG": "Uganda",
                "UA": "Ukraine",
                "AE": "United Arab Emirates (the)",
                "GB": "United Kingdom of Great Britain and Northern Ireland (the)",
                "UM": "United States Minor Outlying Islands (the)",
                "US": "United States of America (the)",
                "UY": "Uruguay",
                "UZ": "Uzbekistan",
                "VU": "Vanuatu",
                "VE": "Venezuela (Bolivarian Republic of)",
                "VN": "Viet Nam",
                "VG": "Virgin Islands (British)",
                "VI": "Virgin Islands (U.S.)",
                "WF": "Wallis and Futuna",
                "EH": "Western Sahara",
                "YE": "Yemen",
                "ZM": "Zambia",
                "ZW": "Zimbabwe",
                "AX": "Åland Islands"
            },
            firstPageElements : [],
            inputElements: [],
            next_step: function(step_index){

                this.firstPageElements = [];
                const invalidElements = this.inputElements.filter((input) => {
                    var input_name = input.name;
                    var input_bool = window.Iodine.is(input.value, JSON.parse(input.dataset.rules)) !== true;

                    if(input_name != 'zip' && input_name != 'city'){
                        if(input_bool == true){
                            this.firstPageElements.push(input_name)
                        }
                    }
                    return window.Iodine.is(input.value, JSON.parse(input.dataset.rules)) !== true;
                });




                if (this.firstPageElements.length > 0) {

                    //We set all the inputs as blurred if the form has been submitted
                    this.inputElements.map((input) => {
                        this[input.name].blurred = true;
                    });
                    //And update the error messages.
                    this.updateErrorMessages();
                    this.step = 1;

                }else{
                    this.step = step_index;
                }

            },
            init() {
                //Set up custom window.Iodine rules
                window.Iodine.addRule(
                    "matchingPassword",
                    (value) => value === document.getElementById("password").value
                );
                window.Iodine.messages.matchingPassword =
                    "Password confirmation needs to match password";
                //Store an array of all the input elements with 'data-rules' attributes
                this.inputElements = [...this.$el.querySelectorAll("input[data-rules]")];
                this.initDomData();
                this.updateErrorMessages();
            },
            initDomData: function () {
                //Create an object attached to the component state for each input element to store its state
                this.inputElements.map((ele) => {
                    this[ele.name] = {
                        serverErrors: JSON.parse(ele.dataset.serverErrors),
                        blurred: false
                    };
                });
            },
            updateErrorMessages: function () {
                //map throught the input elements and set the 'errorMessage'
                this.inputElements.map((ele) => {
                    this[ele.name].errorMessage = this.getErrorMessage(ele);
                });
            },
            getErrorMessage: function (ele) {
                //Return any server errors if they're present
                if (this[ele.name].serverErrors.length > 0) {
                    return input.serverErrors[0];
                }
                //Check using window.Iodine and return the error message only if the element has not been blurred
                const error = window.Iodine.is(ele.value, JSON.parse(ele.dataset.rules));
                if (error !== true && this[ele.name].blurred) {
                    return window.Iodine.getErrorMessage(error);
                }
                //return empty string if there are no errors
                return "";
            },
            submit: function (event) {
                const invalidElements = this.inputElements.filter((input) => {
                    return window.Iodine.is(input.value, JSON.parse(input.dataset.rules)) !== true;
                });
                if (invalidElements.length > 0) {
                    event.preventDefault();
                    document.getElementById(invalidElements[0].id).scrollIntoView();
                    //We set all the inputs as blurred if the form has been submitted
                    this.inputElements.map((input) => {
                        this[input.name].blurred = true;
                    });
                    //And update the error messages.
                    this.updateErrorMessages();
                }
            },
            change: function (event) {
                //Ignore all events that aren't coming from the inputs we're watching
                if (!this[event.target.name]) {
                    return false;
                }
                if (event.type === "input") {
                    this[event.target.name].serverErrors = [];
                }
                if (event.type === "focusout") {
                    this[event.target.name].blurred = true;
                }
                //Whether blurred or on input, we update the error messages
                this.updateErrorMessages();
            }
        };

    }
</script>
