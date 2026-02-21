{{-- resources/views/admin/notifications/index.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Notifications')
@section('title', 'Notifications - NeoProLab')

@section('content')
    @include('notifications.partials.list')
@endsection