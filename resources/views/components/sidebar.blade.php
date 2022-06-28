<div x-data="{hidden: false}"
    x-init="console.log('test');hidden = screen.width < 768;"
    @sidebarvisibility.window="hidden=$event.detail.hidden;"
    class="overflow-hidden fixed top-0 left-0 z-50 sm:relative bg-base-100 w-full sm:w-auto transition-all"
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
        <li><x-menu-item title="Clients Overview" href="{{route('clients.index')}}" icon="icons.users"/></li>
        <li><x-menu-item title="Client wise" href="{{route('clients.show', 0)}}" icon="icons.user"/></li>
        <li><x-menu-item title="Script wise" href="{{route('client_scripts.index')}}"/></li>
        <li><x-menu-item title="Users" href="{{route('users.index')}}"/></li>
        <li><x-menu-item /></li>
    </ul>
</div>