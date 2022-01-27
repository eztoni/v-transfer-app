<ul class="mfb-component--br mfb-slidein" data-mfb-toggle="hover">
    <li class="mfb-component__wrap">
        <!-- the main menu button -->
        <a  class="bg-primary mfb-component__button--main ">
            <!-- the main button icon visibile by default -->
            <i class="mfb-component__main-icon--resting">
                @include('components.icons.plus')

            </i>
            <!-- the main button icon visibile when the user is hovering/interacting with the menu -->
            <i class="mfb-component__main-icon--active ">
                @include('components.icons.x')
            </i>
        </a>
        <ul class="mfb-component__list">
            <!-- a child button, repeat as many times as needed -->
            <li>
                <a href="/service-wizard" data-mfb-label="New service" class="mfb-component__button--child">
                    <i class="mfb-component__child-icon ">@include('components.icons.map')</i>
                </a>
            </li>
        </ul>
    </li>
</ul>
