
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('zone.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Zones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSV File</label>
                        <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">Please upload a CSV file with columns: city_id, zone_name, sub_area, pincode, zone_order, status</div>
                    </div>
                    <div class="mb-3">
                        <a href="{{ asset('sample_zones.csv') }}" class="btn btn-sm btn-outline-primary">
                            <i class="ri-download-line align-bottom me-1"></i> Download Sample CSV
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                        <span id="SubmitText">Import</span>
                        <span id="SubmitSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>