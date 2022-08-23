<x-dashboard-base :ajax="$x_ajax">
    <div class="p-3 overflow-x-scroll h-full mb-8">
        <div class="p-4 w-full text-center text-warning text-sm font-bold">
            @if (isset($error))
                @if ($error_type=="access denied")
                    <h1>You are not authorised to view this script. Use the search box to find your script.</h1>
                @else
                    <h1>Oops! Something went wrong :( Please try again.</h1>
                @endif

            @endif
        </div>
        <h3 class="text-xl font-bold mb-2">Search Script</h3>
        <x-utils.itemssearch
            itemsName="scripts"
            url="{{route('scripts.show', 0)}}"
            searchUrl="{{route('scripts.list')}}"
            routeName="scripts.show"
            :searchDisplayKeys="['symbol', 'company_name']"
            class="!justify-start"/>
    </div>
</x-dashboard-base>