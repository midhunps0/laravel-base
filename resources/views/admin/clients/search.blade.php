<x-dashboard-base :ajax="$x_ajax">
    <div class="p-3 border-b border-base-200 overflow-x-scroll h-full mb-8">
        <div class="p-4 w-full text-center text-warning text-sm font-bold">
            @if (isset($error))
                @if ($error_type=="access denied")
                    <h1>You are not authorised to view this client. Use the search box to find your client.</h1>
                @else
                    <h1>Oops! Something went wrong :( Please try again.</h1>
                    {{$error}}
                @endif

            @endif
        </div>
        <h3 class="text-xl font-bold mb-2">Search Client</h3>
        <x-utils.itemssearch
            itemsName="clients"
            url="{{route('clients.show', 0)}}"
            searchUrl="{{route('clients.list')}}"
            routeName="clients.show"
            :searchDisplayKeys="['code', 'name']"
            />
    </div>
</x-dashboard-base>