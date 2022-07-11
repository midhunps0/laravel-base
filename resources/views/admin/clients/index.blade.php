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
    :results_json="$results_json"
    :result_calcs="[

    ]"
    total_disp_cols="15"
    unique_str="clnx"
    adv_fields="
        none: {key: 'none', text: 'Select A Field', type: 'none'},
        client_code: {key: 'client_code', text: 'Client Code', type: 'string'},
        name: {key: 'name', text: 'Client Name', type: 'string'},
        allocated_aum: {key: 'allocated_aum', text: 'ALCTD AUM', type: 'numeric'},
        aum: {key: 'aum', text: 'AUM', type: 'numeric'},
    "
    :paginator="$paginator">
    <x-slot:thead>
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
        <th class="sticky !left-6 w-44">
            <div class="flex flex-row items-center w-36">
                <x-utils.spotsort name="client_code" val="{{ $sort['client_code'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Code
                    <x-utils.spotsearch textval="{{ $params['client_code'] ?? '' }}" textname="client_code"
                        label="Search client code" />
                </div>
            </div>
        </th>
        <th class="w-72 border-l-2 border-base-100 sticky !left-40 z-20">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="name" val="{{ $sort['name'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Name
                    <x-utils.spotsearch textval="{{ $params['name'] ?? '' }}" textname="name"
                        label="Search name" />
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
                <x-utils.spotsort name="cur_value" val="{{ $sort['cur_value'] ?? 'none' }}" />
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
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.show', 0)}}'.replace('0', result.id)})"
                        class="link no-underline hover:underline" href="" x-text="result.client_code"></a>
                </td>
                <td class="sticky !left-40">
                    <a @click.prevent.stop="$dispatch('linkaction', {link: '{{route('clients.show', 0)}}'.replace('0', result.id)})"
                        class="link no-underline hover:underline" href="" x-text="result.name"></a>
                </td>
                <td class="text-right" x-text="formatted(result.aum)"></td>
                <td class="text-right" x-text="formatted(result.allocated_aum)"></td>
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