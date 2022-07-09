@php
    $params = \Request::except(['x_mode']);
@endphp
{{ $results->appends($params)->links() }}