@props(['name', 'val' => 'none', 'exclusive' => 'true'])
<button type="button" x-data="{
        spotsort: '{{$val ?? 'none'}}',
        options: ['none', 'asc', 'desc'],
        exclusive: {{$exclusive}},
        processClick() {
            console.log('sp: '+this.spotsort);
            for(let i = 0; i < this.options.length; i++) {
                if(this.options[i] == this.spotsort) {
                    this.spotsort = (i+1) < this.options.length ? this.options[i+1] : this.options[0];
                    console.log('i='+i);
                    break;
                }
            }
            console.log('sp: '+this.spotsort);
            $dispatch('spotsort', {exclusive: this.exclusive, data: { {{$name}}: this.spotsort}});
            console.log('sp: dispatched');
        },
        reset(sorts) {
            if(!Object.keys(sorts).includes('{{$name}}')){
                this.spotsort = 'none';
                console.log('resetting: {{$name}}');
            } else {
                console.log('NOT resetting: {{$name}}');
            }
        }
    }"
    x-init="
        {{-- console.log('spot sort init');
        if(!options.includes(spotsort)) {
            spotsort = options['0'];
        }
        $dispatch('setsort', {exclusive: exclusive, data: { {{$name}}: spotsort}}); --}}
    "
    @click.prevent.stop="processClick"
    @clearsorts.window="reset($event.detail.sorts);"
    :class="spotsort=='none' || 'text-accent'"
    >
    <x-display.icon icon="icons.up-down" x-show="spotsort=='none'"/>
    <x-display.icon icon="icons.sort-up" x-show="spotsort=='asc'"/>
    <x-display.icon icon="icons.sort-down" x-show="spotsort=='desc'"/>
</button>