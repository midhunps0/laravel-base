@if ($ajax)
    <div class="flex-grow bg-base-100 overflow-hidden shadow-sm sm:rounded-lg">
            {{$slot}}
    </div>
@else
<div>
    <x-app-layout>
        <div class="py-1 flex-grow flex flex-row h-full items-stretch w-full space-x-1">
            <div class="bg-base-100 overflow-hidden shadow-sm sm:rounded-md">
                <x-sidebar />
            </div>
            <div x-html="page" x-show="page != null" class="flex-grow bg-base-100 overflow-hidden shadow-sm sm:rounded-lg" :class="!ajaxLoading || 'opacity-50'"></div>
            <template x-if="page == null">
                <div x-data id="renderedpanel" class="flex-grow bg-base-100 overflow-hidden shadow-sm sm:rounded-lg" :class="!ajaxLoading || 'opacity-50'">
                        {{$slot}}
                </div>
            </template>
        </div>
    </x-app-layout>
@endif
