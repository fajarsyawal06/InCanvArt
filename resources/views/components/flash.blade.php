@php
    $payload = [
        'success' => session('success'),
        'error'   => session('error'),
        'warning' => session('warning'),
        'info'    => session('info'),
        'errors'  => $errors->any() ? $errors->all() : [],
    ];
@endphp

@if ($payload['success'] || $payload['error'] || $payload['warning'] || $payload['info'] || count($payload['errors']))
  <script type="application/json" id="flash-json">@json($payload)</script>
@endif
