<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            <button type="button" class="modal-nav-btn prev" onclick="prevImage()">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="modal-nav-btn next" onclick="nextImage()">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Gallery Image">
            </div>
        </div>
    </div>
</div>