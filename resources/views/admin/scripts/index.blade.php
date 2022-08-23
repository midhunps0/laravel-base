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
    :paginator="$paginator"
    total_disp_cols=19
    id="scripts_index">
    <x-slot:inputFields>
        <input type="hidden" value="{{ $aggregates }}" id="aggregates">
        <input type="hidden" value="{{ $results_json }}" id="results_json">
        <input type="hidden" value="{{ $items_ids }}" id="itemIds">
    </x-slot>
    <x-slot:aggregateCols>
        <th colspan="2">
            Totals:
        </th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_pa, 2)"></span></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_qty)"></span></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_amt_invested)"></span></th>
        <th></th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_cur_value)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_overall_gain)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_gain_pc, 2)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_todays_gain)"></span></th>
        <th class="text-right"><span x-text="formatted(aggregates.dlr_todays_gain_pc, 2)"></span></th>
        <th colspan="2" class="text-center bg-base-300">
            @ CMP&nbsp;+&nbsp;<input type="number" x-model="profit_margin" class="input input-sm w-14 py-0 px-1 h-6 text-secondary" @blur="if(profit_margin.length == 0){profit_margin = 0;}">&nbsp;%
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </x-slot>
    <x-slot:thead>
        {{-- <th class="relative w-72">
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
        </th> --}}
        <th class="sticky !left-7 flex flex-row min-w-36">
            <x-utils.spotsort name="symbol" val="{{ $sort['symbol'] ?? 'none' }}" />
            <div class="relative flex-grow ml-2 min-w-32">
                <span>Symbol</span>
                <x-utils.spotsearch textval="{{ $params['symbol'] ?? '' }}" textname="symbol"
                    label="Search symbol" />
            </div>
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
            <span>Quantity</span>
        </th>
        <th>
            <x-utils.spotsort name="abv" val="{{ $sort['abv'] ?? 'none' }}" />
            <span>ABV</span>
        </th>
        <th>
            <x-utils.spotsort name="amt_invested" val="{{ $sort['amt_invested'] ?? 'none' }}" />
            <span>Buy Value</span>
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
            <x-utils.spotsort name="todays_gain" val="{{ $sort['todays_gain'] ?? 'none' }}" />
            <span>Day's Gain</span>
        </th>
        <th>
            <x-utils.spotsort name="todays_gain_pc" val="{{ $sort['todays_gain_pc'] ?? 'none' }}" />
            <span>Day's Gain %</span>
        </th>
        <th>
            {{-- <x-utils.spotsort name="gain_pc" val="{{ $sort['gain_pc'] ?? 'none' }}" /> --}}
            <span>Sell Rate</span>
        </th>
        <th>
            <x-utils.spotsort name="gain_pc" val="{{ $sort['gain_pc'] ?? 'none' }}" />
            <span>Profit %</span>
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
    </x-slot>
    <x-slot:rows>
        <template x-for="result in results">
            <tr>
                <td>
                    <input type="checkbox" :value="result.sid" x-model="selectedIds"
                    class="checkbox checkbox-primary checkbox-xs">
                </td>
                {{-- <td x-text="result.bse_code"></td>
                <td x-text="result.dop"></td> --}}
                <td class="sticky !left-7">
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('scripts.show', 0)}}'.replace('0', result.sid), route: 'scripts.show'})"
                        class="link no-underline hover:underline" href="" x-text="result.symbol"></a>
                        <div class="h-full w-1 absolute top-0 right-0 border-r border-base-300"></div>
                </td>
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
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.todays_gain < 0 ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.todays_gain)"></span>
                    </div>
                </td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.todays_gain_pc < 0 ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.todays_gain_pc, 2)"></span>
                    </div>
                </td>
                <td class="text-right"><span x-text="formatted(result.cmp * (100 + profit_margin * 1) / 100)"></span></td>
                <td class="text-right"><span x-text="formatted((((result.cmp * (100 + profit_margin * 1) / 100) * result.tot_qty) - result.amt_invested) / result.amt_invested * 100, 2)"></span></td>
                <td x-text="formatted(result.day_high, 2)"></td>
                <td x-text="formatted(result.day_low, 2)"></td>
                <td>
                    <div class="flex flex-row items-baseline justify-end"
                        :class="result.impact < 0 ? 'text-error' : 'text-accent'">
                        <span x-text="formatted(result.impact, 2)"></span>
                    </div>
                </td>
                <td x-text="result.dealer"></td>
            </tr>
        </template>
    </x-slot>
</x-utils.panelbase>
