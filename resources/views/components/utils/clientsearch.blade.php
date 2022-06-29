<form x-data="{
    id: 0,
    search: '',
    url: '{{route('clients.show', 0)}}',
    searchUrl: '{{route('clients.list')}}',
    list: [],
    getClientsList() {
        if (this.search.length > 2) {
            axios.get(this.searchUrl, {params: {search: this.search}}).then((r) => {
                this.list = r.data.data.clients;
                console.log(this.list);
                console.log('ln: '+this.list.length);
            });
        }
    }
}"
    @submit.prevent.stop="getClientsList"
    action="" class="flex flex-row items-end space-x-4">
    <div class="form-control w-52 relative">
        {{-- <label class="label">
          <span class="label-text">Code/Name</span>
        </label> --}}
        <input x-model="search"
            @input.deboune="getClientsList" type="text" placeholder="Code/Name" class="input input-bordered input-sm w-full max-w-xs" />
        <ul x-show="list.length > 0" class="absolute top-10 left-0 z-10 flex flex-col bg-base-200 p-2 rounded-sm shadow-sm">
            <template x-for="client in list">
                <button @click="id = client.id;
                    $dispatch('linkaction', { link: url.replace('0', id), route: 'clients.show'}); list=[];" class="btn-link text-left border-b border-base-300 py-1 text-base-content hover:text-warning hover:no-underline focus:text-warning" :key="client.code">
                    <span x-text="client.code"></span>, <span x-text="client.name"></span>
                </button>
            </template>
        </ul>
    </div>
    <button class="btn btn-sm">Search</button>
</form>