<x-utils.panelbase
    :x_ajax="$x_ajax"
    title="Scriptwise Details"
    indexUrl="{{route('client_scripts.index')}}"
    downloadUrl="{{route('client_scripts.download')}}"
    selectIdsUrl="{{route('client_scripts.selectIds')}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$client_scripts"
    unique_str="clscr">
    {{-- <x-slot:body>
        <h1>Test</h1>
    </x-slot> --}}
    <x-slot:thead>
        <th class="relative">
            <div class="flex flex-row items-center" :class="compact ? 'w-32' : 'w-36'">
                <x-utils.spotsort name="client_code" val="{{ $sort['client_code'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Client
                    <x-utils.spotsearch textval="{{ $params['client_code'] ?? '' }}" textname="client_code"
                        label="Search client" />
                </div>
            </div>
        </th>
        <th class="relative w-52">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="symbol" val="{{ $sort['symbol'] ?? 'symbol' }}" />
                <div class="relative flex-grow ml-2">
                    Script
                    <x-utils.spotsearch textval="{{ $params['symbol'] ?? '' }}" textname="symbol"
                        label="Search script" />
                </div>
            </div>
        </th>
        <th>Qty</th>
        <th>Avg. Buy Rate</th>
        <th>Avg. Buy Value</th>
        <th>CMP</th>
        <th>Cur. Value</th>
        <th>PNL</th>
        <th>P/L</th>
        <th>No. of days</th>
        <th>Imacpt</th>
        <th>Allocation %</th>
    </x-slot>
    <x-slot:rows>
        @foreach ($client_scripts as $result)
            <tr>
                {{-- @if ($selectionEnabled) --}}
                <td><input type="checkbox" value="{{ $result->id }}" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs"></td>
                {{-- @endif --}}
                {{-- {{$rows}} --}}
                <td>{{ $result->client_code }}</td>
                <td>{{ $result->symbol }}</td>
                <td>{{ $result->qty }}</td>
                <td>{{ $result->avg_buy_price }}</td>
                <td>{{ $result->avg_buy_value }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </x-slot>
</x-utils.panelbase>