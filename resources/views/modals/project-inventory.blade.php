<div class="modal fade" id="projectInventory" tabindex="-1" aria-labelledby="projectInventoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h1 class="modal-title fs-5" id="projectInventoryLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <div class="row">
                <div class="col-md-12">
                    <h5>Type of property</h5>
                    <hr>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for=" ">Select Type</label>
                        <select class="form-control" name="category_type" id="category_type">
                            <option disabled selected>Select Type</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for=" ">Select Category Name</label>
                        <select class="form-control" name="category_id" id="category_id">
                            <option disabled selected>Select Name</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 mt-2">
                    <div class="form-group">
                        <label for=" ">Select Sub Category Name</label>
                        <select class="form-control" name="sub_category_id" id="sub_category_id">
                            <option disabled selected>Select Name</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 mt-4">
                    <h5>Property Details</h5>
                    <hr>
                </div>
                <div class="col-6">
                    <label for=" ">Enter Property Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Property Name">
                </div>
                <div class="col-6">
                    <label for=" ">Enter Description</label>
                    <textarea type="text" name="description" id="description" class="form-control" placeholder="Enter Description"></textarea>
                </div>
                <div class="col-6">
                    <label for=" ">Enter Address</label>
                    <textarea type="text" name="location" id="location" class="form-control" placeholder="Enter Address"></textarea>
                </div>
                <div class="col-6">
                    <label for=" ">Enter City</label>
                    <input type="text" name="city" id="city" class="form-control" placeholder="Enter City">
                </div>
                <div class="col-6">
                    <label for=" ">Enter State</label>
                    <input type="text" name="state" id="state" class="form-control" placeholder="Enter State">
                </div>
                <div class="col-6">
                    <label for=" ">Enter Price</label>
                    <input type="number" name="price" id="price" class="form-control" placeholder="Enter Price">
                </div>
                <div class="col-6">
                    <label for=" ">Enter Size</label>
                    <input type="text" name="size" id="size" class="form-control" placeholder="Enter size">
                </div>
                <div class="col-md-12 mt-4">
                    <h5>Property View</h5>
                    <hr>

                </div>
                <div class="col-md-6">
                    <label for="">Plan</label>
                    <input type="file" name="plan" class="form-control" id="plan">

                </div>
                <div class="col-md-6">
                    <label for="">Floor Plan</label>
                    <input type="file" name="floor_plan" class="form-control" id="floor_plan">

                </div>

                <div class="col-md-6">
                    <label for="">Price List</label>
                    <input type="file" name="price_list" class="form-control" id="price_list">

                </div>

                <div class="col-md-6">
                    <label for="">Upload Photos</label>
                    <input type="file" name="files[]" class="form-control" id="files" multiple>

                </div>
                <div class="col-md-6">
                    <label for="">Video Link</label>
                    <input type="" name="video_link" class="form-control" id="video_link">

                </div>



                <div class="col-6">
                    <label for=" ">Boucher </label>
                    <input type="file" name="document" id="document" class="form-control">
                </div>
            </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>