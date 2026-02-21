{{-- resources/views/courier/notifications/index.blade.php --}}
@extends('layouts.courier')

@section('page-title', 'Notifications')
@section('title', 'Notifications - NeoProLab')

@section('content')
    @include('notifications.partials.list')
@endsection