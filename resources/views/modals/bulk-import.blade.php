<style>
     .dataTables_paginate 
    {
        display:block !important;
    }
</style>
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-4 fw-bold">
                    <i class="bi bi-cloud-arrow-up me-2"></i> Bulk Import Leads
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Template
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="text-muted">Download our pre-formatted CSV template to ensure proper formatting.</p>
                                <div class="mt-auto">
                                    <a href="{{ asset('sample.csv') }}" download="{{ asset('sample.csv') }}" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-download me-2"></i> Download Sample CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-upload me-2"></i>Upload Your File
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('lead.import.upload') }}" enctype="multipart/form-data" id="importForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="csv_file" class="form-label fw-semibold">Select CSV File</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" name="file" id="csv_file" accept=".csv" class="form-control" required
                                                onchange="previewFile(this)">
                                            <div class="invalid-feedback">Please select a valid CSV file.</div>
                                        </div>
                                    </div>
                                    <div class="upload-preview mt-3 text-center d-none" id="filePreview">
                                        <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
                                        <p class="mb-0 fw-semibold" id="fileName"></p>
                                        <small class="text-muted" id="fileSize"></small>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-table me-2"></i>Required CSV Format
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-nowrap">Source <span class="text-danger">*</span></th>
                                        <th class="text-nowrap">Stage <span class="text-danger">*</span></th>
                                        <th class="text-nowrap">Campaign <span class="text-danger">*</span></th>
                                        <th class="text-nowrap">Name</th>
                                        <th class="text-nowrap">Phone No.</th>
                                        <th class="text-nowrap">E-mail</th>
                                        <th class="text-nowrap">Alternative No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Website</td>
                                        <td>Stage 1</td>
                                        <td>Summer 2023</td>
                                        <td>John Doe</td>
                                        <td>9899999999</td>
                                        <td>john@example.com</td>
                                        <td>9899999999</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-muted small">* Required fields</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Available Sources:</h6>
                                @if(isset($sources) && $sources->count())
                                    <div class="table-responsive">
                                        <table id="sourcesTable" class="table table-striped table-bordered table-hover mb-0" style="width:100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Source Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sources as $source)
                                                    <tr>
                                                        <td>{{ $source->name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted fst-italic">No sources available.</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Available Campaigns:</h6>
                                @if(isset($campaigns) && $campaigns->count())
                                    <div class="table-responsive">
                                        <table id="bulkCampaignsTable" class="table table-striped table-bordered table-hover mb-0" style="width:100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Campaign Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($campaigns as $campaign)
                                                    <tr>
                                                        <td>{{ $campaign->name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted fst-italic">No campaigns available.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i> Cancel
                </button>
                <button type="submit" form="importForm" class="btn btn-primary" id="SubmitBtn">
                    <span id="SubmitText">Upload & Import</span>
                    <span id="SubmitSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('importForm').addEventListener('submit', function() 
    {
        let btn = document.getElementById('SubmitBtn');
        let text = document.getElementById('SubmitText');
        let spinner = document.getElementById('SubmitSpinner');
        btn.disabled = true;
        text.classList.add('d-none');
        spinner.classList.remove('d-none');
    });
</script>
