<x-dashboard-base :ajax="$x_ajax">
    <x-utils.bulk-import
        title="Import Clients Portfolio"
        actionRoute="clientsportfolio.import.store"
        filename="clients.xlsx"
        buttonText="Import"
        :headings="[
            'Client Code', 'Symbol', 'Qty', 'Buy Avg Price', 'Entry Date'
        ]"
        notes="<span class='font-bold text-warning'>Entry date</span> shall be in the format <span class='text-warning font-bold'>dd-mm-yyyy</span>">
        <x-slot:thead>
            <th>No.</th>
            <th>Client Code</th>
            <th>Symbol</th>
            <th>Status</th>
        </x-slot>
        <x-slot:trows>
            <td x-text="index+1"></td>
            <td x-text="item.client_code"></td>
            <td x-text="item.symbol"></td>
            <td x-text="item.status"></td>
        </x-slot>
    </x-utils.bulk-import>
</x-dashboard-base>