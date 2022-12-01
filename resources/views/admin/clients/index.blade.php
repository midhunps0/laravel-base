<x-utils.panelbase
    :x_ajax="$x_ajax"
    title="Clients"
    indexUrl="{{route('clients.index')}}"
    downloadUrl="{{route('clients.download')}}"
    selectIdsUrl="{{route('clients.selectIds')}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$clients"
    results_name="clients"
    unique_str="clnx"
    :results_json="$results_json"
    :paginator="$paginator"
    total_disp_cols="15"
    adv_fields="
        none: {key: 'none', text: 'Select A Field', type: 'none'},
        client_code: {key: 'client_code', text: 'Client Code', type: 'string'},
        name: {key: 'name', text: 'Client Name', type: 'string'},
        allocated_aum: {key: 'allocated_aum', text: 'ALCTD AUM', type: 'numeric'},
        aum: {key: 'aum', text: 'AUM', type: 'numeric'},
    "
    id="clients_index">
    <x-slot:inputFields>
        <input type="hidden" value="{{$aggregates}}" id="aggregates">
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
    </x-slot>
    <x-slot:aggregateCols>
        <th colspan="2" class="sticky !left-0">
            Totals:
        </th>
        <th class="sticky !left-36 z-20"></th>
        <th class="sticky !left-72 z-30"></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_aum)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_allocated_aum)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pa, 2)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_cur_value)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pnl)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pnl_pc, 2)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_realised_pnl)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_liquidbees)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_cash)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_cash_pc, 2)"></span></th>
        <td class="text-right"><span x-text="formatted(aggregates.agr_ledger_balance)"></span></td>
        <th class="text-right"><span x-text="formatted(aggregates.agr_returns)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_returns_pc, 2)"></span></th>
        <th class="text-right"></th>
    </x-slot>
    <x-slot:thead>
        <th class="sticky !left-6 w-32">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="client_code" val="{{ $sort['client_code'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Code
                    <x-utils.spotsearch textval="{{ $params['client_code'] ?? '' }}" textname="client_code"
                        label="Search client code" />
                </div>
            </div>
        </th>
        <th class="w-36 border-l-2 border-base-100 sticky !left-36 z-20">
            <div class="flex flex-row items-center w-36">
                <x-utils.spotsort name="name" val="{{ $sort['name'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Name
                    <x-utils.spotsearch textval="{{ $params['name'] ?? '' }}" textname="name"
                        label="Search name" />
                </div>
            </div>
        </th>
        <th class="border-l-2 border-base-100 sticky !left-72 z-30">
            <div class="flex flex-row items-center">
                {{-- <div class="relative flex-grow ml-2 flex flex-row items-center justify-between"> --}}
                    <x-utils.spotfilter name="category" :options="\App\Helpers\AppHelper::getDistinctCategories()" :selectedoption="$filter['category'] ?? config('appSettings.default_client_category')" />
                    {{-- <select x-data="{
                        'val': '{{$filter['category'] ?? config('appSettings.default_client_category')}}'
                        }"
                        @change.stop.prevent="$dispatch('spotfilter', {data: {category: val}});"
                        x-model="val" class="select select-bordered select-sm max-w-xs py-0 m-1"
                        :class="val == -1 || 'text-accent'">
                        <option value="All">All Categories</option>
                        @foreach (\App\Helpers\AppHelper::getDistinctCategories() as $cat)
                            <option value="{{ $cat }}">
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select> --}}
                {{-- </div> --}}
                {{-- <x-utils.spotsort name="category" val="{{ $sort['category'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Type
                </div> --}}
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="aum" val="{{ $sort['aum'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    AUM
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="allocated_aum" val="{{ $sort['allocated_aum'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Buy Value
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="pa" val="{{ $sort['pa'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    PA
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="cur_value" val="{{ $sort['cur_value'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cur. Value
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
                <x-utils.spotsort name="realised_pnl" val="{{ $sort['realised_pnl'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Realised PNL
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="liquidbees" val="{{ $sort['liquidbees'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Liquidbees
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="cash" val="{{ $sort['cash'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cash
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="cash_pc" val="{{ $sort['cash_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cash %
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="ledger_balance" val="{{ $sort['ledger_balance'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Ledger Balance
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="returns" val="{{ $sort['returns'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Return
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="returns_pc" val="{{ $sort['returns_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Return %
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center min-w-36">
                <x-utils.spotsort name="dealer" val="{{ $sort['dealer'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    RM
                    <x-utils.spotsearch textval="{{ $params['dealer'] ?? '' }}" textname="dealer"
                        label="Search dealer" />
                </div>
            </div>
        </th>
    </x-slot>
    <x-slot:rows>
        {{-- @foreach ($clients as $result) --}}
        <template x-for="result in results">
            <tr>
                {{-- @if ($selectionEnabled) --}}
                <td><input type="checkbox" :value="result.id" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs"></td>
                {{-- @endif --}}
                {{-- {{$rows}} --}}
                <td class="sticky !left-6">
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.show', 0)}}?filter[]=tracked::1'.replace('0', result.id), route: 'clients.show'})"
                        class="link no-underline hover:underline" href="" x-text="result.client_code"></a>
                </td>
                <td class="sticky !left-36 z-20">
                    <div class="tooltip tooltip-top" :data-tip="result.name">
                    <a @click.prevent.stop="$dispatch('linkaction', {link: ('{{route('clients.show', 'x0x')}}?filter[]=tracked::1&items_count=y0y'.replace('x0x', result.id)).replace('y0y', items_count), route: 'clients.show'})"
                        class="link no-underline hover:underline" href="" x-text="formatString(result.name, 10)"></a>
                    </div>
                </td>
                <td class="sticky !left-72 z-20 text-center">
                    <span x-text="result.category"></span>
                    <div class="h-full w-1 absolute top-0 right-0 border-r border-base-300"></div>
                </td>
                <td class="text-right" x-text="formatted(result.aum)"></td>
                <td class="text-right" x-text="formatted(result.allocated_aum)"></td>
                <td class="text-right" x-text="formatted(result.pa, 2)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="{'text-error' : result.cur_value < result.allocated_aum, 'text-accent' : result.cur_value > result.allocated_aum}">
                    <x-display.icon x-show="result.cur_value > result.allocated_aum" icon="icons.up-arrow"/>
                    <x-display.icon x-show="result.cur_value < result.allocated_aum" icon="icons.down-arrow"/>
                        <span x-text="formatted(result.cur_value)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                    :class="{'text-error' : result.pnl < 0, 'text-accent' : result.pnl > 0}">
                        <span x-text="formatted(result.pnl)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.pnl < 0, 'text-accent' : result.pnl > 0}">
                        <span x-text="formatted(result.pnl_pc, 2)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.realised_pnl < 0, 'text-accent' : result.realised_pnl > 0}">
                        <span x-text="formatted(result.realised_pnl)"></span>
                    </div>
                </td>
                <td class="text-right" x-text="formatted(result.liquidbees)"></td>
                <td class="text-right" x-text="formatted(result.cash)"></td>
                <td class="text-right" x-text="formatted(result.cash_pc, 2)"></td>
                <td class="text-right" x-text="formatted(result.ledger_balance)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.returns < 0, 'text-accent' : result.returns > 0}">
                        <span x-text="formatted(result.returns)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.returns_pc < 0, 'text-accent' : result.returns_pc > 0}">
                        <span x-text="formatted(result.returns_pc, 2)"></span>
                    </div>
                </td>
                <td x-text="result.dealer" class="text-left"></td>
            </tr>
        </template>
        {{-- @endforeach --}}
    </x-slot>
</x-utils.panelbase>
