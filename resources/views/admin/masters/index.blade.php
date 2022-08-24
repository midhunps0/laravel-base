<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{ compact: $persist(false) }" class="p-3 overflow-x-scroll">

        <div class="flex flex-row justify-between items-center mb-4">
            <h3 class="text-xl font-bold"><span>Data Import</span></h3>
        </div>

        <div class="rounded-md w-full flex flex-row justify-start space-x-10">
            <x-display.dashboard-item route="get.import.trade_backup" title="Trade Backup" />
            <x-display.dashboard-item route="clients.import.create" title="Clients" />
            <x-display.dashboard-item route="scripts.import.create" title="Scripts" />
            <x-display.dashboard-item route="clientsportfolio.import.create" title="Portfolio" />
        </div>
    </div>
</x-dashboard-base>
