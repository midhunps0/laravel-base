@props(['xmodel_name', 'itemsName', 'searchUrl', 'searchDisplayKeys', 'valueKey'])

<div x-data="{
        list: [],
        id: 0,
        @if (!isset($xmodel_name))
            search: '',
        @endif
        searchUrl: '{{$searchUrl}}',
        getItemsList() {
            axios.get(this.searchUrl, {params: {search: this.{{$xmodel_name ?? 'search'}}} }).then((r) => {
                this.list = r.data.data.{{$itemsName}};
            });
        }
    }"
    x-init="
        list = [];
    "
    {{-- @submit.prevent.stop="getItemsList" --}}
    action="" {{$attributes->merge(['class' => 'flex flex-row justify-end items-center p-0 m-0'])}}>
    <div class="form-control w-full relative">
        {{-- <label class="label">
          <span class="label-text">Code/Name</span>
        </label> --}}
        <input x-model="{{$xmodel_name ?? 'search'}}"
            type="text"
            class="input input-bordered w-full max-w-md"
            @input.deboune="if ({{$xmodel_name ?? 'search'}}.length > 2) { getItemsList(); }"
            @keyup="if($event.code=='Escape') {
                {{$xmodel_name ?? 'search'}} = '';
                list = [];
            }"
             />
        <ul x-show="list.length > 0"
            @click.outside="list=[];"
            class="absolute top-10 left-0 z-50 flex flex-col bg-base-200 p-2 rounded-sm shadow-sm">
            <template x-for="item in list">
                <a href="#"
                    @click.prevent.stop="
                    id = item.id;
                    {{$xmodel_name ?? 'search'}} = item.{{$valueKey}};
                    list = [];
                    "
                    @keyup="if($event.code=='Escape') {
                        {{$xmodel_name ?? 'search'}}='';
                        list=[];
                    }" class="btn-link text-left border-b border-base-300 py-1 text-base-content hover:text-warning hover:no-underline focus:text-warning">
                    @foreach ($searchDisplayKeys as $key)
                        <span x-text="item.{{$key}}"></span>@if(!$loop->last),@endif
                    @endforeach

                </a>
            </template>
        </ul>
    </div>
    {{-- <button class="btn btn-sm" type="submit">Search</button> --}}
</div>
