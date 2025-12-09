@php
    $errorView = match($status) {
        404 => 'client.components.errors.404',
        500 => 'client.components.errors.500',
        default => null,
    }
@endphp

<div>
    @isset($errorView)
        @component($errorView) @endcomponent
    @endisset
</div>
