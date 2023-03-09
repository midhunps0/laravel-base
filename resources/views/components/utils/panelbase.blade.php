@props(['x_ajax', 'title', 'indexUrl', 'downloadUrl', 'selectIdsUrl', 'results', 'results_name', 'items_count', 'items_ids', 'total_results', 'current_page', 'unique_str', 'results_json' => '', 'result_calcs' => [], 'selectionEnabled' => true, 'total_disp_cols', 'adv_fields' => '', 'enableAdvSearch' => false, 'soPriceField' => 'false', 'paginator', 'columns' => [], 'orderBaseUrl' => '', 'orderVerifyUrl' => '', 'orderCheckUrl' => '', 'id' => '', 'enableOrderform' => true, 'search' => [], 'sort' => [], 'filter' => [], 'advsearch' => []])
<x-dashboard-base :ajax="$x_ajax">
    <div id="{{$id}}" x-data="{ compact: $persist(false), showAdvSearch: false, showOrderForm: false, noconditions: true }" class="p-3 overflow-x-scroll relative h-full" :id="$id('panel-base')">

        @if (isset($inputFields))
        <div>
            {{ $inputFields }}
        </div>
        @endif
        <div class="flex-grow flex flex-row flex-wrap justify-between items-center space-x-4">
            <h3 class="text-xl font-bold"><span>{{ $title }}</span>&nbsp;
                <button x-data="{navcollapsed: $persist(false)}"
                    x-init="$dispatch('navresize', {navcollapsed: navcollapsed});" class="btn btn-xs" :class="!navcollapsed || 'text-warning'" @click.prevent.stop="navcollapsed = !navcollapsed; $dispatch('navresize', {navcollapsed: navcollapsed});">
                    <x-display.icon x-show="navcollapsed" icon="icons.minus_circle" height="h-4" width="w-4"/>
                    <x-display.icon x-show="!navcollapsed" icon="icons.expand" height="h-4" width="w-4"/>
                </button>
            </h3>
            @if (isset($searchbox))
                <div>{{$searchbox}}</div>
            @endif
            <div class="flex-grow flex flex-row flex-wrap justify-end items-center space-x-4">
                <div class="flex-grow flex flex-row flex-wrap justify-end items-center space-x-4">
                    <div x-data="{showbtns: false}" class="flex flex-row flex-grow justify-end items-center space-x-4">
                        <div class="relative w-full">
                            <div x-show="showbtns" class="w-full absolute right-0 -top-4 z-20 flex flex-row justify-end items-center space-x-4">
                                <x-utils.itemscount items_count="{{ $items_count }}" />
                                <div>
                                    <input x-model="compact" type="checkbox" id="compact"
                                        class="checkbox checkbox-xs checkbox-primary">
                                    <label for="compact">{{ __('Compact View') }}</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-xs" :class="!showbtns || 'text-secondary'" @click.prevent.stop="showbtns = !showbtns;">
                                <x-display.icon icon="icons.gear" height="h-4" width="w-4"/>
                            </button>
                        </div>
                    </div>
                    <div x-data="{ dropopen: false, url_all: '', url_selected: '', selectedCount: 0 }"
                        @downloadurl.window="url_all = $event.detail.url_all; url_selected=$event.detail.url_selected; selectedCount = $event.detail.idscount;"
                        @click.outside="dropopen = false;" class="relative">
                        <label @click="dropopen = !dropopen;" tabindex="0"
                            class="btn btn-xs m-1">{{ __('Export') }}&nbsp;
                            <x-display.icon icon="icons.down" />
                        </label>
                        <ul x-show="dropopen" tabindex="0"
                            class="absolute top-5 right-0 z-50 p-2 shadow-md bg-base-200 rounded-md w-52 scale-90 origin-top-right transition-all duration-100 opacity-0"
                            :class="!dropopen || 'top-8 scale-110 opacity-100'">
                            <li class="py-2 px-4" :class="selectedCount > 0 ? 'cursor-pointer hover:bg-base-100' : 'opacity-40'">
                                <span x-show="selectedCount == 0">{{ __('Download Selected') }}</span>
                                <a x-show="selectedCount > 0" :href="url_selected"
                                    download>{{ __('Download Selected') }}</a>
                                </li>
                            <li class="py-2 px-4 hover:bg-base-100"><a :href="url_all"
                                    download>{{ __('Download All') }}</a></li>
                        </ul>
                    </div>
                </div>
                {{-- <a href="#" role="button" class="btn btn-xs">Add&nbsp;
                    <x-display.icon icon="icons.plus" />
                </a> --}}
            </div>
        </div>
        @if (isset($body))
        <div class="flex flex-row justify-between items-center flex-wrap">
            <div class="my-4">{{ $body }}</div>
            @if ($enableAdvSearch)
            <div class="flex flex-row items-center justify-end space-x-4 flex-wrap">
                <div>
                    <button @click.prevent.stop="showAdvSearch = true;"
                        @keydown.window="
                        if($event.altKey && $event.keyCode == 65) {
                            showAdvSearch = true;
                        }
                        if($event.altKey && $event.shiftKey && $event.keyCode == 65) {
                            showAdvSearch = false;
                        }
                        "
                        class="btn btn-sm py-0 px-1 hover:bg-base-300 hover:text-warning transition-colors rounded-md flex flex-row items-center justify-center"
                        :class="noconditions || 'bg-accent text-base-200'">
                        <x-display.icon icon="icons.doc_search" height="h-5" width="w-5" />&nbsp;Adv Search
                    </button>
                </div>
                @if ($enableOrderform)
                <div>
                    <button @click.prevent.stop="$dispatch('showorderform');"
                    @keydown.window="
                    if($event.altKey && $event.keyCode == 83) {
                        showOrderForm = true;
                    }
                    if($event.altKey && $event.shiftKey && $event.keyCode == 83) {
                        showOrderForm = false;
                    }
                    "
                        class="btn btn-sm py-0 px-1 hover:bg-base-300 hover:text-warning transition-colors rounded-md flex flex-row items-center justify-center">
                        <x-display.icon icon="icons.play" height="h-5" width="w-5" />&nbsp;Sell Order
                    </button>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <div class="flex flex-row flex-wrap justify-between items-center mb-2">
            @if (!isset($body))
                {{-- <h3 class="text-xl font-bold">{{ $title }}</h3> --}}
                @if ($enableAdvSearch)
                <div class="flex flex-row items-center justify-end space-x-4 flex-wrap w-full mt-4">
                    <div>
                        <button @click.prevent.stop="showAdvSearch = true;"
                            @keydown.window="
                            if($event.altKey && $event.keyCode == 65) {
                                showAdvSearch = true;
                            }
                            if($event.altKey && $event.shiftKey && $event.keyCode == 65) {
                                showAdvSearch = false;
                            }
                            "
                            class="btn btn-sm py-0 px-1 hover:bg-base-300 hover:text-warning transition-colors rounded-md flex flex-row items-center justify-center"
                            :class="noconditions || 'bg-accent text-base-200'">
                            <x-display.icon icon="icons.doc_search" height="h-5" width="w-5" />&nbsp;Adv Search
                        </button>
                    </div>
                    @if ($enableOrderform)
                    <div>
                        <button @click.prevent.stop="$dispatch('showorderform');"
                        @keydown.window="
                        if($event.altKey && $event.keyCode == 83) {
                            showOrderForm = true;
                        }
                        if($event.altKey && $event.shiftKey && $event.keyCode == 83) {
                            showOrderForm = false;
                        }
                        "
                            class="btn btn-sm py-0 px-1 hover:bg-base-300 hover:text-warning transition-colors rounded-md flex flex-row items-center justify-center">
                            <x-display.icon icon="icons.play" height="h-5" width="w-5" />&nbsp;Sell Order
                        </button>
                    </div>
                    @endif
                </div>
                @endif
            @endif
        </div>
        @if (isset($extra))
        <div class="my-2">
            {{$extra}}
        </div>
        @endif
        <div class="rounded-md relative">
            <form x-data="{
                //url: '{{ $indexUrl }}',
                itemsCount: {{ $items_count }},
                totalResults: {{ $total_results }},
                currentPage: {{ $current_page }},
                downloadUrl: '{{ $downloadUrl }}',
                orderBaseUrl: '{{ $orderBaseUrl }}',
                orderCheckUrl: '{{ $orderCheckUrl }}',
                params: {},
                sort: {},
                filters: {},
                itemIds: [],
                selectedIds: [],//$persist([]).as('{{ $unique_str }}ids'),
                pageSelected: false,
                allSelected: false,
                pages: [],
                results: null,
                aggregates: null,
                profit_margin: '0',
                {{-- orderVerifyUrl: '{{ $orderVerifyUrl }}', --}}
                order: {
                    //bors: 'Sell',
                    qty: 0,
                    price: 0.00,
                    slippage: 0.01,
                    listVerified: false,
                    listInvalid: false,
                    processing: false,
                    message: '',
                    selIdsUrlStr: '',
                    statChecked: false,
                    uniqueSymbol: false,
                    chosenSymbol: '',
                    get url() {
                        return '{{ $orderBaseUrl }}'
                        + '?bors=' + this.bors
                        + '&qty=' + this.qty
                        + '&price=' + this.price
                        + '&slippage=' + this.slippage
                        + this.selIdsUrlStr;
                    },
                    get enabled() {
                        return this.qty.length != 0 && this.price.length != 0
                            && this.slippage.length != 0
                            && this.slippage >= 0.01 && this.slippage <= 1;
                    },
                    reset() {
                        //this.bors = 'Sell';
                        this.qty = 0;
                        this.price = 0.00;
                        this.slippage = 0.01;
                        this.listVerified = false;
                        this.listInvalid = false;
                        this.processing = false;
                        this.message = '';
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
                verifyOrderList() {
                    let allParams = this.paramsExceptSelection();
                    if (this.selectedIds.length > 0) {
                        allParams.selected_ids = this.selectedIds.join('|');
                    }
                    this.order.processing = true;
                    axios.get(
                        this.orderVerifyUrl, {
                            headers: {
                                'X-ACCEPT-MODE': 'only-json'
                            },
                            params: allParams
                        }
                    ).then((r) => {
                        if(r.data.success) {
                            this.order.listVerified = true;
                            if (this.selectedIds.length > 0) {
                                this.order.selIdsUrlStr = '&selected_ids=' + this.selectedIds.join('|');
                            }
                        } else {
                            this.order.listInvalid = true;
                        }
                        this.order.message = r.data.message;
                        this.order.processing = false;
                    }).catch((e) => {
                        this.order.message = 'Unexpected error. It may be due to invalid list. Please modify the list and try again.';
                        this.order.processing = false;
                    });
                },
                orderSatusCheck() {
                    let allParams = this.paramsExceptSelection();

                    if (this.selectedIds.length > 0) {
                        allParams.selected_ids = this.selectedIds.join('|');
                    }
                    this.order.processing = true;
                    this.order.statChecked = false;

                    axios.get(
                        this.orderCheckUrl, {
                            headers: {
                                'X-ACCEPT-MODE': 'only-json'
                            },
                            params: allParams
                        }
                    ).then((r) => {
                        if (this.selectedIds.length > 0) {
                            this.order.selIdsUrlStr = '&selected_ids=' + this.selectedIds.join('|');
                        }
                        this.order.uniqueSymbol = r.data.uniqueSymbol;
                        if (r.data.uniqueSymbol) {
                            this.order.price = Math.round(r.data.price * 100)/100;
                            this.order.chosenSymbol = r.data.symbol;
                        } else {
                            this.order.price = null;
                        }
                        this.order.processing = false;
                        this.order.statChecked = true;
                    }).catch((e) => {
                        console.log(e);
                        this.order.processing = false;
                    });
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
                advSearchStatus() {
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
                    //showAdvSearch = false;
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
                    console.log('conditions');
                    console.log(this.conditions);
                    if (!(this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                        params.adv_search = this.advQueryParams();
                    }
                    console.log('params.adv_search');
                    console.log(params.adv_search);
                    params.items_count = this.itemsCount;
                    params.page = this.paginatorPage;
                    console.log('items count: '+ this.itemsCount);
                    return params;
                },
                async triggerFetch() {
                    let allParams = this.paramsExceptSelection();
                    console.log('tf');
                    axios.get(
                        //this.url, {
                        url, {
                            headers: {
                                'X-ACCEPT-MODE': 'only-json'
                            },
                            params: allParams
                        }
                    ).then((r) => {
                        console.log(r.data);
                        this.results = this.setResults(JSON.parse(r.data.results_json));
                        this.aggregates = JSON.parse(r.data.aggregates);
                        this.totalResults = r.data.total_results;
                        this.currentPage = r.data.current_page;
                        $dispatch('setpagination', {paginator: JSON.parse(r.data.paginator)});
                        $dispatch('routechange', {route: r.data.route});
                        //let temp = JSON.parse(JSON.stringify(this.selectedIds));
                        //this.selectedIds = [];
                        $nextTick(() => {
                            //this.selectedIds = JSON.parse(JSON.stringify(temp));
                        });
                        setTimeout(() => {
                            ajaxLoading = false;
                        }, 200);
                    });
                },
                fetchResults(param) {
                    ajaxLoading = true;
                    this.paginator.currentPage = 1;
                    this.paginatorPage = 1;
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
                    this.paginator.currentPage = 1;
                    this.paginatorPage = 1;
                    ajaxLoading = true;
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
                    console.log(keys);
                    if (detail.data[keys[0]] != '') {
                        this.filters[keys[0]] = detail.data[keys[0]];
                    } else {
                        delete this.filters[keys[0]];
                    }
                    /*
                    if (keys[0] == 'tracked' && detail.data[keys[0]] = 0) {
                        this.filters[keys[0]] = detail.data[keys[0]];
                    } else if(keys[0] == 'category' && detail.data[keys[0]] != 'All') {
                        this.filters[keys[0]] = detail.data[keys[0]];
                    } else {
                        if (typeof(this.filters[keys[0]]) != 'undefined') {
                            delete this.filters[keys[0]];
                        }
                    }*/
                },
                doFilter(detail) {
                    console.log(detail);
                    this.setFilter(detail);
                    ajaxLoading = true;
                    this.paginator.currentPage = 1;
                    this.paginatorPage = 1;
                    this.triggerFetch();
                },
                pageUpdateCount(count) {
                    this.itemsCount = count;
                    console.log('pu count: '+this.itemsCount);
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
                    console.log('iil: '+this.itemIds.length+' tr: '+this.totalResults);
                    if (this.itemIds.length == this.totalResults) {
                        this.allSelected = true;
                    }
                    console.log(this.selectedIds);
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

                    $dispatch('downloadurl', { url_all: this.downloadUrl + '?' + url_all, url_selected: this.downloadUrl + '?' + url_selected, idscount: this.selectedIds.length });
                },
                setResults(results) {
                    results.map((result) => {
                        @foreach($result_calcs as $calc)
                        {{ $calc }}
                        @endforeach
                        return result;
                    });
                    return results;
                },

                getFieldOperators(field) {
                    let f = this.advFields.filter((f) => {
                        return f.key == field;
                    })[0];

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
                },
                initiateOrderForm() {
                    if (this.selectedIds.length > 0) {
                        this.order.selIdsUrlStr = '&selected_ids=' + this.selectedIds.join('|');
                    }
                    showOrderForm = true;
                }
            }" @spotsearch.window="fetchResults($event.detail)"
                @setparam.window="setParam($event.detail)"
                @spotsort.window="console.log('se!');doSort($event.detail)"
                {{-- @setsort.window="setSort($event.detail)" --}}
                @spotfilter.window="console.log('filter event captured');doFilter($event.detail);"
                @setfilter.window="setFilter($event.detail)"
                @countchange.window="pageUpdateCount($event.detail.count);"
                @selectpage="selectPage();"
                @selectall="selectAll();"
                @cancelselection="cancelSelection();"
                @pageselect="processPageSelect();"
                @paginator.window="getPaginatedPage($event.detail.page);"
                @linkaction.window="
                console.log('timer ids ->: ');
                console.log(timers);
                timers.forEach((t) => {
                    clearInterval(t);
                });
                timers = [];
                console.log('timer cleared');
                console.log(timers);
                "
                @advsearch.window="conditions = $event.detail.conditions; advSearchStatus(); runQuery();"
                @showorderform.window="initiateOrderForm();"
                @triggerfetch.window="triggerFetch();"
                x-init="
                    $watch('selectedIds', (ids) => {
                        if (ids.length < itemIds.length) {
                            pageSelected = false;
                            allSelected = false;
                        } else {
                            pageSelected = true;
                        }
                        setDownloadUrl();
                    });
                    {{-- $watch(
                        'profit_margin',
                        () => {
                            results = setResults(JSON.parse(JSON.stringify(results)));
                        }
                    ); --}}

                    console.log('init done');

                    setDownloadUrl();

                    aggregates = JSON.parse(document.getElementById('aggregates').value);

                        url = '{{ $indexUrl }}';
                        let asjson = JSON.parse('{{json_encode($advsearch)}}');
                        console.log('asjson');
                        console.log(asjson);
                        conditions = Object.keys(asjson).map((k) => {
                            return {
                                field: k,
                                type: '',
                                operation: asjson[k][0],
                                value: asjson[k][1]
                            };
                        });
                        params = JSON.parse('{{json_encode($search)}}');
                        sort = JSON.parse('{{json_encode($sort)}}');
                        filters = JSON.parse('{{json_encode($filter)}}');
                        itemsCount = {{ $items_count }};
                        {{-- itemIds = JSON.parse('{{$items_ids}}'); --}}
                        itemIds = JSON.parse(document.getElementById('itemIds').value);
                        {{-- itemIds = document.getElementById('itemIds').value.split(','); --}}
                        selectedIds = [];//$persist([]).as('{{ $unique_str }}ids');

                        pageSelected = false;
                        allSelected = false;
                        pages = [];
                        totalResults = {{ $total_results }};
                        currentPage = {{ $current_page }};
                        downloadUrl = '{{ $downloadUrl }}';
                        results = null;
                        orderBaseUrl = '{{ $orderBaseUrl }}';
                        orderVerifyUrl = '{{ $orderVerifyUrl }}';
                        order.bors = 'Sell';
                        order.qty = 0;
                        order.price = 0.00;
                        order.slippage = 0.01;
                        order.listVerified = false;
                        order.listInvalid = false;
                        order.processing = false;
                        order.message = '';
                        order.selIdsUrlStr = '';

                        paginatorPage = null;

                        let rs = JSON.parse(document.getElementById('results_json').value);
                        {{-- console.log(rs); --}}
                        results = setResults(rs);
                        paginator = JSON.parse('{{$paginator}}');
                        $dispatch('setpagination', {paginator: paginator});


                        if (!initialised) {
                            setTimeout(() => {
                                liveUpdate = true;
                                if (timers.length > 0) {
                                    timers.forEach((t) => {
                                        clearInterval(t);
                                    });
                                }
                                timers = [];
                                let t = setInterval(() => {
                                    if (liveUpdate) {
                                        triggerFetch();
                                    }
                                }, 6000);
                                timers.push(t);
                            initialised = true;
                            }, 1000);
                        }
                "
                action="#"
                class="max-w-full">
                <div x-show="selectedIds.length > 0" x-transition class="max-w-full">
                    <div colspan="{{ $total_disp_cols }}" class="text-center bg-warning text-base-200 p-2 rounded-sm">
                        <span x-text="selectedIds.length" class="font-bold"></span>
                        &nbsp;<span class="font-bold">item<span x-show="selectedIds.length > 1">s</span>
                            selected.</span>
                        &nbsp;<button type="button" @click.prevent.stop="$dispatch('selectpage');" class="btn btn-xs"
                            :disabled="pageSelected">Select Page</button>
                        &nbsp;<button type="button" @click.prevent.stop="$dispatch('selectall')" class="btn btn-xs" :disabled="allSelected">Select All
                            {{ $total_results }} items</button>
                        &nbsp;<button type="button" @click.prevent.stop="$dispatch('cancelselection')"
                            class="btn btn-xs">Cancel All</button>
                        </div>
                </div>
                <div class="overflow-x-scroll scroll-m-1 relative max-w-full p-0 m-0 rounded-md">
                    <table class="table min-w-200 w-full border-2 border-base-200 rounded-md"
                        :class="compact ? 'table-mini' : 'table-compact'">

                        <thead>
                            <tr>
                                @if ($selectionEnabled)
                                    <th class="w-7">
                                        <input type="checkbox" x-model="pageSelected" @change="$dispatch('pageselect');"
                                            class="checkbox checkbox-xs"
                                            :class="!allSelected ? 'checkbox-primary' : 'checkbox-secondary'">
                                    </th>
                                @endif
                                {{ $thead }}
                            </tr>
                            @if (isset($aggregateCols))
                            <tr class="text-warning font-bold">
                                {{ $aggregateCols }}
                            </tr>
                            @endif
                        </thead>
                        <tbody>
                            {{ $rows }}
                        </tbody>
                    </table>
                </div>
                @if ($enableAdvSearch)
                <div  x-data="{
                        myconditions: [{
                            field: 'none',
                            type: '',
                            operation: 'none',
                            value: 0
                        }],
                        addContition() {
                            this.myconditions.push({
                                field: 'none',
                                type: '',
                                operation: 'none',
                                value: 0
                            });
                        },
                        resetAdvSearch() {
                            this.myconditions = [{
                                field: 'none',
                                operation: 'none',
                                value: 0
                            }];

                        },
                    }"
                    x-show="showAdvSearch" x-transition
                    class="absolute top-0 left-0 z-30 w-full flex flex-row justify-center p-16 items-start bg-base-100 bg-opacity-60 min-h-full">
                        <div
                            class="flex flex-col items-center px-4 py-6 rounded-md w-2/3 mx-auto bg-base-200 shadow-lg relative">
                            <button @click.prevent.stop="showAdvSearch = false;advSearchStatus();"
                                class="w-8 h-8 p-1 bg-base-100 hover:bg-base-300 hover:text-warning transition-colors text-base-content rounded-md flex flex-row items-center justify-center absolute top-2 right-2">
                                <x-display.icon icon="icons.close" height="h-7" width="w-7" />
                            </button>
                            <div class="w-full flex flex-row justify-center">
                                <h3 class="text-lg font-bold mb-4">Advanced Search</h3>
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
                            <template x-for="(condition, index) in myconditions" :key="'con'+index">
                                <div class="w-full flex flex-row justify-center my-2">
                                    <div class="w-full flex-1 mx-1">
                                        <select x-model="condition.field" :id="'advf' + index"
                                            class="select select-sm select-bordered py-0 w-full"
                                            @change.prevent.stop="document.getElementById('advop'+index).dispatchEvent(new Event('change', { 'bubbles': false }));">
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
                                        <button @click.prevent.stop="myconditions.splice(index, 1);"
                                            class="w-6 h-6 p-1 bg-error text-base-content rounded-md flex flex-row items-center justify-center disabled:bg-opacity-70" :disabled="myconditions.length == 1">
                                            <x-display.icon icon="icons.close" height="h-5" width="w-5" />
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
                                    <button @click.prevent.stop="showAdvSearch = false;$dispatch('advsearch', {conditions: JSON.parse(JSON.stringify(myconditions))})"
                                        class="btn btn-sm btn-success p-0 w-full border border-base-100 flex felx-row items-center justify-center">
                                        <x-display.icon icon="icons.go_right" height="h-4" width="w-4" />&nbsp;Get Items List
                                    </button>
                                </div>
                                <div class="w-10 px-2 flex flex-row items-center">
                                    <button @click.prevent.stop="resetAdvSearch();$dispatch('advsearch', {conditions: JSON.parse(JSON.stringify(myconditions))});"
                                        class="w-6 h-6 p-1 bg-error text-base-content rounded-md flex flex-row items-center justify-center">
                                        <x-display.icon icon="icons.delete" height="h-5" width="w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- Order Form --}}
                    @if ($enableOrderform)
                    <div x-show="showOrderForm" x-transition
                        class="absolute top-0 left-0 z-30 w-full flex flex-row justify-center p-16 items-start bg-base-100 bg-opacity-60 min-h-full">
                        <div class="flex flex-col items-center px-4 py-6 rounded-md w-2/3 mx-auto bg-base-200 shadow-lg relative">
                            <button @click.prevent.stop="showOrderForm = false;"
                                class="w-8 h-8 p-1 bg-base-100 hover:bg-base-300 hover:text-warning transition-colors text-base-content rounded-md flex flex-row items-center justify-center absolute top-2 right-2">
                                <x-display.icon icon="icons.close" height="h-7" width="w-7" />
                            </button>
                            <div class="w-full flex flex-row justify-center">
                                <h3 class="text-lg font-bold mb-4">Create Sell Order</h3>
                            </div>
                            <div class="w-full justify-center items-center rounded-md">
                                <h6 class="text-sm p-4">
                                    This order will be generated for <span class="font-bold text-warning text-lg" x-text="getOrderItemsCount()"></span> items.<br/>
                                    {{-- <span x-show="order.uniqueSymbol">
                                        Only one script in your list. You can set the price at which to create sell order. The current market price is chosen by default.
                                    </span>
                                    <span x-show="!order.uniqueSymbol">
                                        More than one script in your list. You cannot set the price at which to create the sell order. The current market price will be taken to generate the sell order.
                                    </span> --}}
                                </h6>
                                <h6 x-show="order.uniqueSymbol" class="text-sm p-4">
                                    Chosen Script: <span x-text="order.chosenSymbol" class="font-bold text-warning"></span>
                                </h6>
                            </div>
                            {{-- <div x-show="!order.statChecked" x-transition class="w-full border border-base-content border-opacity-30 rounded-md">
                                <h6 class="w-full p-3 text-center animate-pulse">
                                    Analysing the list..
                                </h6>
                            </div> --}}
                            <div x-transition class="w-full border border-base-content border-opacity-30 rounded-md">
                                <div class="flex flex-row justify-between w-full mx-auto p-4 m-4 space-x-2">
                                    {{-- <div class="w-1/4">
                                        <label for="bors">Action</label><br/>
                                        <select x-model="order.bors" id="bors" class="select select-sm py-0 w-full">
                                            <option value="Buy">Buy</option>
                                            <option value="Sell">Sell</option>
                                        </select>
                                    </div> --}}
                                    <div class="flex-1">
                                        <label for="order_qty">Quantity %</label><br/>
                                        <input x-model="order.qty" id="order_qty" type="number" min="0" class="input input-sm w-full" oninput="if (this.value < 0) {this.value = 0;} if (this.value.length != 0) {var val = Math.floor(this.value); this.value = null; this.value = val;}">
                                    </div>
                                    @if (isset($soPriceField) && $soPriceField)
                                    <div class="flex-1">
                                        <label for="order_price">Price</label><br/>
                                        <input x-model="order.price" type="number" min="0.00" step="0.01" id="order_price" type="text" class="input input-sm w-full"
                                        oninput="if(this.value < 0) {this.value = 0.00;}"
                                        @showorderform.window="order.price=results[0].cmp;">
                                    </div>
                                    @endif
                                    <div class="flex-1">
                                        <label for="order_slippage">Slippage</label><br/>
                                        <input x-model="order.slippage" type="number" min="0.00" max="2.00" step="0.01" id="order_slippage" type="text" class="input input-sm w-full" :class="order.slippage < 0.01 || order.slippage > 1 ? 'text-error border border-error' : ''" >
                                        <label class="label">
                                            <span class="label-text-alt" :class="order.slippage < 0.01 ? 'text-error' : ''">Min: 0.01</span>
                                            <span class="label-text-alt" :class="order.slippage > 1 ? 'text-error' : ''">Max: 1.00</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="my-4 px-1 text-center w-full flex flex-row justify-between space-x-4">
                                    <button @click.prevent.stop="order.reset();" :disabled="order.processing" class="btn btn-sm text-base-content"><x-display.icon icon="icons.refresh" height="h-4" width="w-4" />&nbsp;Reset</button>
                                    {{-- <button @click.prevent.stop="verifyOrderList()" x-show="!order.listVerified" :disabled="order.processing"
                                        class="btn btn-sm btn-warning py-0 border border-base-100 flex felx-row items-center justify-center mx-auto" download :disabled="!order.enabled">
                                        <x-display.icon icon="icons.doc_tick" height="h-4" width="w-4" />&nbsp;Verify Items List
                                    </button> --}}
                                    <a :href="order.url"
                                        class="btn btn-sm btn-success py-0 border border-base-100 flex felx-row items-center justify-center mx-auto" download :disabled="!order.enabled || order.processing">
                                        <x-display.icon icon="icons.doc_tick" height="h-4" width="w-4" />&nbsp;Generate Order
                                    </a>
                                </div>
                                {{-- <div x-show="order.listVerified" class="p-3 m-3 border rounded-md"
                                    :class="order.listInvalid ? 'border-error text-error' : 'border-success text-success'">
                                    <span x-text="order.message"></span>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </form>

        </div>
        <div class="my-4 p-2">
            <x-utils.paginator />
        </div>
    </div>
</x-dashboard-base>
