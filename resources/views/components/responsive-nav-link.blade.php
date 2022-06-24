@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block pl-3 pr-4 py-2 border-l-4 border-primary text-base font-medium text-primary-content bg-primary focus:outline-none focus:text-primary-content focus:bg-primary focus:border-primary-focus transition duration-150 ease-in-out'
            : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-base-content hover:text-base-content hover:bg-base-100 hover:border-base-200 focus:outline-none focus:text-base-content focus:bg-base-content focus:border-base-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
