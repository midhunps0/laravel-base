@props(['textval'=> '', 'textname', 'label'])

<div x-data="{
    textval: '',
    visible: false,
    }"
    x-init="$nextTick(() => {
        textval = '{{$textval}}';
        visible = textval != '';
        $dispatch('setparam', { {{$textname}}: textval});
    });"
    class="form-control w-full h-full flex flex-row justify-end items-center absolute top-0 left-0">
    <div x-show="visible" class="absolute z-10 w-full flex flex-row">
        <input type="text" x-ref="search_box" x-model="textval"
        @change="if (textval.length > 0) {$dispatch('spotsearch', { {{$textname}}: textval});}"
        @keyup="if($event.code == 'Escape') {
            textval='';
            visible=false; $dispatch('spotsearch', { {{$textname}}: textval});  
        }"
        @click.outside="visible=textval.length > 0;"
        placeholder="{{$label}}" class="input input-sm input-bordered text-accent flex-grow"/>
        <button
        @click.prevent.stop="
        if (textval=='') {
            visible=false;
        } else {
            textval='';
            visible=false; $dispatch('spotsearch', { {{$textname}}: textval});
        }"
        class="btn btn-sm border-none"><x-display.icon icon="icons.close" height="h-6" width="w-6"/></button>
    </div>
    <button class="btn btn-sm bg-inherit border-none focus:outline-primary-focus text-base-content hover:bg-base-100" @click.prevent.stop="visible=true; $nextTick(() => $refs.search_box.focus());">
        <x-display.icon icon="icons.search" height="h-6" width="w-6"/>
    </button>
</div>