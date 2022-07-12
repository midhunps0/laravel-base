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
    :result_calcs="[
        {{-- 'result.buy_val = (result.qty * result.buy_avg_price);',
        'result.cur_val = (result.cmp * result.qty);',
        'result.gain = (result.cur_val - result.buy_val);',
        'result.pc_change = (result.gain / result.buy_val * 100);', --}}
        {{-- 'result.pc_change = (result.gain / result.buy_amt * 100);' --}}
    ]"
    total_disp_cols="19"
    unique_str="clxone"
    :paginator="$paginator">
    <x-slot:body>
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
        <div class="flex flex-row space-x-4" >
        <div class="font-bold border border-base-300 rounded-md p-4">
            <h1><span class="inline-block mr-1">Code: </span><span class="inline-block mr-4 text-warning">{{$model->client_code}}</span><span class="inline-block mr-1">Name: </span><span class="inline-block mr-4 text-warning">{{$model->name}}</span><span class="inline-block mr-1">AUM: </span><span class="inline-block mr-4 text-warning">{{$model->total_aum}}</span></h1>
        </div>
        <x-utils.itemssearch
            itemsName="clients"
            url="{{route('clients.show', 0)}}"
            searchUrl="{{route('clients.list')}}"
            routeName="clients.show"
            :searchDisplayKeys="['code', 'name']"
            />
    </div>
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
        <th class="relative w-52 border-l-2 border-base-100">DOP</th>
        <th class="text-center border-l-2 border-base-100">PA %</th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center w-48">
                <x-utils.spotsort name="industry" val="{{ $sort['industry'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Category
                    <x-utils.spotsearch textval="{{ $params['industry'] ?? '' }}" textname="industry"
                        label="Search category" />
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center w-48">
                <x-utils.spotsort name="mvg_sector" val="{{ $sort['mvg_sector'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Sector
                    <x-utils.spotsearch textval="{{ $params['mvg_sector'] ?? '' }}" textname="mvg_sector"
                        label="Search sector" />
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
                    Avg. Buy Rate
                </div>
            </div>
        </th>
        <th class="text-center border-l-2 border-base-100">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="amt_invested" val="{{ $sort['amt_invested'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Amount Invested
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
                    Today's Gain
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
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('scripts.show', 0)}}'.replace('0', result.id)})"
                        class="link no-underline hover:underline" href="" x-text="result.symbol"></a>
                </td>
                <td x-text="result.entry_date"></td>
                <td x-text="formatted(result.pa)"></td>
                <td x-text="result.category"></td>
                <td x-text="result.sector"></td>
                <td x-text="result.qty"></td>
                <td x-text="formatted(result.buy_avg_price)"></td>
                <td x-text="formatted(result.amt_invested)"></td>
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
                <td class="text-right" x-text="formatted(result.day_high)"></td>
                <td class="text-right" x-text="formatted(result.day_low)"></td>
                <td class="text-right" :class="{'text-error' : result.impact < 0, 'text-accent' : result.impact > 0}" x-text="formatted(result.impact, 2)"></td>
                <td class="text-center" x-text="result.nof_days"></td>
            </tr>
        </template>
        {{-- @endforeach --}}
    </x-slot>
</x-utils.panelbase>