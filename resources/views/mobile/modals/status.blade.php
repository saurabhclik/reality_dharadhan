
<style>
    .modal-overlay 
    {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;   
        display: block; 
        
    }

    .modal-content 
    {
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 100%;
        max-width: 400px;
        max-height: 90vh;  
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        overflow-y: auto;
    }

    .modal-title 
    {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
    }
    .choices__inner 
    {
        min-height: 45px;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 14px;
    }

    .choices__list--multiple .choices__item 
    {
        background-color: #0d6efd;
        border: none;
        border-radius: 6px;
        padding: 4px 8px;
    }

    .choices__input 
    {
        font-size: 14px;
    }
</style>
@php
    use Illuminate\Support\Facades\DB;
@endphp
<div id="statusModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3 class="modal-title">Update Status</h3>
        <form id="statusUpdateForm">
            <div class="form-group">
                <label for="statusSelect">New Status:</label>
                <select id="statusSelect" name="newStatus" class="form-control" required>
                    <option value="">Select new status</option>
                    <option value="PENDING">Pending</option>
                    <option value="INTERESTED">Interested</option>
                    <option value="WHATSAPP">Whatsapp</option>
                    <option value="CALL SCHEDULED">Call Scheduled</option>
                    <option value="MEETING SCHEDULED">Meeting Scheduled</option>
                    <option value="VISIT SCHEDULED">Visit Scheduled</option>
                    <option value="VISIT DONE">Visit Done</option>
                    <option value="WRONG NUMBER">Wrong Number</option>
                    <option value="NOT INTERESTED">Not Interested</option>
                    <option value="FUTURE LEAD">Future Lead</option>
                    <option value="NOT REACHABLE">Not Reachable</option>
                    <option value="LOST">Lost</option>
                    <option value="CHANNEL PARTNER">Channel Partner</option>
                    <option value="CONVERTED">Converted</option>
                </select>
            </div>     
            <div class="form-group col-md-6 col-lg-6 mb-2 mobile-schedule-project">
                <label for="">Project</label>
                @php
                    $projects = DB::table('projects')->select('id', 'project_name')->get();
                @endphp

                <select class="form-control" name="prj_id[]" id="prj_id" multiple>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>
            </div>      
            <div id="reminderFields">
                <div class="form-grsoup">
                    <label for="remindDate">Reminder Date:</label>
                    <input type="date" name="remindDate" id="remindDate" class="form-control">
                </div>
                <div class="form-group">
                    <label for="remindTime">Reminder Time:</label>
                    <input type="time" name="remindTime" id="remindTime" class="form-control">
                </div>
            </div>
            <div id="conversionFields" style="display: none;">
                <div class="form-group">
                    <label for="conversionType">Conversion Type:</label>
                    <select id="conversionType" name="conversionType" class="form-control">
                        <option value="">Select Conversion Type</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Booked">Booked</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12 applicant_div" style="display: none;">
                <div class="row">
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Project</label>
                        @php
                            $projects = DB::table('projects')->select('id', 'project_name')->get();
                        @endphp

                        <select class="form-select" name="prj_id" id="prj_id">
                            <option value="">--- Select Project ---</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Size</label>
                        <input type="text" class="form-control" name="prop_size" id="prop_size" placeholder="Enter Size">
                    </div>
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Final Price</label>
                        <input type="text" class="form-control" name="final_price" id="final_price" placeholder="Enter final price">
                    </div>
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Applicant Name</label>
                        <input type="text" class="form-control" name="app_name" id="app_name" placeholder="Enter applicant name">
                    </div>
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Applicant Contact</label>
                        <input type="number" class="form-control" name="app_contact" id="app_contact" placeholder="Enter applicant contact">
                    </div>
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Applicant City</label>
                        @php
                            $cities = DB::table('state_district')->select('District')->get();
                        @endphp

                        <select class="form-select" name="app_city" id="app_city">
                            <option value="">---- Select City ----</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->District }}">{{ $city->District }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Applicant DOB</label>
                        <input type="date" class="form-control" name="app_dob" id="app_dob" placeholder="Enter applicant DOB">
                    </div>
                    <div class="form-group col-md-6 col-lg-6 mb-2">
                        <label for="">Applicant DOA</label>
                        <input type="date" class="form-control" name="app_doa" id="app_doa" placeholder="Enter applicant date of anniversary">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="statusComment">Comment:</label>
                <textarea id="statusComment" name="comment" class="form-control" rows="4"></textarea>
            </div>
            <div class="modal-actions mb-5">
                <button type="button" id="cancelStatusUpdate" class="btn btn-secondary text-light">Cancel</button>
                <button type="submit" id="confirmStatusUpdate" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>