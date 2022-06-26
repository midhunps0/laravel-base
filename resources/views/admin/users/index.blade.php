<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{compact: $persist(false)}" class="p-3 border-b border-base-200 overflow-x-scroll">
        <div class="flex flex-row justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Users</h3>
            <div class="flex-grow flex flex-row justify-end items-center space-x-4">
                <div x-data="{count: {{$items_count}} }" class="flex flex-row items-center w-44 space-x-2">
                    <label for="items_count">Items per page: </label>
                    <select x-model="count"
                        @change="$dispatch('countchange', {count: count});" class="select select-bordered select-sm w-16 py-0">
                        <option value="10" @if($items_count == 10) selected @endif>10</option>
                        <option value="20" @if($items_count == 20) selected @endif>20</option>
                        <option value="30" @if($items_count == 30) selected @endif>30</option>
                        <option value="50" @if($items_count == 50) selected @endif>50</option>
                        <option value="100" @if($items_count == 100) selected @endif>100</option>
                        {{-- <option value="0"  @if($items_count == 0) selected @endif>Show all</option> --}}
                      </select>
                </div>
                <div>
                    <input x-model="compact" type="checkbox" id="compact" class="checkbox checkbox-xs checkbox-primary">
                    <label for="compact">Compact View</label>
                </div>
                <div x-data="{dropopen: false}" class="relative">
                    <label @click="dropopen = !dropopen;" tabindex="0" class="btn btn-xs m-1">Bulk&nbsp;<x-display.icon icon="icons.down"/></label>
                    <ul tabindex="0" class="absolute top-5 right-0 z-50 p-2 shadow bg-base-200 rounded-md w-52 scale-90 origin-top-right transition-all duration-200 opacity-0"
                        :class="!dropopen || 'top-8 scale-110 opacity-100'">
                      <li><a>Item 1</a></li>
                      <li><a>Item 2</a></li>
                    </ul>
                </div>
                <a href="#" role="button" class="btn btn-xs">Add&nbsp;<x-display.icon icon="icons.plus"/></a>
            </div>

        </div>
        <div class="overflow-x-scroll scroll-m-1 rounded-md">
            <table class="table min-w-200 w-full border-2 border-base-200 rounded-md"
                :class="!compact || 'table-compact'">
            <form x-data="{
                    url: '{{route('users.index')}}',
                    params: {},
                    search: {},
                    sort: {},
                    filters: {},
                    itemsCount: {{$items_count}},
                    processParams(params) {
                        let processed = [];
                        let paramkeys = Object.keys(params);
                        paramkeys.forEach((k) => {
                            processed.push(k + '::' + params[k]);
                        });
                        return processed;
                    },
                    triggerFetch() {
                        let allParams = {};
                        allParams.search = this.processParams(this.params);
                        allParams.sort = this.processParams(this.sort);
                        allParams.filter = this.processParams(this.filters);
                        allParams.items_count = this.itemsCount;
                        console.log(allParams);
                        $dispatch('linkaction', {link: this.url, params: allParams});
                    },
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
                        console.log('filters');
                        console.log(this.filters);
                    },
                    doFilter(detail) {
                        this.setFilter(detail);
                        console.log('filter data');
                        console.log(detail.data);
                        this.triggerFetch();
                    },
                    pageUpdateCount(count) {
                        this.itemsCount = count;
                        console.log('count:'+this.itemsCount);
                        this.triggerFetch();
                    }
                }"
                @spotsearch.window="fetchResults($event.detail)"
                @setparam.window="setParam($event.detail)"
                @spotsort.window="doSort($event.detail)"
                @setsort.window="setSort($event.detail)"
                @spotfilter.window="console.log('spot filter fn');console.log($event); doFilter($event.detail);"
                @setfilter.window="console.log('set filter fn');setFilter($event.detail)"
                @countchange.window="pageUpdateCount($event.detail.count)"
                action="#">
                <thead>
                    <tr>
                        <th class="relative w-3/12">
                            <div class="flex flex-row items-center">
                                <x-utils.spotsort name="name"
                                    val="{{$sort['name'] ?? 'none'}}"/>
                                <div class="relative flex-grow ml-2">
                                    Name
                                    <x-utils.spotsearch
                                        textval="{{$params['name'] ?? ''}}"
                                        textname="name"
                                        label="Search name"/>
                                </div>
                            </div>
                        </th>
                        <th class="relative w-4/12">
                            <div class="flex flex-row items-center">
                                <x-utils.spotsort name="email"
                                    val="{{$sort['email'] ?? 'none'}}"/>
                                <div class="relative flex-grow ml-2">
                                    Email
                                    <x-utils.spotsearch
                                        textval="{{$params['email'] ?? ''}}"
                                        textname="email"
                                        label="Search email"/>
                                </div>
                            </div>
                        </th>
                        <th class="relative w-3/12 p-0">
                            <div class="flex flex-row items-center">
                                <div class="relative flex-grow ml-2 flex flex-row items-center justify-between">
                                    <span>Roles</span>
                                    <select x-data="{
                                        'val': 0
                                    }"
                                    x-init="
                                        val = {{$filter['roles']['selected'] ?? 0}};
                                        $nextTick(() => {$dispatch('setfilter', {data: {roles: val}});});
                                    "
                                    @change.stop.prevent="$dispatch('spotfilter', {data: {roles: val}});"
                                    x-model="val"
                                    class="select select-bordered select-sm max-w-xs py-0 m-1"
                                    :class="val == 0 || 'text-accent'">
                                        <option value="0">All Roles</option>
                                        @foreach ($filter['roles']['options'] as $role)
                                            <option value="{{$role['id']}}"
                                                @if(isset($filter['roles']['selected']) && $filter['roles']['selected'] == $role['id'])
                                                    selected
                                                @endif>
                                                {{$role['name']}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </th>
                        <th class="relative w-2/12">Action</th>
                    </tr>
                </thead>
            </form>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                {{$role->name}}@if (!$loop->last), @endif
                            @endforeach
                        </td>
                        <td>Edit/Delete</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="my-4 p-2">
            @php
                $params = \Request::except(['x_mode']);
            @endphp
            {{ $users->appends($params)->links() }}
        </div>
    </div>
</x-dashboard-base>