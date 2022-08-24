<x-dashboard-base :ajax="$x_ajax">
    <x-utils.bulk-import
    title="Import Clients"
    actionRoute="clients.import.store"
    filename="clients.xlsx"
    buttonText="Import"
    :headings="[
        'RM',
        'Client Code',
        'Unique Code',
        'Name',
        'Fresh Fund',
        'Re Invest',
        'Withdrawal',
        'Payout',
        'Total Aum',
        'Other Funds',
        'Brokerage',
        'Realised PNL',
        'Ledger Balance',
        'PFO Type',
        'Category',
        'Type',
        'FNO',
        'Pan Number',
        'Email',
        'Phone Number',
        'Whatsapp',
    ]">
    <x-slot:thead>
        <th>No.</th>
        <th>Client Code</th>
        <th>Name</th>
        <th>Status</th>
    </x-slot>
    <x-slot:trows>
        <td x-text="index+1"></td>
        <td x-text="item.client_code"></td>
        <td x-text="item.name"></td>
        <td x-text="item.status"></td>
    </x-slot>
</x-utils.bulk-import>
</x-dashboard-base>