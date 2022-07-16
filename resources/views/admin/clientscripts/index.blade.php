<x-utils.panelbase
    :x_ajax="$x_ajax"
    title="Clients With Their Scripts"
    results_name="clientscripts"
    indexUrl="{{route('clientscripts.index')}}"
    downloadUrl="{{route('clientscripts.download')}}"
    selectIdsUrl="{{route('clientscripts.selectIds')}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$clientscripts"
    :results_json="$results_json"
    total_disp_cols="24"
    unique_str="clnscrx"
    adv_fields="
        client_code: {key: 'client_code', text: 'Client Code', type: 'string'},
        symbol: {key: 'symbol', text: 'Symbol', type: 'string'},
        client_pnl_pc: {key: 'client_pnl_pc', text: 'CL. PNL %', type: 'numeric'},
        pnl_pc: {key: 'pnl_pc', text: 'SCR. PNL %', type: 'numeric'},
        pa: {key: 'pa', text: 'Perc. Allocation', type: 'numeric'},
        sector: {key: 'sector', text: 'Sector', type: 'string'},
        nof_days: {key: 'nof_days', text: 'No. of Days', type: 'numeric'},
    "
    :enableAdvSearch="true"
    :paginator="$paginator"
    :columns="[
        'client_code', 'name', 'symbol', 'aum', 'allocated_aum', 'client_cur_value', 'client_pnl', 'client_pnl_pc', 'realised_pnl', 'liquidbees', 'cash', 'cash_pc', 'returns', 'returns_pc', 'qty', 'buy_avg_price', 'cmp', 'ldc', 'day_high', 'day_low', 'pnl', 'pnl_pc', 'nof_days', 'impact', 'industry', 'sector', 'dealer'
    ]"
    orderBaseUrl="{{route('clientscripts.order.download')}}"
    orderVerifyUrl="{{route('clientsripts.sellorder.verify')}}"
    orderCheckUrl="{{route('clientsripts.sellorder.analyse')}}">
    {{-- {{dd($clientscripts->links())}} --}}
    <x-slot:thead>
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
        {{-- <div>{{dd($paginator)}}</div> --}}
        <th class="sticky !left-6 w-36">
            <div class="flex flex-row items-center w-36">
                <x-utils.spotsort name="client_code" val="{{ $sort['client_code'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cl. Code
                    <x-utils.spotsearch textval="{{ $params['client_code'] ?? '' }}" textname="client_code"
                        label="Search client code" />
                </div>
            </div>
        </th>
        <th class="sticky z-20 !left-6 w-52 border-l-2 border-base-100">
            <div class="flex flex-row items-center min-w-72">
                <x-utils.spotsort name="name" val="{{ $sort['name'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cl. Name
                    <x-utils.spotsearch textval="{{ $params['name'] ?? '' }}" textname="name"
                        label="Search name" />
                </div>
            </div>
        </th>
        <th class="sticky z-30 !left-44 w-52 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="symbol" val="{{ $sort['symbol'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Symbol
                    <x-utils.spotsearch textval="{{ $params['symbol'] ?? '' }}" textname="symbol"
                        label="Search symbol" />
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="client_category" val="{{ $sort['client_category'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cl. Category
                </div>
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
                    Alctd AUM
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="client_cur_value" val="{{ $sort['client_cur_value'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cl. Cur Value
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="client_pnl" val="{{ $sort['client_pnl'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    CL. PNL
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="client_pnl_pc" val="{{ $sort['client_pnl_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    CL. PNL %
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
                    Cash%
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="returns" val="{{ $sort['returns'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    T RETN
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="returns_pc" val="{{ $sort['returns_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    T Retn %
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
                    Buy Avg Price
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="cmp" val="{{ $sort['cmp'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    CPM
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="ldc" val="{{ $sort['ldc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    LDC
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
                <x-utils.spotsort name="pnl" val="{{ $sort['pnl'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    SCR. PNL
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="pnl_pc" val="{{ $sort['pnl_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    SCR. PNL %
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
                <x-utils.spotsort name="nof_days" val="{{ $sort['nof_days'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    No. of Days
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
                <x-utils.spotsort name="industry" val="{{ $sort['industry'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Industry
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="sector" val="{{ $sort['sector'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Sector
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
                <td><input type="checkbox" :value="result.uxid" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs sticky"></td>
                {{-- @endif --}}
                {{-- {{$rows}} --}}
                <td class="sticky !left-6">
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.show', 0)}}'.replace('0', result.id)})"
                        class="link no-underline hover:underline" href="" x-text="result.client_code"></a>
                </td>
                <td class="sticky z-20 !left-6 w-44">
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.show', 0)}}'.replace('0', result.id)})"
                        class="link no-underline hover:underline" href="" x-text="result.name"></a>
                </td>
                <td class="sticky z-30 !left-44 ">
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.show', 0)}}'.replace('0', result.id)})"
                        class="link no-underline hover:underline" href="" x-text="result.symbol"></a>
                </td>
                <td class="text-right" x-text="result.client_category"></td>
                <td class="text-right" x-text="formatted(result.aum)"></td>
                <td class="text-right" x-text="formatted(result.allocated_aum)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="{'text-error' : result.client_cur_value < result.allocated_aum, 'text-accent' : result.client_cur_value > result.allocated_aum}">
                    <x-display.icon x-show="result.client_cur_value > result.allocated_aum" icon="icons.up-arrow"/>
                    <x-display.icon x-show="result.client_cur_value < result.allocated_aum" icon="icons.down-arrow"/>
                        <span x-text="formatted(result.client_cur_value)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                    :class="{'text-error' : result.client_pnl < 0, 'text-accent' : result.client_pnl > 0}">
                        <span x-text="formatted(result.client_pnl)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.client_pnl_pc < 0, 'text-accent' : result.client_pnl_pc > 0}">
                        <span x-text="formatted(result.client_pnl_pc, 2)"></span>
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
                <td class="text-right" x-text="result.qty"></td>
                <td class="text-right" x-text="formatted(result.buy_avg_price, 2)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.cmp < result.buy_avg_price, 'text-accent' : result.cmp > result.buy_avg_price}">
                        <span x-text="formatted(result.cmp, 2)"></span>
                    </div>
                </td>
                <td class="text-right" x-text="formatted(result.ldc, 2)"></td>
                <td class="text-right" x-text="formatted(result.day_high, 2)"></td>
                <td class="text-right" x-text="formatted(result.day_low, 2)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.pnl < 0, 'text-accent' : result.pnl > 0}">
                        <span x-text="formatted(result.pnl, 2)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.pnl_pc < 0, 'text-accent' : result.pnl_pc > 0}">
                        <span x-text="formatted(result.pnl_pc, 2)"></span>
                    </div>
                </td>
                <td class="text-right" x-text="formatted(result.pa, 2)"></td>
                <td class="text-right" x-text="result.nof_days"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end" :class="{'text-error' : result.impact < 0, 'text-accent' : result.impact > 0}">
                        <span x-text="formatted(result.impact, 2)"></span>
                    </div>
                </td>
                <td x-text="result.industry"></td>
                <td x-text="result.sector"></td>
                <td x-text="result.dealer" class="text-left"></td>
            </tr>
        </template>
        {{-- @endforeach --}}
    </x-slot>
</x-utils.panelbase>