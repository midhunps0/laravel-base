@props(['route', 'title'])
{{-- <div class="flex flex-row w-full h-full p-4 space-x-4 items-start space-y-4"> --}}
    <div class="p-4 rounded-md h-28 w-52 text-center bg-base-200 text-base-content border border-base-300 shadow-md hover:bg-base-300 hover:text-warning hover:cursor-pointer flex flex-col">
        <a href="#" @click.prevent.stop="$dispatch('linkaction', {link: '{{route($route)}}', route: '{{$route}}'})" class="flex flex-row h-full w-full no-underline hover:text-warning items-center justify-center"><span>{{$title}}</span></a>
    </div>
{{-- </div> --}}