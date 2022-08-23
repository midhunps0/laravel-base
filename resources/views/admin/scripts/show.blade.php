<x-utils.panelbase
    :x_ajax="$x_ajax"
    title="Scriptwise Details"
    results_name="scriptclients"
    indexUrl="{{route('scripts.show', $model->id)}}"
    downloadUrl="{{route('scripts.show.download', $model->id)}}"
    selectIdsUrl="{{route('scripts.show.selectIds', $model->id)}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$clients"
    :results_json="$results_json"
    total_disp_cols="13"
    unique_str="scrx"
    adv_fields="
        code: {key: 'code', text: 'Client Code', type: 'string'},
        pnl_pc: {key: 'pnl_pc', text: 'PNL %', type: 'numeric'},
        pa: {key: 'pa', text: 'Allocation %', type: 'numeric'},
        nof_days: {key: 'nof_days', text: 'No. of Days', type: 'numeric'},
    "
    :enableAdvSearch="true"
    :soPriceField="true"
    :paginator="$paginator"
    :columns="[
        'client_code', 'cmp', 'pnl'
    ]"
    orderBaseUrl="{{route('scripts.order.download', $model->id)}}"
    orderVerifyUrl="{{route('clientsripts.sellorder.verify')}}"
    id="scripts_show"
    :editbtn="true"
    editroute="scripts.import.store"
    :model_id="$model->id"
    >
    <x-slot:searchbox>
        <x-utils.itemssearch
            itemsName="scripts"
            url="{{route('scripts.show', 0)}}"
            searchUrl="{{route('scripts.list')}}"
            routeName="scripts.show"
            :searchDisplayKeys="['symbol', 'company_name']"
            />
    </x-slot>
    <x-slot:body>
        <div class="flex flex-row flex-wrap space-x-2 items-center">
            <div class="font-bold border border-base-300 rounded-md p-4 mb-2 md:mb-0">
                <h1><span class="inline-block mr-1">Symbol: </span><span class="inline-block mr-4 text-warning">{{$model->symbol}}</span><span class="inline-block mr-1">Company Name: </span><span class="inline-block mr-4 text-warning">{{$model->company_name}}</span></h1>
            </div>
            <div>
                <button class="btn btn-sm btn-ghost text-warning" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('scripts.edit', $model->id)}}', route: 'scripts.edit', fresh: true});">
                    <x-display.icon icon="icons.view_on" height="h-5" width="w-5"/>
                </button>
            </div>
        </div>
    </x-slot>
    <x-slot:inputFields>
        <input type="hidden" value="{{$aggregates}}" id="aggregates">
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
    </x-slot>
    <x-slot:aggregateCols>
        <th colspan="3">
            Totals:
        </th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pa, 2)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_qty)"></span></th>
        <th></th>
        <th></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_cur_val)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pnl)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pnl_pc, 2)"></span></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_impact, 2)"></span></th>
        <th></th>
    </x-slot>
    <x-slot:thead>
        <th class="relative text-center">
            Sl. No.
        </th>
        <th class="text-center border-l-2 border-base-100 sticky !left-6">
            <div class="flex flex-row items-center w-44">
                <x-utils.spotsort name="client_code" val="{{ $sort['client_code'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2 text-left">
                    Client Code
                    <x-utils.spotsearch textval="{{ $params['client_code'] ?? '' }}" textname="client_code"
                        label="Search client code" />
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <div class="relative flex-grow ml-2 text-left">
                    Symbol
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="pa" val="{{ $sort['pa'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Allocation %
                </div>
            </div>
        </th>
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
                    Avg Buy Rate
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="buy_val" val="{{ $sort['buy_val'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Avg Buy Value
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
                <x-utils.spotsort name="cur_val" val="{{ $sort['cur_val'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cur Value
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="pnl" val="{{ $sort['pnl'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    PNL
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="pnl_pc" val="{{ $sort['pnl_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    PNL %
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="nof_days" val="{{ $sort['nof_days'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    No of days
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="impact" val="{{ $sort['impact'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Impact %
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center w-44">
                <x-utils.spotsort name="category" val="{{ $sort['category'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2 text-left">
                    Type
                </div>
            </div>
        </th>
    </x-slot>

    <x-slot:rows>
        {{-- @foreach ($clients as $result) --}}
        <template x-for="(result, index) in results">
            <tr>
                {{-- @if ($selectionEnabled) --}}
                <td><input type="checkbox" :value="result.id" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs"></td>
                <td x-text="itemsCount * (currentPage - 1) + index + 1"></td>
                {{-- @endif --}}
                {{-- {{$rows}} --}}
                <td class="sticky !left-6" >
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.show', 0)}}'.replace('0', result.id), route: 'clients.show'})"
                        class="link no-underline hover:underline" href="" x-text="result.code"></a>
                        <div class="h-full w-1 absolute top-0 right-0 border-r border-base-300"></div>
                </td>
                <td x-text="result.symbol"></td>
                <td class="text-right" x-text="formatted(result.pa, 2)"></td>
                <td class="text-right" x-text="result.qty"></td>
                <td class="text-right" x-text="formatted(result.buy_avg_price)"></td>
                <td class="text-right" x-text="formatted(result.buy_val)"></td>
                <td class="flex flex-row items-baseline justify-end" :class="result.cmp < result.buy_avg_price ? 'text-error' : 'text-accent'">
                    <x-display.icon x-show="result.cmp >= result.buy_avg_price" icon="icons.up-arrow"/>
                    <x-display.icon x-show="result.cmp < result.buy_avg_price" icon="icons.down-arrow"/>
                    <span x-text="formatted(result.cmp, 2)"></span>
                </td>
                <td class="text-right" x-text="formatted(result.cur_val)"></td>
                <td class="flex flex-row items-baseline justify-end" :class="result.pnl < 0 ? 'text-error' : 'text-accent'">
                    {{-- <x-display.icon x-show="result.pnl >= 0" icon="icons.up-arrow"/>
                    <x-display.icon x-show="result.pnl < 0" icon="icons.down-arrow"/> --}}
                    <span x-text="formatted(result.pnl)"></span>
                </td>
                <td class="text-right" x-text="formatted(result.pnl_pc, 2)"></td>
                <td class="text-center" x-text="result.nof_days"></td>
                <td class="flex flex-row items-baseline justify-end" :class="result.impact < 0 ? 'text-error' : 'text-accent'">
                    {{-- <x-display.icon x-show="result.impact >= 0" icon="icons.up-arrow"/>
                    <x-display.icon x-show="result.impact < 0" icon="icons.down-arrow"/> --}}
                    <span x-text="formatted(result.impact, 2)"></span>
                </td>
                <td x-text="result.category"></td>
            </tr>
        </template>
        {{-- @endforeach --}}
    </x-slot>
</x-utils.panelbase>