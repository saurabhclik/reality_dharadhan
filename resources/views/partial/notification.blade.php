@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Notifications</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Notifications</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            @if($notifications->count())
                                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="ri-check-double-line align-bottom"></i> Mark all as read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if($notifications->count())
                            <div data-simplebar style="max-height: 500px;">
                                @foreach($notifications as $noti)
                                    <div class="d-flex p-3 border-bottom align-items-center {{ $noti->ack ? '' : 'bg-light' }}">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $noti->notification_title }}</h6>
                                            <p class="mb-0 text-muted">{{ $noti->message }}</p>
                                            <small class="text-muted">{{ $noti->time_diff }}</small>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            @if(!$noti->ack)
                                                <form action="{{ route('notifications.markAllRead', $noti->id) }}" method="POST" class="d-inline mark-as-read-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-light" title="Mark as read">
                                                        <i class="ri-checkbox-circle-line"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $notifications->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-lg mx-auto mb-4">
                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle font-size-24">
                                        <i class="ri-notification-off-line"></i>
                                    </div>
                                </div>
                                <h5>No notifications found</h5>
                                <p class="text-muted">You don't have any notifications right now.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        document.querySelectorAll('.mark-as-read-form').forEach(form => {
            form.addEventListener('submit', function(e) 
            {
                e.preventDefault();
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) 
                    {
                        this.closest('.d-flex').classList.remove('bg-light');
                        this.remove();
                        let badge = document.querySelector('#page-header-notifications-dropdown .badge');
                        if (badge) 
                        {
                            let count = parseInt(badge.innerText) - 1;
                            badge.innerText = count > 0 ? count : '';
                        }
                    }
                });
            });
        });
    });
</script>
@endsection
