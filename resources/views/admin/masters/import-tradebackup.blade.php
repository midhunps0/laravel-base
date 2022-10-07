<x-dashboard-base :ajax="$x_ajax">
    <x-utils.bulk-import
    title="Import Trade Backup"
    actionRoute="post.import.trade_backup"
    buttonText="Import"
    :headings="[
        'Client Code',
        'Symbol',
        'Trade Date Time',
        'Trade Qty',
        'Trade Price',
        'Trade No',
        'Buy/Sell',
    ]"
    notes="<span class='font-bold text-warning'>Trade Date Time</span> shall be in the format <span class='font-bold text-warning'>dd-mm-yyyy</span>">
    <x-slot:thead>
        <th>No.</th>
        <th>Client Code</th>
        <th>Client Status</th>
        <th>Script Symbol</th>
        <th>Script Status</th>
        <th>Trade Date</th>
    </x-slot>
    <x-slot:trows>
        <td x-text="index+1"></td>
        <td x-text="item.client_code"></td>
        <td x-text="item.client"></td>
        <td x-text="item.script_symbol"></td>
        <td x-text="item.script"></td>
        <td x-text="item.trade_date"></td>
    </x-slot>
</x-utils.bulk-import>
</x-dashboard-base>