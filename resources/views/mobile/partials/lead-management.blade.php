<div class="lead-management-app">
    <div class="fab" role="button" onclick="openBottomSheet()">
        <a href="#" class="text-decoration-none text-light">
            <i class="fa fa-plus"></i>
        </a>
    </div>
    <div class="lead-status-tabs">
        <div class="tab-scroll-container">
            <a href="{{ route('mobile.new-leads') }}" class="status-tab">
                <i class="fas fa-user-plus"></i>
                <span>New Lead</span>
                <span>{{ $leadStats->new_lead ?? 0 }}</span>
            </a>
            <a href="{{route('mobile.transfer')}}" class="status-tab">
                <i class="fas fa-exchange-alt"></i>
                <span>Transfer</span>
                <span>{{ $leadStats->transfer_lead ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.pending-leads') }}" class="status-tab">
                <i class="fas fa-hourglass-half"></i>
                <span>Pending</span>
                <span>{{ $leadStats->pending_lead ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.processing-leads') }}" class="status-tab">
                <i class="fas fa-hourglass-half"></i>
                <span>Processing</span>
                <span>{{ $leadStats->processing ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.interested-leads') }}" class="status-tab">
                <i class="fas fa-heart"></i>
                <span>Interested</span>
                <span>{{ $leadStats->interested ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.call-leads') }}" class="status-tab">
                <i class="fas fa-phone"></i>
                <span>Calls</span>
                <span>{{ $leadStats->call_schedule ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.visit-leads') }}" class="status-tab">
                <i class="fas fa-map-marker-alt"></i>
                <span>Visits</span>
                <span>{{ $leadStats->visit_schedule ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.visit-done-leads') }}" class="status-tab">
                <i class="fas fa-clipboard-check"></i>   
                <span>Visits Done</span>
                <span>{{ $leadStats->visit_done ?? 0 }}</span>
            </a>
            <a href="{{route('mobile.booked')}}" class="status-tab">
                <i class="fa-solid fa-bowl-rice"></i>   
                <span>Booked</span>
                <span>{{ $leadStats->booked ?? 0 }}</span>
            </a>
            <a href="{{route('mobile.whatsapp')}}" class="status-tab">
                <i class="fa-brands fa-whatsapp"></i>   
                <span>Whatsapp</span>
                <span>{{ $leadStats->whatsapp ?? 0 }}</span>
            </a>
        </div>

        <div class="tab-scroll-container">
            <a href="{{ route('mobile.meeting-scheduled-leads') }}" class="status-tab">
                <i class="fa-solid fa-handshake"></i>
                <span>Meeting Scheduled</span>
                <span>{{ $leadStats->meeting_scheduled ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.completed') }}" class="status-tab">
                <i class="fas fa-check-circle"></i>
                <span>Completed</span>
                <span>{{ $leadStats->completed ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.not-interested-leads') }}" class="status-tab">
                <i class="fas fa-thumbs-down"></i>
                <span>Not Interested</span>
                <span>{{ $leadStats->not_interested ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.not-picked-leads') }}" class="status-tab">
                <i class="fas fa-thumbs-down"></i>
                <span>Not Picked</span>
                <span>{{ $leadStats->not_picked ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.not-reachable-leads') }}" class="status-tab">
                <i class="fas fa-ban"></i>
                <span>Not Reachable</span>
                <span>{{ $leadStats->not_reachable ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.wrong-number-leads') }}" class="status-tab">
                <i class="fas fa-phone-slash"></i>
                <span>Wrong Number</span>
                <span>{{ $leadStats->wrong_number ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.lost-leads') }}" class="status-tab">
                <i class="fas fa-user-times"></i>
                <span>Lost</span>
                <span>{{ $leadStats->lost ?? 0 }}</span>
            </a>
            <a href="{{ route('mobile.future-leads') }}" class="status-tab">
                <i class="fas fa-calendar-alt"></i>
                <span>Future</span>
                <span>{{ $leadStats->future_lead ?? 0 }}</span>
            </a>
        </div>
    </div>
    <div class="lead-summary-cards">
        <div class="summary-card warning">
            <a href="{{route('mobile.notifications')}}" class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="card-content">
                    <h3>{{ $totalCallVisitInterested }}</h3>
                    <p>All Scheduled</p>
                </div>
            </a>
        </div>
    </div>
</div>
