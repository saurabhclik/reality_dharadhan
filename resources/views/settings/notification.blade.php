@extends('layouts.app')

@section('title', 'Notification | Pro‑leadexpertz')

@section('content')
<div class="page-content">
    <div class="container py-4">
        <h2 class="mb-4">Your Notifications</h2>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if($notifications->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">You have no new notifications.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush list-unstyled p-2">
                        @foreach($notifications as $noti)
                            <span class="badge">Notification</span>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <span>{{ $noti->message }}</span>
                                </div>
                                <span class="badge bg-secondary">{{ $noti->time_diff }}</span>
                            </li>
                            <li class="list-group-divider"></li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if($notifications->isNotEmpty())
            <div class="card-footer text-center">
                <form action="{{ route('setting.notification.mark_all_read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        Mark All as Read
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
