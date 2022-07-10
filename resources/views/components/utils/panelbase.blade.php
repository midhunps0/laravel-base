@props(['x_ajax', 'title', 'indexUrl', 'downloadUrl', 'selectIdsUrl', 'results', 'results_name', 'items_count', 'items_ids', 'total_results', 'current_page', 'unique_str', 'results_json' => '', 'result_calcs' => [], 'selectionEnabled' => true, 'total_disp_cols', 'adv_fields' => '', 'enableAdvSearch' => false, 'paginator', 'columns' => [], 'orderBaseUrl' => ''])
<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{ compact: $persist(false), showAdvSearch: false, showOrderForm: false, noconditions: true }" class="p-3 border-b border-base-200 overflow-x-scroll relative">

        @if (isset($body))
            <h3 class="text-xl font-bold">{{ $title }}</h3>
            <div>{{ $body }}</div>
        @endif

        <div class="flex flex-row flex-wrap justify-between items-center mb-4">
            @if (!isset($body))
                <h3 class="text-xl font-bold">{{ $title }}</h3>
            @endif
            <div class="flex-grow flex flex-row flex-wrap justify-end items-center space-x-4">
                @if ($enableAdvSearch)
                    <div>
                        <button @click.prevent.stop="showAdvSearch = true;"
                            class="btn btn-sm py-0 px-1 hover:bg-base-300 hover:text-warning transition-colors text-base-content rounded-md flex flex-row items-center justify-center"
                            :class="noconditions || 'bg-accent text-base-200'">
                            <x-display.icon icon="icons.settings" height="h-5" width="w-5" />
                        </button>
                    </div>
                    <div>
                        <button @click.prevent.stop="showOrderForm = true;"
                            class="btn btn-sm py-0 px-1 hover:bg-base-300 hover:text-warning transition-colors text-base-content rounded-md flex flex-row items-center justify-center">
                            <x-display.icon icon="icons.play" height="h-5" width="w-5" />&nbsp;Create Order
                        </button>
                    </div>
                @endif
                <x-utils.itemscount items_count="{{ $items_count }}" />
                <div>
                    <input x-model="compact" type="checkbox" id="compact"
                        class="checkbox checkbox-xs checkbox-primary">
                    <label for="compact">{{ __('Compact View') }}</label>
                </div>
                <div x-data="{ dropopen: false, url_all: '', url_selected: '' }"
                    @downloadurl.window="url_all = $event.detail.url_all; url_selected=$event.detail.url_selected;"
                    @click.outside="dropopen = false;" class="relative">
                    <label @click="dropopen = !dropopen;" tabindex="0"
                        class="btn btn-xs m-1">{{ __('Export') }}&nbsp;
                        <x-display.icon icon="icons.down" />
                    </label>
                    <ul x-show="dropopen" tabindex="0"
                        class="absolute top-5 right-0 z-50 p-2 shadow-md bg-base-200 rounded-md w-52 scale-90 origin-top-right transition-all duration-100 opacity-0"
                        :class="!dropopen || 'top-8 scale-110 opacity-100'">
                        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_selected"
                                download>{{ __('Download Selected') }}</a></li>
                        <li class="py-2 px-4 hover:bg-base-100"><a :href="url_all"
                                download>{{ __('Download All') }}</a></li>
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
                sort: {},
                filters: {},
                itemsCount: {{ $items_count }},
                itemIds: [{{ $items_ids }}],
                selectedIds: $persist([]).as('{{ $unique_str }}ids'),
                pageSelected: false,
                allSelected: false,
                pages: [],
                totalResults: {{ $total_results }},
                currentPage: {{ $current_page }},
                downloadUrl: '{{ $downloadUrl }}',
                results: null,
                order_base_url: '{{ $orderBaseUrl }}',
                order: {
                    bors: 'Sell',
                    qty: 0,
                    price: 0.00,
                    slippage: 0.01,
                    get url() {
                        console.log(this.qty);
                        console.log(this.price);
                        console.log(this.slippage);
                        return '{{ $orderBaseUrl }}'
                        + '?bors=' + this.bors
                        + '&qty=' + this.qty
                        + '&price=' + this.price
                        + '&slippage=' + this.slippage;
                    },
                    get enabled() {
                        return this.qty.length != 0 && this.price.length != 0
                            && this.slippage.length != 0
                            && this.slippage >= 0.01 && this.slippage <= 2;
                    },
                    reset() {
                        this.bors = 'Sell';
                        this.qty = 0;
                        this.price = 0.00;
                        this.slippage = 0.01;
                    }
                },
                paginatorPage: null,
                paginator: {
                    currentPage: 0,
                    totalItems: 0,
                    lastPage: 0,
                    itemsPerPage: 0,
                    nextPageUrl: '',
                    pervPageUrl: '',
                    elements: [],
                    firstItem: null,
                    lastItem: null,
                    count: 0,
                },
                conditions: [{
                    field: 'none',
                    type: '',
                    operation: 'none',
                    value: 0
                }],
                fieldOperators: {
                    none: [{ key: 'none', text: 'Choose A Condition' }],
                    numeric: [
                        { key: 'gt', text: 'Greater Than' },
                        { key: 'lt', text: 'Less Than' },
                        { key: 'gte', text: 'Greater Than Or Equal To' },
                        { key: 'lte', text: 'Less Than Or Equal To' },
                        { key: 'eq', text: 'Equal To' },
                        { key: 'neq', text: 'Not Equal To' },
                    ],
                    string: [
                        { key: 'ct', text: 'Contains' },
                        { key: 'st', text: 'Starts With' },
                        { key: 'en', text: 'Ends With' },
                    ],
                },
                advFields: {
                     none: {key: 'none', text: 'Select A Field', type: 'none'},
                    {{ $adv_fields }}
                },
                advQueryParams() {
                    if ((this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                        return [];
                    }
                    let processed = this.conditions.map((c) => {
                        return c.field + '::' + c.operation + '::' + c.value;
                    });
                    return processed;
                },
                addContition() {
                    this.conditions.push({
                        field: 'none',
                        type: '',
                        operation: 'none',
                        value: 0
                    });
                },
                advSearchStatus() {
                    showAdvSearch = false;
                    console.log('conditions:');
                    console.log(this.conditions);
                    if ((this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                        noconditions = true;
                        this.triggerFetch();
                    } else {
                        noconditions = false;
                    }
                },
                /*
                getConditionsText() {
                    let str = '';
                    this.conditions.forEach((c) => {
                        str +=
                    });
                },
                findTextForOprKey(key) {
                    let item = null;
                    let item = this.fieldOperators.numeric.filter((x) => {
                        return x.key == key;
                    });
                    if (!item) {
                        item = this.fieldOperators.string.filter((x) => {
                            return x.key == key;
                        });
                    }
                    return item.text;
                },
                findTextForFieldKey(key) {
                    let text = 'Some field';
                    Object.keys(this.advFields).forEach((f) => {
                        if(this.advFields[f].key == key) {
                            text = this.advFields[f].text;
                        }
                    });
                    return text;
                },*/
                runQuery() {
                    this.params = {};
                    this.sort = {};
                    this.filters = {};
                    if ((this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                        noconditions = true;
                    } else {
                        noconditions = false;
                    }
                    showAdvSearch = false;
                    this.triggerFetch();
                    /*
                    let searchParams = this.advQueryParams();
                    let allParams = {
                        adv_search: searchParams
                    };
                    axios.get(
                        this.url, {
                            headers: {
                                'X-ACCEPT-MODE': 'only-json'
                            },
                            params: allParams
                        }
                    ).then((r) => {
                        this.results = JSON.parse(r.data.results_json);
                        this.totalResults = r.data.total_results;
                        this.currentPage = r.data.current_page;
                        {{-- this.conditions = [{
                            field: 'none',
                            type: '',
                            operation: 'none',
                            value: 0
                        }]; --}}

                        if ((this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                            noconditions = true;
                        } else {
                            noconditions = false;
                        }
                        showAdvSearch = false;
                    }).catch((e) => {
                        alert('Unexpected error. No results fetched.')
                        console.log(e);
                    });*/
                },
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
                    if (!(this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                        params.adv_search = this.advQueryParams();
                    }
                    params.items_count = this.itemsCount;
                    params.page = this.paginatorPage;

                    return params;
                },
                triggerFetch() {
                    let allParams = this.paramsExceptSelection();

                    axios.get(
                        this.url, {
                            headers: {
                                'X-ACCEPT-MODE': 'only-json'
                            },
                            params: allParams
                        }
                    ).then((r) => {
                        this.results = JSON.parse(r.data.results_json);
                        this.totalResults = r.data.total_results;
                        this.currentPage = r.data.current_page;
                        $dispatch('setpagination', {paginator: JSON.parse(r.data.paginator)});
                        console.log('r: '+r.data.route);
                        $dispatch('routechange', {route: r.data.route});
                    });
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
                    if (detail.exclusive) {
                        this.sort = {};
                    }
                    if (detail.data[keys[0]] != 'none') {
                        this.sort[keys[0]] = detail.data[keys[0]];
                    } else {
                        if (typeof(this.sort[keys[0]]) != 'undefined') {
                            delete this.sort[keys[0]];
                        }
                    }
                    $dispatch('clearsorts', {sorts: this.sort});
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
                    axios.get('{{ $selectIdsUrl }}', { params: params }).then(
                        (r) => {
                            this.itemIds = r.data.ids;
                            this.selectedIds = r.data.ids;
                            this.pageSelected = true;
                            this.allSelected = true;
                            ajaxLoading = false;
                        }
                    ).catch(
                        function(e) {
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

                    $dispatch('downloadurl', { url_all: this.downloadUrl + '?' + url_all, url_selected: this.downloadUrl + '?' + url_selected });
                },
                setResults(results) {
                    this.results.map((result) => {
                        {{-- result.cur_val = result.cmp * result.qty; --}}
                        @foreach($result_calcs as $calc)
                        {{ $calc }}
                        @endforeach
                        return result;
                    });
                    return results;
                },

                resetAdvSearch() {
                    this.conditions = [{
                        field: 'none',
                        operation: 'none',
                        value: 0
                    }];
                },
                getFieldOperators(field) {
                    console.log('field:');
                    console.log(field);
                    let f = this.advFields.filter((f) => {
                        return f.key == field;
                    })[0];
                    console.log('f');
                    console.log(f);
                    if (f == null) {
                        return [];
                    }
                    switch (f.type) {
                        case 'numeric':
                            return this.MathOprs;
                            break;
                        case 'text':
                            return this.stringOprs;
                            break;
                    }
                },
                getPaginatedPage(page) {
                    this.paginatorPage = page;
                    this.triggerFetch();
                },
                getOrderItemsCount() {
                    return this.selectedIds.length > 0 ? this.selectedIds.length : this.totalResults;
                }
            }" @spotsearch.window="fetchResults($event.detail)"
                @setparam.window="setParam($event.detail)" @spotsort.window="doSort($event.detail)"
                @setsort.window="setSort($event.detail)" @spotfilter.window="doFilter($event.detail);"
                @setfilter.window="setFilter($event.detail)" @countchange.window="pageUpdateCount($event.detail.count);"
                @selectpage="selectPage();"
                @selectall="selectAll();"
                @cancelselection="cancelSelection();"
                @pageselect="processPageSelect();"
                @paginator.window="getPaginatedPage($event.detail.page);"
                x-init="$watch('selectedIds', (ids) => {
                    if (ids.length < itemIds.length) {
                        pageSelected = false;
                        allSelected = false;
                    }
                    setDownloadUrl();
                    $watch('order', (ord) => {
                        order.url = order_base_url + '?' +
                            'bors=' + ord.bors + '&' +
                            'qty=' + ord.qty + '&' +
                            'price=' + ord.price + '&' +
                            'slippage=' + ord.slippage;
                        })
                    });

                $nextTick(() => {
                    setDownloadUrl();
                    results = JSON.parse(document.getElementById('results_json').value);
                    results = setResults(results);
                    $dispatch('setpagination', {paginator: JSON.parse('{{$paginator}}')});
                });" action="#">
                <table class="table min-w-200 w-full border-2 border-base-200 rounded-md"
                    :class="compact ? 'table-mini' : 'table-compact'">

                    <thead>
                        <tr>
                            @if ($selectionEnabled)
                                <th class="w-7">
                                    <input type="checkbox" x-model="pageSelected" @change="$dispatch('pageselect')"
                                        class="checkbox checkbox-xs"
                                        :class="!allSelected ? 'checkbox-primary' : 'checkbox-secondary'">
                                </th>
                            @endif
                            {{ $thead }}
                        </tr>
                    </thead>
                    <tbody>
                        <tr x-show="selectedIds.length > 0" x-transition>
                            <td colspan="{{ $total_disp_cols }}" class="text-center bg-warning text-base-200">
                                <span x-text="selectedIds.length" class="font-bold"></span>
                                &nbsp;<span class="font-bold">item<span x-show="selectedIds.length > 1">s</span>
                                    selected.</span>
                                &nbsp;<button @click.prevent.stop="$dispatch('selectpage')" class="btn btn-xs"
                                    :disabled="pageSelected">Select Page</button>
                                &nbsp;<button @click.prevent.stop="$dispatch('selectall')" class="btn btn-xs">Select All
                                    {{ $total_results }} items</button>
                                &nbsp;<button @click.prevent.stop="$dispatch('cancelselection')"
                                    class="btn btn-xs">Cancel All</button>
                            </td>
                        </tr>
                        {{ $rows }}
                    </tbody>
                </table>
                @if ($enableAdvSearch)
                <div x-show="showAdvSearch" x-transition
                    class="absolute top-0 left-0 z-30 w-full flex flex-row justify-center p-16 items-start bg-base-100 bg-opacity-60 min-h-full">
                    <div
                        class="flex flex-col items-center px-4 py-6 rounded-md w-2/3 mx-auto bg-base-200 shadow-lg relative">
                        <button @click.prevent.stop="advSearchStatus"
                            class="w-8 h-8 p-1 bg-base-100 hover:bg-base-300 hover:text-warning transition-colors text-base-content rounded-md flex flex-row items-center justify-center absolute top-2 right-2">
                            <x-display.icon icon="icons.close" height="h-7" width="w-7" />
                        </button>
                        <div class="w-full flex flex-row justify-center">
                            <h3 class="text-lg font-bold mb-4">Advanced Query</h3>
                        </div>
                        <div class="w-full flex flex-row justify-center mb-2">
                            <div
                                class="flex-1 px-2 py-1 mx-1 font-bold text-center border-b border-opacity-60 border-base-content">
                                Field</div>
                            <div
                                class="flex-1 px-2 py-1 mx-1 font-bold text-center border-b border-opacity-60 border-base-content">
                                Condition</div>
                            <div
                                class="w-24 px-2 py-1 mx-1 font-bold text-center border-b border-opacity-60 border-base-content">
                                Value</div>
                            <div class="w-10 px-2 flex flex-row space-x-2">
                            </div>
                        </div>
                        <template x-for="(condition, index) in conditions">
                            <div class="w-full flex flex-row justify-center my-2">
                                <div class="w-full flex-1 mx-1">
                                    <select x-model="condition.field" :id="'advf' + index"
                                        class="select select-sm select-bordered py-0 w-full"
                                        @change="document.getElementById('advop'+index).dispatchEvent(new Event('change', { 'bubbles': true }));">
                                        {{-- <option value="none">Select Field</option> --}}
                                        <template x-for="field in Object.values(advFields)">
                                            <option :value="field.key"></span><span x-text="field.text"></span>
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex-1 mx-1">
                                    <select x-model="condition.operation" :id="'advop' + index"
                                        class="select select-sm select-bordered py-0 w-full">
                                        {{-- <option value="none">Choose Condition</option> --}}
                                        <template x-for="op in fieldOperators[(advFields[condition.field]).type]"
                                            :key="op.key">
                                            <option :value="op.key"><span x-text="op.text"></span></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="w-24 mx-1">
                                    <input type="text" x-model="condition.value"
                                        class="input input-sm input-bordered w-full">
                                </div>
                                <div class="w-10 px-2 flex flex-row items-center">
                                    <button @click.prevent.stop="conditions.splice(index, 1);"
                                        class="w-6 h-6 p-1 bg-error text-base-content rounded-md flex flex-row items-center justify-center disabled:bg-opacity-70" :disabled="conditions.length == 1">
                                        <x-display.icon icon="icons.delete" height="h-5" width="w-5" />
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="w-full flex flex-row justify-center mt-6 mb-2">
                            <div class="flex-1 flex-grow px-1">
                                <button @click.prevent.stop="addContition"
                                    class="btn btn-sm btn-warning p-0 w-full border border-base-100 flex felx-row items-center justify-center">
                                    <x-display.icon icon="icons.plus" height="h-4" width="w-4" />&nbsp;Add
                                    Condition
                                </button>
                            </div>
                            <div class="flex-1 flex-grow px-1">
                                <button @click.prevent.stop="runQuery"
                                    class="btn btn-sm btn-success p-0 w-full border border-base-100 flex felx-row items-center justify-center">
                                    <x-display.icon icon="icons.info" height="h-4" width="w-4" />&nbsp;Fetch
                                    Results
                                </button>
                            </div>
                            <div class="w-10 px-2 flex flex-row items-center">
                                <button @click.prevent.stop="resetAdvSearch"
                                    class="w-6 h-6 p-1 bg-error text-base-content rounded-md flex flex-row items-center justify-center">
                                    <x-display.icon icon="icons.refresh" height="h-5" width="w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Order Form --}}
                <div x-show="showOrderForm" x-transition
                    class="absolute top-0 left-0 z-30 w-full flex flex-row justify-center p-16 items-start bg-base-100 bg-opacity-60 min-h-full">
                    <div class="flex flex-col items-center px-4 py-6 rounded-md w-2/3 mx-auto bg-base-200 shadow-lg relative">
                        <button @click.prevent.stop="showOrderForm = false;"
                            class="w-8 h-8 p-1 bg-base-100 hover:bg-base-300 hover:text-warning transition-colors text-base-content rounded-md flex flex-row items-center justify-center absolute top-2 right-2">
                            <x-display.icon icon="icons.close" height="h-7" width="w-7" />
                        </button>
                        <div class="w-full flex flex-row justify-center">
                            <h3 class="text-lg font-bold mb-4">Create Order</h3>
                        </div>
                        <div class="w-full justify-center items-center rounded-md">
                            <h6 class="text-sm p-4">
                                This order will be generated for <span class="font-bold text-warning text-lg" x-text="getOrderItemsCount()"></span> items.
                            </h6>
                        </div>
                        <div class="w-full border border-warning rounded-md">
                            <div class="flex flex-row justify-between w-full mx-auto p-4 m-4 space-x-2">
                                <div class="w-1/4">
                                    <label for="bors">Action</label><br/>
                                    <select x-model="order.bors" id="bors" class="select select-sm py-0 w-full">
                                        <option value="Buy">Buy</option>
                                        <option value="Sell">Sell</option>
                                    </select>
                                </div>
                                <div class="w-1/4">
                                    <label for="order_qty">Quantity</label><br/>
                                    <input x-model="order.qty" id="order_qty" type="number" class="input input-sm w-full" oninput="if (this.value.length != 0) {var val = Math.floor(this.value); this.value = null; this.value = val;}">
                                </div>
                                <div class="w-1/4">
                                    <label for="order_price">Price</label><br/>
                                    <input x-model="order.price" type="number" min="0.00" step="0.01" id="order_price" type="text" class="input input-sm w-full">
                                </div>
                                <div class="w-1/4">
                                    <label for="order_slippage">Slippage</label><br/>
                                    <input x-model="order.slippage" type="number" min="0.00" max="2.00" step="0.01" id="order_slippage" type="text" class="input input-sm w-full" :class="order.slippage < 0.01 || order.slippage > 2 ? 'text-error border border-error' : ''">
                                    <label class="label">
                                        <span class="label-text-alt" :class="order.slippage < 0.01 ? 'text-error' : ''">Min: 0.01</span>
                                        <span class="label-text-alt" :class="order.slippage > 2 ? 'text-error' : ''">Max: 2.00</span>
                                    </label>
                                </div>
                            </div>
                            <div class="my-4 px-1 text-center w-full flex flex-row justify-between space-x-4">
                                <button @click.prevent.stop="order.reset();" class="btn btn-sm text-base-content">Reset</button>
                                <a :href="order.url"
                                    class="btn btn-sm btn-success py-0 border border-base-100 flex felx-row items-center justify-center mx-auto" download :disabled="!order.enabled">
                                    <x-display.icon icon="icons.info" height="h-4" width="w-4" />&nbsp;Generate Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </form>

        </div>
        <div class="my-4 p-2">
            <x-utils.paginator />
        </div>
    </div>
</x-dashboard-base>
