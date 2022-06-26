@props(['name', 'val' => 'none', 'exclusive' => 'true'])
<button x-data="{
        spotsort: '{{$val ?? 'none'}}',
        options: ['none', 'asc', 'desc'],
        exclusive: {{$exclusive}},
        processClick() {
            for(let i = 0; i < this.options.length; i++) {
                if(this.options[i] == this.spotsort) {
                    this.spotsort = (i+1) < this.options.length ? this.options[i+1] : this.options[0];
                    console.log('i='+i);
                    break;
                }
            }
            $dispatch('spotsort', {exclusive: this.exclusive, data: { {{$name}}: this.spotsort}});
        }
    }"
    x-init="console.log('spot sort init');
        if(!options.includes(spotsort)) {
            spotsort = options['0'];
        }
        $dispatch('setsort', {exclusive: exclusive, data: { {{$name}}: spotsort}});
    "
    @click.prevent.stop="processClick"
    :class="spotsort=='none' || 'text-accent'"
    >
    <x-display.icon icon="icons.up-down" x-show="spotsort=='none'"/>
    <x-display.icon icon="icons.sort-up" x-show="spotsort=='asc'"/>
    <x-display.icon icon="icons.sort-down" x-show="spotsort=='desc'"/>
</button>