<x-utils.panelbase
    :x_ajax="$x_ajax"
    title="Client wise Details"
    indexUrl="{{route('clients.show', $model->id)}}"
    downloadUrl="{{route('clients.show.download', $model->id)}}"
    selectIdsUrl="{{route('clients.show.selectIds', $model->id)}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$scripts"
    unique_str="clxone">
    <x-slot:body>
        <div class="flex flex-row space-x-4" >
        <div class="font-bold border border-base-300 rounded-md p-4">
            <h1><span class="inline-block w-12">Client: </span><span class="text-warning">{{$model->client_code}}, {{$model->name}}</span></h1>
            <h1><span class="inline-block w-12">AUM: </span><span class="text-warning">Rs. {{$model->total_aum}}</span></h1>
        </div>
        <x-utils.itemssearch
            itemsName="clients"
            url="{{route('clients.show', 0)}}"
            searchUrl="{{route('clients.list')}}"
            routeName="clients.show"
            />
    </div>
    </x-slot>
    <x-slot:thead>
        <th class="relative">
            {{-- <div class="flex flex-row items-center" :class="compact ? 'w-32' : 'w-36'">
                <x-utils.spotsort name="client_code" val="{{ $sort['client_code'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Client
                    <x-utils.spotsearch textval="{{ $params['client_code'] ?? '' }}" textname="client_code"
                        label="Search client" />
                </div>
            </div> --}}
            Sl. No.
        </th>
        <th>Symbol</th>
        <th class="relative w-52">DOP</th>
        <th>PA %</th>
        <th>Category</th>
        <th>Sector</th>
        <th>Qty</th>
        <th>Avg. Buy Rate</th>
        <th>Amount Invested</th>
        <th>CMP</th>
        <th>Cur Value</th>
        <th>Overall Gain</th>
        <th>% Change</th>
        <th>Today's Gain</th>
        <th>Day High</th>
        <th>Day Low</th>
        <th>Impact</th>
        <th>No. of days</th>
    </x-slot>

    <x-slot:rows>
        @foreach ($scripts as $result)
            <tr>
                {{-- @if ($selectionEnabled) --}}
                <td><input type="checkbox" value="{{ $result->id }}" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs"></td>
                <td>{{$loop->index}}</td>
                {{-- @endif --}}
                {{-- {{$rows}} --}}
                <td>{{ $result->symbol }}</td>
                <td>{{ $result->entry_date }}</td>
                <td>{{ $result->pa }}</td>
                <td>{{ $result->category }}</td>
                <td>{{ $result->sector }}</td>
                <td>{{ $result->qty }}</td>
                <td>{{ $result->buy_avg_price }}</td>
                <td>{{ $result->amt_invested }}</td>
                <td></td>
                <td></td>
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