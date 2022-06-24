@props(['title' => 'Menu Item', 'href' => '#', 'icon' => 'icons.info'])
<a x-data="{collapsed: false}"
    @sidebarresize.window="collapsed = $event.detail.collapsed;"
    @click.prevent.stop="$dispatch('linkaction', {link: '{{$href}}'});"
    href="{{$href}}" class="flex flex-row items-center my-1 text-sm px-2 hover:bg-base-300"
    :class="currentpath != '{{$href}}' || 'text-accent font-bold bg-base-300'">
    <x-display.icon icon="{{$icon}}" height="h-6" width="w-6"/>
    <span class="inline-block py-3 transition-all" :class="collapsed ? 'w-0 px-0' : 'w-40 px-5'" x-transition>
        <span class="block w-36 transition-opacity" :class="!collapsed || 'opacity-0'">{{$title}}</span>
    </span>
</a>