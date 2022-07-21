<x-utils.panelbase
    :x_ajax="$x_ajax"
    title="Scripts"
    indexUrl="{{ route('scripts.index') }}"
    downloadUrl="{{ route('scripts.download') }}"
    selectIdsUrl="{{ route('scripts.selectIds') }}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$scripts"
    results_name="scripts"
    unique_str="scrx"
    :results_json="$results_json"
    :enableAdvSearch="true"
    :paginator="$paginator"
    total_disp_cols=19
    :columns="['client_code', 'cmp', 'pnl']"
    id="scripts_index">
    <x-slot:inputFields>
        <input type="hidden" value="{{ $aggregates }}" id="aggregates">
        <input type="hidden" value="{{ $results_json }}" id="results_json">
        <input type="hidden" value="{{ $items_ids }}" id="itemIds">
    </x-slot>
    <x-slot:aggregateCols>
            {{-- <th colspan="3">
            Aggregates:
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_buy_val)"></span></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_cur_val)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pnl)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pnl_pc)"></span></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_impact, 2)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.agr_pa, 2)"></span></th> --}}
    </x-slot>
    <x-slot:thead>
        <th class="relative w-44">
            <div class="flex flex-row items-center w-36">
                <x-utils.spotsort name="dealer" val="{{ $sort['dealer'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Dealer
                    <x-utils.spotsearch textval="{{ $params['dealer'] ?? '' }}" textname="dealer"
                        label="Search dealer" />
                </div>
            </div>
        </th>
        <th class="relative w-72">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="bse_code" val="{{ $sort['name'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    BSE Code
                    <x-utils.spotsearch textval="{{ $params['name'] ?? '' }}" textname="name"
                        label="Search name" />
                </div>
            </div>
        </th>
        <th>
            <x-utils.spotsort name="dop" val="{{ $sort['dop'] ?? 'none' }}" />
            <span>DOP</span>
        </th>
        <th>
            <x-utils.spotsort name="symbol" val="{{ $sort['symbol'] ?? 'none' }}" />
            <span>Symbol</span>
        <th>
            <x-utils.spotsort name="pa" val="{{ $sort['pa'] ?? 'none' }}" />
            <span>PA</span>
        </th>
        <th>
            <x-utils.spotsort name="sector" val="{{ $sort['sector'] ?? 'none' }}" />
            <span>Sector</span>
        </th>
        <th>
            <x-utils.spotsort name="tot_qty" val="{{ $sort['tot_qty'] ?? 'none' }}" />
            <span>Tot. Qty.</span>
        </th>
        <th>
            <x-utils.spotsort name="abv" val="{{ $sort['abv'] ?? 'none' }}" />
            <span>ABV</span>
        </th>
        <th>
            <x-utils.spotsort name="amt_invested" val="{{ $sort['amt_invested'] ?? 'none' }}" />
            <span>Amt Invested</span>
        </th>
        <th>
            <x-utils.spotsort name="cmp" val="{{ $sort['cmp'] ?? 'none' }}" />
            <span>CMP</span>
        </th>
        <th>
            <x-utils.spotsort name="cur_value" val="{{ $sort['cur_value'] ?? 'none' }}" />
            <span>Cur. Value</span>
        </th>
        <th>
            <x-utils.spotsort name="overall_gain" val="{{ $sort['overall_gain'] ?? 'none' }}" />
            <span>Overall Gain</span>
        </th>
        <th>
            <x-utils.spotsort name="gain_pc" val="{{ $sort['gain_pc'] ?? 'none' }}" />
            <span>Gain %</span>
        </th>
        <th>
            {{-- <x-utils.spotsort name="gain_pc" val="{{ $sort['gain_pc'] ?? 'none' }}" /> --}}
            <span>Sell Rate 4%</span>
        </th>
        <th>
            {{-- <x-utils.spotsort name="gain_pc" val="{{ $sort['gain_pc'] ?? 'none' }}" /> --}}
            <span>Profit Rate</span>
        </th>
        <th>
            <x-utils.spotsort name="todays_gain" val="{{ $sort['todays_gain'] ?? 'none' }}" />
            <span>Today's Gain</span>
        </th>
        <th>
            <x-utils.spotsort name="day_high" val="{{ $sort['day_high'] ?? 'none' }}" />
            <span>Day High</span>
        </th>
        <th>
            <x-utils.spotsort name="day_low" val="{{ $sort['day_low'] ?? 'none' }}" />
            <span>Day Low</span>
        </th>
        <th>
            <x-utils.spotsort name="impact" val="{{ $sort['impact'] ?? 'none' }}" />
            <span>Impact</span>
        </th>
    </x-slot>
    <x-slot:rows>
        <template x-for="result in results">
            <tr>
                <td>
                    <input type="checkbox" :value="result.id" x-model="selectedIds"
                    class="checkbox checkbox-primary checkbox-xs">
                </td>
                <td x-text="result.dealer"></td>
                <td x-text="result.bse_code"></td>
                <td x-text="result.dop"></td>
                <td x-text="result.symbol"></td>
                <td class="text-right" x-text="formatted(result.pa, 2)"></td>
                <td x-text="result.sector"></td>
                <td class="text-right" x-text="result.tot_qty"></td>
                <td class="text-right" x-text="result.abv"></td>
                <td class="text-right" x-text="formatted(result.amt_invested)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.cmp < result.abv ? 'text-error' : 'text-accent'">
                        <x-display.icon x-show="result.cmp >= result.abv" icon="icons.up-arrow" />
                        <x-display.icon x-show="result.cmp < result.abv" icon="icons.down-arrow" />
                        <span x-text="formatted(result.cmp, 2)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.cur_value < result.amt_invested ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.cur_value)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.overall_gain < 0 ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.overall_gain)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.gain_pc < 0 ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.gain_pc, 2)"></span>
                    </div>
                </td>
                <td></td>
                <td></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.todays_gain < 0 ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.todays_gain, 2)"></span>
                    </div>
                </td>
                <td x-text="formatted(result.day_high, 2)"></td>
                <td x-text="formatted(result.day_low, 2)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.impact < 0 ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.impact, 2)"></span>
                    </div>
                </td>
            </tr>
        </template>
    </x-slot>
</x-utils.panelbase>
