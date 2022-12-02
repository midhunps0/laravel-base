<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{ compact: $persist(false) }" class="p-3 overflow-x-scroll">

        <div class="flex flex-row justify-between items-center mb-4">
            <h3 class="text-xl font-bold"><span>Users</span><button @click.prevent.stop="$dispatch('linkaction', {link: '{{route('users.create')}}', route: 'users.create'});" class="ml-4 btn btn-sm btn-primary">Add&nbsp;<x-display.icon icon="icons.plus" width="h-4" height="w-4"/></button></h3>
            <div class="flex-grow flex flex-row justify-end items-center space-x-4">
                <x-utils.itemscount items_count="{{ $items_count }}" />
                <div>
                    <input x-model="compact" type="checkbox" id="compact" class="checkbox checkbox-xs checkbox-primary">
                    <label for="compact">Compact View</label>
                </div>
                {{-- <div x-data="{dropopen: false, url_all: '', url_selected: ''}"
                    @downloadurl.window="url_all = $event.detail.url_all; url_selected=$event.detail.url_selected;"
                    @click.outside="dropopen = false;"
                    class="relative">
                    <label @click="dropopen = !dropopen;" tabindex="0" class="btn btn-xs m-1">Export&nbsp;
                        <x-display.icon icon="icons.down" />
                    </label>
                    <ul tabindex="0"
                        class="absolute top-5 right-0 z-50 p-2 shadow-md bg-base-200 rounded-md w-52 scale-90 origin-top-right transition-all duration-100 opacity-0"
                        :class="!dropopen || 'top-8 scale-110 opacity-100'">
                        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_selected" download>Download Selected</a></li>
                        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_all" download>Download All</a></li>
                    </ul>
                </div> --}}
                {{-- <a href="#" role="button" class="btn btn-xs">Add&nbsp;
                    <x-display.icon icon="icons.plus" />
                </a> --}}
            </div>
        </div>

        <div class="overflow-x-scroll scroll-m-1 rounded-md">
            <form x-data="{
                url: '{{ route('users.index') }}',
                params: {},
                {{-- search: {}, --}}
                sort: {},
                filters: {},
                itemsCount: {{ $items_count }},
                itemIds: [{{$items_ids}}],
                selectedIds: $persist([]).as('uxids'),
                pageSelected: false,
                allSelected: false,
                totalResults: {{$total_results}},
                currentPage: {{$current_page}},
                downloadUrl: '{{route('users.download')}}',
                deleteUrl: '',

                processParams(params) {
                    let processed = [];
                    let paramkeys = Object.keys(params);
                    paramkeys.forEach((k) => {
                        processed.push(k + '::' + params[k]);
                    });
                    return processed;
                },
                paramsExceptSelection() {
                    let params = {};

                    if (Object.keys(this.params).length > 0) {
                        params.search = this.processParams(this.params);
                    }
                    if (Object.keys(this.sort).length > 0) {
                        params.sort = this.processParams(this.sort);
                    }
                    if (Object.keys(this.filters).length > 0) {
                        params.filter = this.processParams(this.filters);
                    }
                    params.items_count = this.itemsCount;
                    params.page = this.currentPage;

                    return params;
                },
                triggerFetch() {
                    let allParams = this.paramsExceptSelection();
                    //allParams.selected_ids = this.selectedIds.join('|');
                    this.selectedIds = [];

                    $dispatch('linkaction', { link: this.url, params: allParams, route: 'users.index'});
                },

                //triggerFetchWithoutSelection() {
                //    let allParams = this.paramsExceptSelection();
                //    console.log(allParams);
                //    $dispatch('linkaction', { link: this.url, params: allParams });
                //},
                fetchResults(param) {
                    this.setParam(param);
                    this.triggerFetch();
                },
                setParam(param) {
                    let keys = Object.keys(param);
                    if (param[keys[0]].length > 0) {
                        this.params[keys[0]] = param[keys[0]];
                    } else {
                        delete this.params[keys[0]];
                    }
                },
                doSort(detail) {
                    this.setSort(detail);
                    this.triggerFetch();
                },
                setSort(detail) {
                    let keys = Object.keys(detail.data);
                    if (detail.exclusive) { this.sort = {}; }
                    if (detail.data[keys[0]] != 'none') {
                        this.sort[keys[0]] = detail.data[keys[0]];
                    } else {
                        if (typeof(this.sort[keys[0]]) != 'undefined') {
                            delete this.sort[keys[0]];
                        }
                    }
                },
                setFilter(detail) {
                    let keys = Object.keys(detail.data);
                    if (detail.data[keys[0]] != 0) {
                        this.filters[keys[0]] = detail.data[keys[0]];
                    } else {
                        if (typeof(this.filters[keys[0]]) != 'undefined') {
                            delete this.filters[keys[0]];
                        }
                    }
                },
                doFilter(detail) {
                    this.setFilter(detail);
                    this.triggerFetch();
                },
                pageUpdateCount(count) {
                    this.itemsCount = count;
                    this.triggerFetch();
                },
                processPageSelect() {
                    if (this.pageSelected) {
                        this.selectPage();
                    } else {
                        this.selectedIds = [];
                    }
                },
                selectPage() {
                    this.selectedIds = this.itemIds;
                    this.pageSelected = true;
                },
                selectAll() {
                    let params = this.paramsExceptSelection();
                    ajaxLoading = true;
                    axios.get('{{route('users.selectIds')}}', {params: params} ).then(
                        (r) => {
                            this.itemIds = r.data.ids;
                            this.selectedIds = r.data.ids;
                            this.pageSelected = true;
                            this.allSelected = true;
                            ajaxLoading = false;
                        }
                    ).catch(
                        function (e) {
                            console.log(e);
                        }
                    );
                },
                cancelSelection() {
                    this.selectedIds = [];
                    this.pageSelected = false;
                    this.asllSelected = false;
                },
                setDownloadUrl() {
                    let allParams = this.paramsExceptSelection();
                    let url_all = getQueryString(allParams);

                    if (this.selectedIds.length > 0) {
                        allParams.selected_ids = this.selectedIds.join('|');
                    }
                    let url_selected = getQueryString(allParams);

                    $dispatch('downloadurl', {url_all: this.downloadUrl+'?'+url_all, url_selected: this.downloadUrl+'?'+url_selected});
                },
                deleteItem(id) {
                    axios.post(
                        this.deleteUrl.replace('0', id),
                        {
                            _method: 'DELETE'
                        }
                    ).then((r) => {
                        $dispatch('deletedone', {success: r.data.success, message: r.data.message});
                    })
                    .catch((e) => { console.log(e)});
                }
            }" @spotsearch.window="fetchResults($event.detail)"
                @setparam.window="setParam($event.detail)"
                @spotsort.window="doSort($event.detail)"
                @setsort.window="setSort($event.detail)"
                @spotfilter.window="doFilter($event.detail);"
                @setfilter.window="setFilter($event.detail)"
                @countchange.window="pageUpdateCount($event.detail.count)"
                @selectpage="selectPage()"
                @selectall="selectAll()"
                @cancelselection="cancelSelection()"
                @pageselect="processPageSelect()"
                x-init="
                $watch('selectedIds', (ids) => {
                    if (ids.length < itemIds.length) {
                        pageSelected = false;
                        allSelected = false;
                    }
                    setDownloadUrl();
                });
                deleteUrl = '{{route('users.destroy', 0)}}';
                $nextTick(() => {setDownloadUrl();});"
                action="#">
                <table class="table min-w-200 w-full border-2 border-base-200 rounded-md"
                    :class="!compact || 'table-compact'">

                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" x-model="pageSelected"
                                    @change="$dispatch('pageselect')"
                                    class="checkbox checkbox-xs"
                                    :class="!allSelected ? 'checkbox-primary' : 'checkbox-secondary'">
                            </th>
                            <th class="relative w-3/12">
                                <div class="flex flex-row items-center">
                                    <x-utils.spotsort name="name" val="{{ $sort['name'] ?? 'none' }}" />
                                    <div class="relative flex-grow ml-2">
                                        Name
                                        <x-utils.spotsearch textval="{{ $params['name'] ?? '' }}" textname="name"
                                            label="Search name" />
                                    </div>
                                </div>
                            </th>
                            <th class="relative w-4/12">
                                <div class="flex flex-row items-center">
                                    <x-utils.spotsort name="email" val="{{ $sort['email'] ?? 'none' }}" />
                                    <div class="relative flex-grow ml-2">
                                        Email
                                        <x-utils.spotsearch textval="{{ $params['email'] ?? '' }}" textname="email"
                                            label="Search email" />
                                    </div>
                                </div>
                            </th>
                            <th class="relative w-3/12 p-0">
                                <div class="flex flex-row items-center">
                                    <div class="relative flex-grow ml-2 flex flex-row items-center justify-between">
                                        <span>Role</span>
                                        <select x-data="{
                                            'val': 0
                                        }" x-init="val = {{ $filter['roles']['selected'] ?? 0 }};
                                        $nextTick(() => { $dispatch('setfilter', { data: { roles: val } }); });"
                                            @change.stop.prevent="$dispatch('spotfilter', {data: {roles: val}});"
                                            x-model="val" class="select select-bordered select-sm max-w-xs py-0 m-1"
                                            :class="val == 0 || 'text-accent'">
                                            <option value="0">All Roles</option>
                                            @foreach ($filter['roles']['options'] as $role)
                                                <option value="{{ $role['id'] }}"
                                                    @if (isset($filter['roles']['selected']) && $filter['roles']['selected'] == $role['id']) selected @endif>
                                                    {{ $role['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </th>
                            <th class="relative w-2/12">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr x-show="selectedIds.length > 0" x-transition>
                            <td colspan="5" class="text-center bg-warning text-base-200">
                                <span x-text="selectedIds.length" class="font-bold"></span>
                                &nbsp;<span class="font-bold">item<span x-show="selectedIds.length > 1">s</span>  selected.</span>
                                &nbsp;<button @click.prevent.stop="$dispatch('selectpage')"
                                class="btn btn-xs" :disabled="pageSelected">Select Page</button>
                                &nbsp;<button @click.prevent.stop="$dispatch('selectall')"
                                    class="btn btn-xs">Select All {{$total_results}} items</button>
                                &nbsp;<button @click.prevent.stop="$dispatch('cancelselection')"
                                    class="btn btn-xs">Cancel All</button>
                            </td>
                        </tr>
                        @foreach ($users as $user)
                            <tr>
                                <td><input type="checkbox" value="{{ $user->id }}" x-model="selectedIds"
                                        class="checkbox checkbox-primary checkbox-xs"></td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        {{ $role->name }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <div class="flex flex-row justify-center space-x-4 items-center">
                                        <a href="#"
                                            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('users.edit', $user->id)}}', route: 'users.edit', fresh: true});"
                                            class="btn btn-xs btn-warning capitalize">
                                            <span>Edit</span>&nbsp;<x-display.icon icon="icons.edit" height="h-4" width="w-4"/>
                                        </a>
                                        <button @click.prevent.stop="$dispatch('deleteitem', {itemId: {{$user->id}}});" class="btn btn-xs btn-error capitalize">Delete&nbsp;<x-display.icon icon="icons.delete" height="h-4" width="w-4"/></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div x-data="{
                        open: false,
                        id: null
                    }"
                    @deleteitem.window="id = $event.detail.itemId; open = true;"
                    class="absolute top-0 left-0 h-full w-full flex flex-row justify-center items-center bg-base-300 bg-opacity-50"
                    x-show="open">
                    <div class="bg-base-100 p-4 rounded-md shadow-md text-center space-y-5">
                        <div>You cannot undo this action. Do you really want to delete this user?</div>
                        <div class="flex flex-row justify-center items-center space-x-10">
                            <button @click.prevent.stop="open=false;" class="btn btn-sm capitalize">No</button>
                            <button @click.prevent.stop="open=false; deleteItem(id); id = null;" class="btn btn-sm btn-error capitalize">Yes</button>
                        </div>
                    </div>
                </div>
                <div x-data="{
                    open: false,
                    message: '',
                    success: false
                }"
                @deletedone.window="success = $event.detail.success; message = $event.detail.message; open = true;"
                class="absolute top-0 left-0 h-full w-full flex flex-row justify-center items-center bg-base-300 bg-opacity-50"
                x-show="open">
                <div class="bg-base-100 p-4 rounded-md shadow-md text-center space-y-5 min-w-72">
                    <div><span x-text="message" :class="success ? 'text-success' : 'text-error'"></span></div>
                    <div class="flex flex-row justify-center items-center space-x-10">
                        <button @click.prevent.stop="open=false; triggerFetch();" class="btn btn-sm capitalize" :class="success ? 'btn-success': 'btn-error'">Ok</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div class="my-4 p-2">
            @php
                $params = \Request::except(['x_mode']);
            @endphp
            {{ $users->appends($params)->links() }}
        </div>
    </div>
</x-dashboard-base>
