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
    unique_str="clnx">
    <x-slot:thead>
        <th class="relative w-44">
            <div class="flex flex-row items-center w-36">
                <x-utils.spotsort name="client_code" val="{{ $sort['client_code'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Code
                    <x-utils.spotsearch textval="{{ $params['client_code'] ?? '' }}" textname="client_code"
                        label="Search client code" />
                </div>
            </div>
        </th>
        <th class="relative w-72">
            <div class="flex flex-row items-center">
                <x-utils.spotsort name="name" val="{{ $sort['name'] ?? 'none' }}" />
                <div class="relative flex-grow ml-2">
                    Name
                    <x-utils.spotsearch textval="{{ $params['name'] ?? '' }}" textname="name"
                        label="Search name" />
                </div>
            </div>
        </th>
        <th>AUM</th>
        <th>Alctd AUM</th>
        <th>Cur Value</th>
        <th>PNL</th>
        <th>PNL %</th>
        <th>Liquidbees</th>
        <th>Cash</th>
        <th>Realised PNL</th>
        <th>T Retn</th>
        <th>T Retn %</th>
        <th>Cash%</th>
        <th>RM</th>
    </x-slot>
    <x-slot:rows>
        @foreach ($clients as $result)
            <tr>
                {{-- @if ($selectionEnabled) --}}
                <td><input type="checkbox" value="{{ $result->id }}" x-model="selectedIds"
                        class="checkbox checkbox-primary checkbox-xs"></td>
                {{-- @endif --}}
                {{-- {{$rows}} --}}
                <td>
                    <a class="link no-underline hover:underline" href="{{route('clients.show', $result->id)}}">
                        {{ $result->client_code }}
                    </a>
                </td>
                <td>
                    <a class="link no-underline hover:underline" href="{{route('clients.show', $result->id)}}">
                        {{ $result->name }}
                    </a>
                </td>
                <td>{{ $result->total_aum }}</td>
                <td></td>
                <td></td>
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