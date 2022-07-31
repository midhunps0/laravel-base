@if ($ajax)
    <div class="flex-grow bg-base-100 overflow-hidden shadow-sm sm:rounded-lg min-h-full overflow-y-scroll">
            {{$slot}}
    </div>
@else
<div>
    <x-app-layout>
        <div class="py-1 flex-grow flex flex-row h-full items-stretch w-full space-x-1">
            <div class="bg-base-100 overflow-hidden shadow-sm sm:rounded-md min-w-fit">
                <x-sidebar />
            </div>
            {{-- <template x-if="page != null">
            <div x-html="page" class="flex-grow bg-base-100 overflow-x-hidden shadow-sm sm:rounded-lg"></div>
            </template>
            <template x-if="page == null"> --}}
                <div x-data class="flex-grow bg-base-100 overflow-x-hidden shadow-sm sm:rounded-lg min-h-full overflow-y-scroll" id="renderedpanel">
                        {{$slot}}
                </div>
            {{-- </template> --}}

            <div class="h-full w-full absolute top-0 left-0 z-50 bg-base-200 opacity-30" x-show="ajaxLoading"></div>

            {{-- <div x-data="{showform: false}"
                @passwordform.window="showform = true;" x-show="showform" class="fixed top-0 left-0 z-50 h-screen w-screen">
                <x-utils.password-form />
            </div> --}}
        </div>
    </x-app-layout>
@endif
