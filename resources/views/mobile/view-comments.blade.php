@extends('mobile.layouts.app')
@section('content')
<div class="d-flex gap-4">
    <button class="back-button" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i>
    </button>
    <h4 class="pt-1 px-4 mt-4">
        <i class="fas fa-comments ms-5"></i> Lead Comments
    </h4>
</div>

<div class="dashboard mt-5 pt-2">
    <div id="commentContainer" class="container"></div>
    <div class="text-center py-3" id="loader" style="display:none;">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2">Loading more comments...</p>
    </div>
    <div class="text-center py-3 text-muted" id="noMore" style="display:none;">
        <p>No more comments</p>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () 
    {
        const commentContainer = document.getElementById('commentContainer');
        const loader = document.getElementById('loader');
        const noMore = document.getElementById('noMore');

        let currentPage = 2;
        let isLoading = false;
        let hasMore = {{ $hasMoreInitial ? 'true' : 'false' }};
        let totalFetched = @json($initialComments ?? []).length;
        const maxComments = {{ $totalCount ?? 0 }};
        const initialComments = @json($initialComments ?? []);

        function formatDate(dateStr) 
        {
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + 
                ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function getStatusClass(status) 
        {
            if (!status) return '';
            const key = status.toLowerCase();
            if (key.includes('pending')) return 'pending';
            if (key.includes('follow')) return 'followup';
            if (key.includes('close')) return 'closed';
            if (key.includes('lost')) return 'lost';
            return '';
        }

        function renderComments(list) 
        {
            list.forEach(c => {
                const statusClass = getStatusClass(c.status);
                const html = `
                    <div class="comment-card ${statusClass}">
                        <div class="comment-header">
                            <div>
                                <span class="comment-badge">${c.status ?? '---'}</span>
                                <span class="comment-time">${formatDate(c.created_date)}</span>
                            </div>
                        </div>
                        <div class="comment-content">
                            <div class="meta-item">
                                <span class="meta-label">Comment:</span>
                                <span class="meta-value">${c.comment ?? '---'}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Reminder:</span>
                                <span class="meta-value">${c.remind_date ?? '--'} ${c.remind_time ?? ''}</span>
                            </div>
                        </div>
                    </div>
                `;
                commentContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        function loadMore() 
        {
            if (isLoading || !hasMore) return;
            isLoading = true;
            loader.style.display = 'block';

            fetch("{{ route('mobile.view-comments', $leadId) }}?page=" + currentPage, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.comments && data.comments.length > 0) 
                {
                    renderComments(data.comments);
                    totalFetched += data.comments.length;
                    currentPage++;
                    hasMore = data.hasMore && totalFetched < maxComments;
                } 
                else 
                {
                    hasMore = false;
                    noMore.style.display = 'block';
                }
                loader.style.display = 'none';
                isLoading = false;
            })
            .catch(() => {
                loader.style.display = 'none';
                isLoading = false;
            });
        }

        if (initialComments.length > 0) 
        {
            renderComments(initialComments);
        } 
        else 
        {
            noMore.style.display = 'block';
            hasMore = false;
        }
        window.addEventListener('scroll', function () 
        {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) 
            {
                loadMore();
            }
        });
    });
</script>
@endsection
