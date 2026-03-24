
<div class="modal fade" id="Modalbox" tabindex="-1" aria-labelledby="ModalboxLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="action" action="">
            @csrf
            <input type="hidden" name="id" id="id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="ModalboxLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 type-field d-none">
                        <label for="checklist_type" class="form-label">Type</label>
                        <select class="form-control" name="type" id="type">
                            <option value="post_sale" selected>Post Sale</option>
                        </select>
                    </div>
                    <div class="mb-3 cat-type d-none">
                        <select class="form-control select2" name="cat_type" id="cat_type">
                            <option value="">-- Select Type --</option>
                            @foreach($categoryList as $cat)
                            <option value="{{$cat->name}}">{{$cat->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="project_name" class="form-label" id="modal-name"></label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="modal-type"></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>