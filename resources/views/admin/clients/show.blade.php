<x-utils.panelbase
    :x_ajax="$x_ajax"
    title="Client wise Details"
    results_name="clientscripts"
    indexUrl="{{route('clients.show', $model->id)}}"
    downloadUrl="{{route('clients.show.download', $model->id)}}"
    selectIdsUrl="{{route('clients.show.selectIds', $model->id)}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$scripts"
    :results_json="$results_json"
    total_disp_cols="19"
    unique_str="clxone"
    adv_fields="
        pc_change: {key: 'pc_change', text: '% Change', type: 'numeric'},
        pa: {key: 'pa', text: 'Allocation %', type: 'numeric'},
        sector: {key: 'sector', text: 'Sector', type: 'string'},
        nof_days: {key: 'nof_days', text: 'No. of Days', type: 'numeric'},
    "
    :paginator="$paginator"
    :enableAdvSearch="true"
    :soPriceField="false"
    orderBaseUrl="{{route('clients.order.download', $model->id)}}"
    id="clients_show"
    :editbtn="true"
    editroute="clients.import.store"
    :model_id="$model->id"
    :advsearch="$advparams"
    :search="$params"
    :sort="$sort"
    :filter="$filter"
    >
    <x-slot:searchbox>
        <x-utils.itemssearch
        itemsName="clients"
        url="{{route('clients.show', 0)}}"
        searchUrl="{{route('clients.list')}}"
        routeName="clients.show"
        :searchDisplayKeys="['code', 'name']"
        />
    </x-slot>
    <x-slot:body>
    <div class="flex flex-row flex-wrap space-x-2 items-center">
        <div class="font-bold border border-base-300 rounded-md p-4 mb-2 md:mb-0">
            <div>
                <span class="inline-block mr-1">Code: </span><span class="inline-block mr-4 text-warning">{{$model->client_code}}</span>
                <span class="inline-block mr-1">Name: </span><span class="inline-block mr-4 text-warning">{{$model->name}}</span>
                <span class="inline-block mr-1">RM: </span><span class="inline-block mr-4 text-warning">{{$model->dealer->name}}</span>
                <span class="inline-block mr-1">AUM: </span><span class="inline-block mr-4 text-warning">{{$model->total_aum}}</span>
            </div>
        </div>
        <div>
            <button class="btn btn-sm btn-ghost text-warning" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.edit', $model->id)}}', route: 'clients.edit', fresh: true});">
                <x-display.icon icon="icons.view_on" height="h-5" width="w-5"/>
            </button>
        </div>
        <div>
            <button @click.prevent.stop="$dispatch('newcs');" class="btn btn-sm py-1">
                <span>Add Script</span>
                <x-display.icon icon="icons.plus" height="h-5" width="w-5"/>
            </button>
        </div>
        {{-- <x-utils.itemssearch
        itemsName="clients"
        url="{{route('clients.show', 0)}}"
        searchUrl="{{route('clients.list')}}"
        routeName="clients.show"
        :searchDisplayKeys="['code', 'name']"
        /> --}}

    </div>
    </x-slot><x-slot:inputFields>
        <input type="hidden" value="{{$aggregates}}" id="aggregates">
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
    </x-slot>
    <x-slot:aggregateCols>
        <th colspan="2">
            Totals:
        </th>
        <th class=" sticky !left-12"></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pa, 2)"></span></th>
        <th></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_amt_invested)"></span></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_cur_value)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_overall_gain)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pc_change, 2)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_todays_gain)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_todays_gain_pc, 2)"></span></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </x-slot>
    <x-slot:thead>
        <th class="sticky !left-6">
            Sl. No.
        </th>
        <th class="text-center border-l-2 border-base-100 sticky !left-12">
            <div class="flex flex-row items-center w-48">
                <x-utils.spotsort name="symbol" val="{{ $sort['symbol'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Symbol
                    <x-utils.spotsearch textval="{{ $params['symbol'] ?? '' }}" textname="symbol"
                        label="Search symbol" />
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">PA %</th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="qty" val="{{ $sort['qty'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Qty
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="buy_avg_price" val="{{ $sort['buy_avg_price'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Avg. Buy Rate
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="amt_invested" val="{{ $sort['amt_invested'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Buy Value
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="cmp" val="{{ $sort['cmp'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    CMP
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="cur_value" val="{{ $sort['cur_value'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cur Value
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="overall_gain" val="{{ $sort['overall_gain'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Overall Gain
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="pc_change" val="{{ $sort['pc_change'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    % Change
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="todays_gain" val="{{ $sort['todays_gain'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Day's Gain
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="todays_gain_pc" val="{{ $sort['todays_gain_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Day's Gain %
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="day_high" val="{{ $sort['day_high'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Day High
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="day_low" val="{{ $sort['day_low'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Day Low
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="impact" val="{{ $sort['impact'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Impact
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="nof_days" val="{{ $sort['nof_days'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    No. of days
                </div>
            </div>
        </th>
        <th class="relative w-52 border-l-2 border-base-100">DOP</th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center w-48">
                <x-utils.spotsort name="industry" val="{{ $sort['industry'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Industry
                    <x-utils.spotsearch textval="{{ $params['industry'] ?? '' }}" textname="industry"
                        label="Search industry" />
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <div class="relative flex-grow ml-2 flex flex-row items-center justify-between">
                    {{-- <span>Tracked ?</span> --}}
                    {{-- <x-utils.spotfilter
                        :options="[
                            ['key' => -1, 'text' => 'All', true],
                            ['key' => 0, 'text' => 'Untracked'],
                            ['key' => 1, 'text' => 'Tracked']
                        ]"
                        selectedoption=-1
                        fieldname="tracked"
                    /> --}}
                    <select x-data="{
                        'val': 1
                        }"
                        @change.stop.prevent="$dispatch('spotfilter', {data: {tracked: val}});"
                        x-model="val" class="select select-bordered select-sm max-w-xs py-0 m-1"
                        :class="val == -1 || 'text-accent'">
                        @foreach ([
                            ['key' => -1, 'text' => 'Tacked/Untracked'],
                            ['key' => 0, 'text' => 'Untracked'],
                            ['key' => 1, 'text' => 'Tracked', 'selected' => true]
                        ] as $item)
                            <option value="{{ $item['key'] }}">
                                {{ $item['text'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </th>
    </x-slot>

    <x-slot:rows>
        {{-- @foreach ($scripts as $result) --}}
        <template x-for="(result, index) in results">
            <tr>
                {{-- @if ($selectionEnabled) --}}
                <td><input type="checkbox" :value="result.id" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs"></td>
                <td class="sticky !left-6" x-text="itemsCount * (currentPage - 1) + index + 1"></td>
                {{-- @endif --}}
                {{-- {{$rows}} --}}
                <td class="sticky !left-12">
                    <div class="flex flex-row justify-between items-center">
                        <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('scripts.show', 0)}}'.replace('0', result.id), route: 'scripts.show'})"
                            class="link no-underline hover:underline" href="" x-text="result.symbol"></a>
                        <div class="flex flex-row">
                            <button @click.prevent.stop="$dispatch('deletecs', {script_id: result.id});" class="btn btn-xs btn-ghost text-error">
                                <x-display.icon icon="icons.delete" height="h-4" width="w-4"/>
                            </button>
                            <button @click.prevent.stop="$dispatch('editcs', {symbol: result.symbol, script_id: result.id, qty: result.qty, buy_avg_price: result.buy_avg_price});" class="btn btn-xs btn-ghost text-warning">
                                <x-display.icon icon="icons.edit" height="h-4" width="w-4"/>
                            </button>
                        </div>
                    </div>
                        <div class="h-full w-1 absolute top-0 right-0 border-r border-base-300"></div>
                </td>
                <td class="text-right" x-text="formatted(result.pa, 2)"></td>
                <td class="text-right" x-text="result.qty"></td>
                <td class="text-right" x-text="formatted(result.buy_avg_price)"></td>
                <td class="text-right" x-text="formatted(result.amt_invested)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                    :class="{'text-error' : result.cmp < result.buy_avg_price, 'text-accent' : result.cmp > result.buy_avg_price}">
                        <span x-text="formatted(result.cmp, 2)"></span>
                        <x-display.icon x-show="result.cmp > result.buy_avg_price" icon="icons.up-arrow"/>
                        <x-display.icon x-show="result.cmp < result.buy_avg_price" icon="icons.down-arrow"/>
                    </div>
                </td>
                <td class="text-right" x-text="formatted(result.cur_value, 2)"
                :class="{'text-error' : result.cur_value < result.amt_invested, 'text-accent' : result.cur_value > result.amt_invested}"></td>
                <td class="text-right" :class="{'text-error' : result.overall_gain < 0, 'text-accent' : result.overall_gain > 0}" x-text="formatted(result.overall_gain)"></td>
                <td class="text-right" :class="{'text-error' : result.pc_change < 0, 'text-accent' : result.pc_change > 0}" x-text="formatted(result.pc_change, 2)"></td>
                <td class="text-right" :class="{'text-error' : result.todays_gain < 0, 'text-accent' : result.todays_gain > 0}" x-text="formatted(result.todays_gain)"></td>
                <td class="text-right" :class="{'text-error' : result.todays_gain_pc < 0, 'text-accent' : result.todays_gain > 0}" x-text="formatted(result.todays_gain_pc, 2)"></td>
                <td class="text-right" x-text="formatted(result.day_high)"></td>
                <td class="text-right" x-text="formatted(result.day_low)"></td>
                <td class="text-right" :class="{'text-error' : result.impact < 0, 'text-accent' : result.impact > 0}" x-text="formatted(result.impact, 2)"></td>
                <td class="text-center" x-text="result.nof_days"></td>
                <td x-text="result.entry_date"></td>
                <td x-text="result.industry"></td>
                <td class="text-center" x-text="result.tracked ? 'Tracked' : 'Untracked'"></td>
            </tr>
        </template>
        {{-- @endforeach --}}
    <div x-data="{
            show: false,
            sid: null,
            cid: null,
            success: false,
            doDelete() {
                axios.post(
                    '{{route('clients.script.delete', $model->id)}}',
                    {
                        script_id: this.sid
                    }
                ).then((r) => {
                    this.success = true;
                    $dispatch('triggerfetch');
                }).catch((e) => {
                    console.log(e);
                });
            }
        }"
        x-init="
            cid = {{$model->id}};
        "
        @deletecs.window="
            show = true;
            sid = $event.detail.script_id;
        "
        x-show="show"
        class="fixed z-50 top-0 left-0 flex flex-row justify-center items-center w-full h-full bg-base-200 bg-opacity-70">
        <div x-show="!success" class="bg-base-200 p-4 border border-base-300 rounded-md relative">
            <div class="w-full text-right">
                <button @click.prevent.stop="show = false;" class="btn btn-xs btn-ghost text-warning">
                    <x-display.icon icon="icons.close" height="h-4" width="w-4"/>
                </button>
            </div>
            <div class="flex flex-col space-y-4">
                <div class="p-3">This will remove the script from the client's portfolio. Are you sure to continue?</div>
                <div class="flex flex-row justify-center space-x-2">
                    <button @click.prevent.stop="show=false;" class="btn btn-sm">No</button>
                    <button @click.prevent.stop="doDelete();" class="btn btn-sm btn-warning">Yes</button>
                </div>
            </div>
        </div>
        <div x-show="success" class="bg-base-200 p-4 border border-base-300 rounded-md relative flex flex-col justify-center items-center space-y-4">
            <span>The script was deleted!</span>
            <button @click.prevent.stop="success=false; show=false; sid=null;" class="btn btn-sm btn-success">Ok</button>
        </div>
    </div>
    <!--Add Script Form-->
    <div x-data="{
            client_id: {{$model->id}},
            symbol: '',
            qty: 0,
            buy_avg_price: 0,
            show: false,
            result: 0,
            error: '',
            get formvalid() {
                return this.qty.toString().length > 0 &&
                    this.buy_avg_price.toString().length > 0;
            },
            reset() {
                this.symbol = '';
                this.qty = 0;
                this.buy_avg_price = 0;
                this.show = false;
                this.result = 0;
                this.error = '';
            },
            createcs() {
                let params = {
                    symbol: this.symbol,
                    qty: this.qty,
                    buy_avg_price: this.buy_avg_price
                };
                axios.post(
                    '{{route('clients.script.create', $model->id)}}',
                    params
                ).then((r) => {
                    if (r.data.success) {
                        this.result = 1;
                        $dispatch('triggerfetch');
                    } else {
                        this.result = -1;
                        console.log(Object.values(r.data.error));

                        let estr = Object.values(r.data.error).reduce((s, e) => {
                            e.forEach((ers) => {
                                if (s.length > 0) {
                                    return s+', '+ers;
                                }
                                else {
                                    return s + ers;
                                }
                            });
                            console.log(e);
                            return s;
                        });
                        console.log(estr);
                        this.error = estr;
                    }
                }).catch((e) => {
                    this.result = -1;
                    this.error = 'Please make sure the symbol you have entered is already there in the system.';
                    console.log(e);
                });
            }
        }"
        x-show="show"
        @newcs.window="
            show = true;
        "
        class="fixed z-50 top-0 left-0 flex flex-row justify-center items-center w-full h-full bg-base-200 bg-opacity-70"
        >
        <div class="bg-base-200 p-4 border border-base-300 rounded-md relative">
            <div class="w-full text-right">
                <button @click.prevent.stop="reset();" class="btn btn-xs btn-ghost text-warning">
                    <x-display.icon icon="icons.close" height="h-4" width="w-4"/>
                </button>
            </div>
            <h3 class="text-center m-3 text-lg underline">Add A Script</h3>
            <form action="" class="max-w-md relative">
                <div x-show="result == 1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                    <div class="text-success">Script added.</div>
                    <div class="flex flex-row justify-evenly space-x-4">
                        <a href="#" @click.prevent.stop="result = 0; show=false; $dispatch('triggerfetch');" class="btn btn-sm capitalize">Ok</a>
                    </div>
                </div>
                <div x-show="result == -1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                    <div class="text-error">Couldn't create script:</div>
                    <div class="flex flex-col space-y-4 justify-center items-center space-x-4 px-4">
                        <span x-text="error"></span>
                        <button @click.prevent.stop="result = 0;" class="btn btn-sm btn-error capitalize">Ok</button>
                    </div>
                </div>
                <div class="form-control w-60 max-w-md my-3 relative">
                    <label class="label mb-0 pb-0">
                    <span class="label-text">Symbol</span>
                    </label>
                    <x-utils.suggestlist
                        xmodel_name="symbol"
                        itemsName="scripts"
                        searchUrl="{{route('scripts.list')}}"
                        :searchDisplayKeys="['symbol']"
                        valueKey="symbol"
                        />
                </div>

                <div class="form-control w-60 max-w-md my-3 relative">
                    <label class="label mb-0 pb-0">
                    <span class="label-text">Qty</span>
                    </label>
                    <input x-model="qty" type="number" class="input input-bordered w-full max-w-md read-only:bg-base-200"/>
                </div>
                <div class="form-control w-60 max-w-md my-3">
                    <label class="label mb-0 pb-0">
                    <span class="label-text">Buy Avg. Price</span>
                    </label>
                    <input x-model="buy_avg_price"  type="number" step="0.01" class="input input-bordered w-full max-w-md read-only:bg-base-200"/>
                </div>
                <div class="w-full mt-6 mb-3 text-center">
                    <button @click.prevent.stop="createcs();" type="submit" class="btn btn-primary btn-sm" :disabled="!formvalid">Add</button>
                </div>
            </form>
        </div>
    </div>
    <!--Edit Script Form-->
    <div x-data="{
            client_id: {{$model->id}},
            symbol: '',
            script_id: 0,
            qty: 0,
            buy_avg_price: 0,
            show: false,
            result: 0,
            error: '',
            get formvalid() {
                return this.qty.toString().length > 0 &&
                    this.buy_avg_price.toString().length > 0;
            },
            updatecs() {
                let params = {
                    script_id: this.script_id,
                    qty: this.qty,
                    buy_avg_price: this.buy_avg_price
                };
                axios.post(
                    '{{route('clients.script.update', $model->id)}}',
                    params
                ).then((r) => {
                    if (r.data.success) {
                        this.result = 1;
                    } else {
                        this.result = -1;
                        console.log(Object.values(r.data.error));

                        let estr = Object.values(r.data.error).reduce((s, e) => {
                            e.forEach((ers) => {
                                if (s.length > 0) {
                                    return s+', '+ers;
                                }
                                else {
                                    return s + ers;
                                }
                            });
                            console.log(e);
                            return s;
                        });
                        console.log(estr);
                        this.error = estr;
                    }
                }).catch((e) => {
                    this.result = -1;
                    this.error = 'Sorry, something went wrong.';
                    console.log(e);
                });
            }
        }"
        x-show="show"
        @editcs.window="
        show = true;
        symbol = $event.detail.symbol;
        script_id = $event.detail.script_id;
        qty = $event.detail.qty;
        buy_avg_price = $event.detail.buy_avg_price;
        "
        class="fixed z-50 top-0 left-0 flex flex-row justify-center items-center w-full h-full bg-base-200 bg-opacity-70"
        >
        <div class="bg-base-200 p-4 border border-base-300 rounded-md relative">
            <div class="w-full text-right">
                <button @click.prevent.stop="show = false;" class="btn btn-xs btn-ghost text-warning">
                    <x-display.icon icon="icons.close" height="h-4" width="w-4"/>
                </button>
            </div>
            <form action="" class="max-w-md relative">
                <div x-show="result == 1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                    <div class="text-success">Client updated successfully!</div>
                    <div class="flex flex-row justify-evenly space-x-4">
                        <a href="#" @click.prevent.stop="result = 0; show=false; $dispatch('triggerfetch'); /*$dispatch('linkaction', {link: '{{route('clients.show', $model->id)}}', route: 'clients.show'})*/" class="btn btn-sm capitalize">Ok</a>
                    </div>
                </div>
                <div x-show="result == -1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                    <div class="text-error">Upadte failed:</div>
                    <div class="flex flex-col space-y-4 justify-center items-center space-x-4">
                        <span x-text="error"></span>
                        <button @click.prevent.stop="result = 0;" class="btn btn-sm btn-error capitalize">Ok</button>
                    </div>
                </div>
                <label class="block w-full font-bold text-center" x-text="symbol"></label>
                <div class="form-control w-60 max-w-md my-3 relative">
                    <label class="label mb-0 pb-0">
                    <span class="label-text">Qty</span>
                    </label>
                    <input x-model="qty" type="number" class="input input-bordered w-full max-w-md read-only:bg-base-200"/>
                </div>
                <div class="form-control w-60 max-w-md my-3">
                    <label class="label mb-0 pb-0">
                    <span class="label-text">Buy Avg. Price</span>
                    </label>
                    <input x-model="buy_avg_price"  type="number" step="0.01" class="input input-bordered w-full max-w-md read-only:bg-base-200"/>
                </div>
                <div class="w-full mt-6 mb-3 text-center">
                    <button @click.prevent.stop="updatecs();" type="submit" class="btn btn-primary btn-sm" :disabled="!formvalid">Update</button>
                </div>
            </form>
        </div>
    </div>
    </x-slot>
</x-utils.panelbase>
