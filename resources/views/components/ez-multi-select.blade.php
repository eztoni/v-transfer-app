@props(['itemsKeyValue' => [],'wrap'=>false,'name'=>'ez-multi-select'])


@php


    #IF TRUE BADGES WILL WRAP
    $wrapClasses= ' h-12';
    if($wrap){
        $wrapClasses= ' min-h-12 flex-wrap py-2';
    }
@endphp

<div class="dropdown w-full" x-data="initData()">
    <div @click="openSelect()" @click.away="clickAway($event)" x-ref="display"
        {{$attributes->merge(['class'=>'border border-base-300 rounded-lg  flex  overflow-y-hidden items-center p-4 cursor-pointer ez-multi-select'.$wrapClasses])}}
    >
        <template x-if="showSlot">
            {{$slot}}
        </template>

        <template x-for="id in Object.keys(addedItems)" :key="id">
            <input type="hidden" :value="id" :name="'govno['+id+']'">
        </template>

        <template x-for="id in Object.keys(addedItems)" :key="id">
            <div class="ds-badge mb-1 pr-0 z-50 flex-shrink-0 ds-badge-primary mr-1 py-3">
                <span x-text="addedItems[id]"></span>
                <button class="btn-outline btn-circle btn-xs ml-1 text-base-100" @click="removeItem(id)">X</button>
            </div>

        </template>
    </div>
    <div tabindex="0" x-ref="open-select" class=""></div>
    <ul tabindex="0" class="p-2 shadow menu dropdown-content bg-base-100 rounded-box  compact max-h-72 overflow-y-auto">

        <button @click="addAll()" class="btn btn-xs btn-accent"
                x-text="!all.clicked ? 'Select All':'Unselect all' "></button>
        <div class="divider"></div>

        <template x-for="id in Object.keys(items)" :key="id">
            <li>
                <a @click="addItem(id)" class="py-0 my-1 ez-menu-item"
                   :class="isItemAdded[id]? 'border-2 border-primary shadow-sm ' : ''" x-text="items[id]"></a>
            </li>
        </template>
    </ul>
</div>
<script>
    function initData() {
        return {
            items: @php echo json_encode($itemsKeyValue) @endphp,
            addedItems: {},
            isItemAdded: {},
            name: '{{$name}}',
            get showSlot() {
                return _.isEmpty(this.addedItems)
            },
            all: {
                clicked: false,
            },

            addItem(key) {
                if (_.has(this.addedItems, key)) {
                    _.unset(this.addedItems, [key])
                    _.unset(this.isItemAdded, [key])
                } else {
                    this.addedItems[key] = this.items[key]
                    this.isItemAdded[key] = true
                }
            },
            removeItem(key) {
                _.unset(this.addedItems, [key])
                _.unset(this.isItemAdded, [key])
            },
            addAll() {

                if (!this.all.clicked) {
                    this.addedItems = {...this.items}
                    for (var k in this.items) this.isItemAdded[k] = true;
                    this.all.clicked = true;
                } else {
                    this.addedItems = {}
                    this.isItemAdded = {}
                    this.all.clicked = false;
                }

            },
            openSelect() {
                if (!_.isEmpty(this.items)) {
                    this.$refs['display'].className += " ez-outline"
                    this.$refs['open-select'].focus()
                }
            },
            clickAway(event) {
                if (event.target.className.indexOf("ez-menu-item") === -1 && event.target.className.indexOf("menu") === -1) {
                    this.$refs.display.classList.remove('ez-outline')
                }
            },

        }
    }
</script>

