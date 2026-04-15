@props(['title' => config('app.name', 'Pelicon')])

@include('layouts.public', [
    'title' => $title,
    'slot' => $slot,
])
