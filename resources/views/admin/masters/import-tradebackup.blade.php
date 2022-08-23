<x-dashboard-base :ajax="$x_ajax">
    <x-utils.bulk-import
    title="Import Trade Backup"
    actionRoute="post.import.trade_backup"
    filename="tbk.xlsx"
    buttonText="Import">
    <x-slot:thead>
        <th>No.</th>
        <th>Client Code</th>
        <th>Client Status</th>
        <th>Script Symbol</th>
        <th>Script Status</th>
    </x-slot>
    <x-slot:trows>
        <td x-text="index+1"></td>
        <td x-text="item.client_code"></td>
        <td x-text="item.client"></td>
        <td x-text="item.script_symbol"></td>
        <td x-text="item.script"></td>
    </x-slot>
</x-utils.bulk-import>
</x-dashboard-base>