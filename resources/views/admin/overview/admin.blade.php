<x-utils.panelbase-aggr
    :x_ajax="$x_ajax"
    title="RMs"
    indexUrl="{{route('dashboard')}}"
    downloadUrl="{{route('aggregates.admin.download')}}"
    selectIdsUrl="{{route('aggregates.admin.selectIds')}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$aggr"
    results_name="aggregates"
    unique_str="aggradm"
    :results_json="$results_json"
    :paginator="$paginator"
    total_disp_cols="15"
    adv_fields="
        {{-- none: {key: 'none', text: 'Select A Field', type: 'none'},
        client_code: {key: 'client_code', text: 'Client Code', type: 'string'},
        name: {key: 'name', text: 'Client Name', type: 'string'},
        allocated_aum: {key: 'allocated_aum', text: 'ALCTD AUM', type: 'numeric'},
        aum: {key: 'aum', text: 'AUM', type: 'numeric'}, --}}
    "
    id="aggr_adm"
    :selectionEnabled="false">
    <x-slot:inputFields>
        {{-- <input type="hidden" value="{{$aggregates}}" id="aggregates"> --}}
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
    </x-slot>
    {{-- <x-slot:aggregateCols>
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
    </x-slot> --}}
    <x-slot:thead>
        <th class="sticky !left-0 w-40">
            <div class="flex flex-row items-center w-40">
                <x-utils.spotsort name="rm" val="{{ $sort['rm'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    RM
                    <x-utils.spotsearch textval="{{ $params['rm'] ?? '' }}" textname="rm"
                        label="Search RM" />
                </div>
            </div>
        </th>
        <th class="w-20 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-20">
                <x-utils.spotsort name="count" val="{{ $sort['count'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Count
                </div>
            </div>
        </th>
        <th class="w-32 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="aum" val="{{ $sort['aum'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    AUM
                </div>
            </div>
        </th>
        <th class="w-32 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="buy_value" val="{{ $sort['buy_value'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Buy Value
                </div>
            </div>
        </th>
        <th class="w-32 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="current_value" val="{{ $sort['current_value'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Current Value
                </div>
            </div>
        </th>
        <th class="w-32 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="pnl" val="{{ $sort['pnl'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    PNL
                </div>
            </div>
        </th>
        <th class="w-28 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-28">
                <x-utils.spotsort name="pnl_pc" val="{{ $sort['pnl_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    PNL %
                </div>
            </div>
        </th>
        <th class="w-36 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-36">
                <x-utils.spotsort name="realised_pnl" val="{{ $sort['realised_pnl'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Realised PNL
                </div>
            </div>
        </th>
        <th class="w-32 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="cash" val="{{ $sort['cash'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cash
                </div>
            </div>
        </th>
        <th class="w-28 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-28">
                <x-utils.spotsort name="cash_pc" val="{{ $sort['cash_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Cash %
                </div>
            </div>
        </th>
        <th class="w-32 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-32">
                <x-utils.spotsort name="returns" val="{{ $sort['returns'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Returns
                </div>
            </div>
        </th>
        <th class="w-28 border-l-2 border-base-100">
            <div class="flex flex-row items-center w-28">
                <x-utils.spotsort name="returns_pc" val="{{ $sort['returns_pc'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Returns %
                </div>
            </div>
        </th>
    </x-slot>
    <x-slot:rows>
        <template x-for="result in results">
            <tr>
                {{-- <td><input type="checkbox" :value="result.rmid" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs"></td> --}}
                <td class="sticky !left-0 z-50 text-left">
                    <span x-text="result.rm"></span>
                    <div class="h-full w-1 absolute top-0 right-0 border-r border-base-300"></div>
                </td>
                <td class="text-right" x-text="formatted(result.count)"></td>
                <td class="text-right" x-text="formatted(result.aum)"></td>
                <td class="text-right" x-text="formatted(result.buy_value)"></td>
                <td class="text-right" x-text="formatted(result.current_value)"></td>
                <td class="text-right" x-text="formatted(result.pnl)"></td>
                <td class="text-right" x-text="formatted(result.pnl_pc, 2)"></td>
                <td class="text-right" x-text="formatted(result.realised_pnl)"></td>
                <td class="text-right" x-text="formatted(result.cash)"></td>
                <td class="text-right" x-text="formatted(result.cash_pc)"></td>
                <td class="text-right" x-text="formatted(result.returns)"></td>
                <td class="text-right" x-text="formatted(result.returns_pc)"></td>
                {{--
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
                <td x-text="result.dealer" class="text-left"></td>--}}
            </tr>
        </template>
    </x-slot>
</x-utils.panelbase>
