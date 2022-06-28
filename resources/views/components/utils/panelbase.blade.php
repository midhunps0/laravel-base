@props([
    'x_ajax',
    'title',
    'indexUrl',
    'downloadUrl',
    'selectIdsUrl',
    'results',
    'items_count',
    'items_ids',
    'total_results',
    'current_page',
    'unique_str',
    'selectionEnabled' => true,
])
<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{ compact: $persist(false) }" class="p-3 border-b border-base-200 overflow-x-scroll">

        @if (isset($body))
        <h3 class="text-xl font-bold">{{$title}}</h3>
        <div>{{$body}}</div>
        @endif

        <div class="flex flex-row justify-between items-center mb-4">
            @if (!isset($body))
            <h3 class="text-xl font-bold">{{$title}}</h3>
            @endif
            <div class="flex-grow flex flex-row justify-end items-center space-x-4">
                <x-utils.itemscount items_count="{{ $items_count }}" />
                <div>
                    <input x-model="compact" type="checkbox" id="compact" class="checkbox checkbox-xs checkbox-primary">
                    <label for="compact">{{__('Compact View')}}</label>
                </div>
                <div x-data="{dropopen: false, url_all: '', url_selected: ''}"
                    @downloadurl.window="url_all = $event.detail.url_all; url_selected=$event.detail.url_selected;"
                    @click.outside="dropopen = false;"
                    class="relative">
                    <label @click="dropopen = !dropopen;" tabindex="0" class="btn btn-xs m-1">{{__('Export')}}&nbsp;
                        <x-display.icon icon="icons.down" />
                    </label>
                    <ul tabindex="0"
                        class="absolute top-5 right-0 z-50 p-2 shadow-md bg-base-200 rounded-md w-52 scale-90 origin-top-right transition-all duration-100 opacity-0"
                        :class="!dropopen || 'top-8 scale-110 opacity-100'">
                        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_selected" download>{{__('Download Selected')}}</a></li>
                        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_all" download>{{__('Download All')}}</a></li>
                    </ul>
                </div>
                {{-- <a href="#" role="button" class="btn btn-xs">Add&nbsp;
                    <x-display.icon icon="icons.plus" />
                </a> --}}
            </div>
        </div>

        <div class="overflow-x-scroll scroll-m-1 rounded-md">
            <form x-data="{
                url: '{{ $indexUrl }}',
                params: {},
                {{-- search: {}, --}}
                sort: {},
                filters: {},
                itemsCount: {{ $items_count }},
                itemIds: [{{$items_ids}}],
                selectedIds: $persist([]).as('{{$unique_str}}ids'),
                pageSelected: false,
                allSelected: false,
                totalResults: {{$total_results}},
                currentPage: {{$current_page}},
                downloadUrl: '{{$downloadUrl}}',

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

                    return params;
                },
                triggerFetch() {
                    let allParams = this.paramsExceptSelection();
                    //allParams.selected_ids = this.selectedIds.join('|');
                    this.selectedIds = [];

                    $dispatch('linkaction', { link: this.url, params: allParams });
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
                    axios.get('{{$selectIdsUrl}}', {params: params} ).then(
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
                x-init="$watch('selectedIds', (ids) => {
                    if (ids.length < itemIds.length) {
                        pageSelected = false;
                        allSelected = false;
                    }
                    setDownloadUrl();
                }); $nextTick(() => {setDownloadUrl();});"
                action="#">
                <table class="table min-w-200 w-full border-2 border-base-200 rounded-md"
                    :class="!compact || 'table-compact'">

                    <thead>
                        <tr>
                            @if ($selectionEnabled)
                            <th class="w-7">
                                <input type="checkbox" x-model="pageSelected"
                                    @change="$dispatch('pageselect')"
                                    class="checkbox checkbox-xs"
                                    :class="!allSelected ? 'checkbox-primary' : 'checkbox-secondary'">
                            </th>
                            @endif
                            {{$thead}}
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
                        {{$rows}}
                    </tbody>
                </table>
            </form>
        </div>
        <div class="my-4 p-2">
            @php
                $params = \Request::except(['x_mode']);
            @endphp
            {{ $results->appends($params)->links() }}
        </div>
    </div>
</x-dashboard-base>
