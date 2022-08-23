<x-dashboard-base :ajax="$x_ajax">
    <x-utils.bulk-import
    title="Import Scripts"
    actionRoute="scripts.import.store"
    filename="scripts.xlsx"
    buttonText="Import">
    <x-slot:thead>
        <th>No.</th>
        <th>Symbol</th>
        <th>ISIN Code</th>
        <th>Company</th>
        <th>Status</th>
    </x-slot>
    <x-slot:trows>
        <td x-text="index+1"></td>
        <td x-text="item.symbol"></td>
        <td x-text="item.isin_code"></td>
        <td x-text="item.company_name"></td>
        <td x-text="item.status"></td>
    </x-slot>
</x-utils.bulk-import>
</x-dashboard-base>