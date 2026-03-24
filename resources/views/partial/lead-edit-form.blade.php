<div class="form-group col-md-6">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="{{ $lead->name }}" required>
</div>

<div class="form-group col-md-6">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ $lead->email }}">
</div>

<div class="form-group col-md-6">
    <label>Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ $lead->phone }}" readonly>
</div>

<div class="form-group col-md-6">
    <label>Category Type</label>
    <select name="type" class="form-control" required>
        <option value="Residential" {{ $lead->type == 'Residential' ? 'selected' : '' }}>Residential</option>
        <option value="Commercial" {{ $lead->type == 'Commercial' ? 'selected' : '' }}>Commercial</option>
    </select>
</div>

<div class="form-group col-md-6">
    <label>Category</label>
    <select name="catg_id" class="form-control" required>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ $lead->catg_id == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group col-md-6">
    <label>Sub Category</label>
    <select name="sub_catg_id" class="form-control" required>
        @foreach($subcategories as $subcategory)
            <option value="{{ $subcategory->id }}" {{ $lead->sub_catg_id == $subcategory->id ? 'selected' : '' }}>
                {{ $subcategory->name }}
            </option>
        @endforeach
    </select>
</div>