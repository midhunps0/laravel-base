@props(['items_count'])
<div x-data="{count: {{$items_count}} }" class="flex flex-row items-center w-48 space-x-2">
    <label for="items_count">Items per page: </label>
    <select x-model="count"
        @change="$dispatch('countchange', {count: count});"
        class="select select-bordered select-sm w-20 py-0">
        <option value="10" @if($items_count == 10) selected @endif>10</option>
        <option value="20" @if($items_count == 20) selected @endif>20</option>
        <option value="30" @if($items_count == 30) selected @endif>30</option>
        <option value="50" @if($items_count == 50) selected @endif>50</option>
        <option value="100" @if($items_count == 100) selected @endif>100</option>
      </select>
</div>