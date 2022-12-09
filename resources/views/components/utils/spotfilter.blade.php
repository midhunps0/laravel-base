@props(['options'=> [], 'name', 'selectedoption' => ''])

<div x-data="{
    visible: true,
    }"
    x-init="
    {{-- $nextTick(() => {
        textval = '{{$textval}}';
        visible = textval != '';
        $dispatch('setparam', { {{$textname}}: textval});
    }); --}}
    "
    class="form-control w-full max-w-full flex flex-row justify-end items-center flex-grow flex-shrink">
    <div x-show="visible" class="z-10 w-full max-w-full flex flex-row">
        <select x-data="{
            val: ''
            }"
            x-init="
                val= '{{ $selectedoption }}';
                $dispatch('setfilter', { data: { {{$name}}: val}});
            "
            @change.stop.prevent="$dispatch('spotfilter', {data: { {{$name}}: val}});"
            x-model="val" class="select select-bordered select-sm max-w-xs py-0 m-1"
            :class="val == -1 || 'text-accent'">
            <option value="All">All Categories</option>
            @foreach ($options as $cat)
                <option value="{{ $cat }}">
                    {{ $cat }}
                </option>
            @endforeach
        </select>
    </div>
    {{-- <button class="btn btn-sm bg-inherit border-none focus:outline-primary-focus text-base-content hover:bg-base-100" @click.prevent.stop="visible=true;">
        <x-display.icon icon="icons.search" height="h-6" width="w-6"/>
    </button> --}}
</div>
