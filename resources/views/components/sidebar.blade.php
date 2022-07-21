<div x-data="{hidden: false}"
    x-init="hidden = screen.width < 768;"
    @sidebarvisibility.window="hidden=$event.detail.hidden;"
    class="overflow-hidden fixed top-0 left-0 z-50 sm:relative bg-base-100 w-full sm:w-auto min-w-fit ransition-all"
    :class="!hidden || 'w-0'">
    <div class="border-b border-base-300">
        <span x-data="{sidebarcollapse: false, collapsed: false}"
            class="w-full sm:w-auto flex flex-row items-center text-sm px-2 bg-base-200">
            <x-display.icon icon="icons.go_left" height="h-6" width="w-6"
            @click="sidebarcollapse=!sidebarcollapse; collapsed=sidebarcollapse; $dispatch('sidebarresize', {'collapsed': sidebarcollapse});" class="hidden sm:inline-block transition-all"  x-bind:class="!collapsed || 'rotate-180'"/>
            <x-display.icon icon="icons.close" height="h-6" width="w-6"
            @click="$dispatch('sidebarvisibility', {'hidden': true});" class="sm:hidden"/>
            <span class="block px-5 py-3 overflow-hidden transition-all" :class="collapsed ? 'w-0 px-0' : 'w-40 px-5'" x-transition>
                <span class="block w-36 transition-opacity" :class="!collapsed || 'opacity-0'">Dashboard</span>
            </span>
        </span>
    </div>
    <ul>
        <li><x-menu-item title="Clients Overview" route="clients.index" href="{{route('clients.index')}}" icon="icons.users"/></li>
        <li><x-menu-item title="Holdings Overview" route="scripts.index" href="{{route('scripts.index')}}" icon="icons.list"/></li>
        <li><x-menu-item title="Client wise" route="clients.show" href="{{route('clients.show', 0)}}" icon="icons.user"/></li>
        <li><x-menu-item title="Script wise" route="scripts.show" href="{{route('scripts.show', 0)}}"
            icon="icons.clipboard"/></li>
        {{-- <li><x-menu-item title="Sell Order" route="clientscripts.index" href="{{route('clientscripts.index')}}" icon="icons.list"/></li> --}}
        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Superadmin'))
        <li><x-menu-item title="Users" route="users.index" href="{{route('users.index')}}"
            icon="icons.lock"/></li>
        @endif
        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Superadmin'))
        <li><x-menu-item title="Trade Backup" route="get.import.trade_backup" href="{{route('get.import.trade_backup')}}"
            icon="icons.lock"/></li>
        @endif
    </ul>
</div>