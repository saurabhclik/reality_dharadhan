@extends('layouts.app')
@section('title', 'Role & Permission Management')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-6"><h4>Roles & Permissions</h4></div>
            <div class="col-6 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                    <i class="fas fa-plus"></i> Add Role
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                    <i class="fas fa-plus"></i> Add Permission
                </button>
            </div>
        </div>

        {{-- Roles Table --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5>Roles</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Role Name</th>
                            <th>Manager Rights</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $role->role_name }}</td>
                            <td>{{ $role->manager_rights ? 'Yes' : 'No' }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary editRoleBtn" 
                                        data-id="{{ $role->id }}" 
                                        data-name="{{ $role->role_name }}" 
                                        data-rights="{{ $role->manager_rights }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editRoleModal">Edit</button>

                                <form action="{{ route('role.permission.delete', $role->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')">Delete</button>
                                </form>

                                <button class="btn btn-sm btn-warning assignPermissionBtn" 
                                        data-id="{{ $role->id }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#assignPermissionModal">Assign Permissions</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Permissions Table --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5>Permissions</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Permission Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $perm)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $perm->name }}</td>
                            <td>
                                <form action="{{ route('permissions.delete', $perm->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this permission?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('role.permission.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Add Role</h5></div>
                <div class="modal-body">
                    <input type="text" name="role_name" class="form-control mb-2" placeholder="Role Name" required>
                    <select name="manager_rights" class="form-control">
                        <option value="1">Manager Rights: Yes</option>
                        <option value="0">Manager Rights: No</option>
                    </select>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Save Role</button></div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="editRoleForm">
            @csrf
            @method('POST')
            <div class="modal-content">
                <div class="modal-header"><h5>Edit Role</h5></div>
                <div class="modal-body">
                    <input type="text" name="role_name" id="editRoleName" class="form-control mb-2" placeholder="Role Name" required>
                    <select name="manager_rights" id="editManagerRights" class="form-control">
                        <option value="1">Manager Rights: Yes</option>
                        <option value="0">Manager Rights: No</option>
                    </select>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Update Role</button></div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="addPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('permission.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Add Permission</h5></div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control mb-2" placeholder="Permission Name" required>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Save Permission</button></div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="assignPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="assignPermissionForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Assign Permissions</h5></div>
                <div class="modal-body">
                    @foreach($permissions as $perm)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm{{ $perm->id }}">
                        <label class="form-check-label" for="perm{{ $perm->id }}">{{ $perm->name }}</label>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Assign</button></div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        document.querySelectorAll('.editRoleBtn').forEach(btn => {
            btn.addEventListener('click', function() 
            {
                let id = this.dataset.id;
                document.getElementById('editRoleName').value = this.dataset.name;
                document.getElementById('editManagerRights').value = this.dataset.rights;
                document.getElementById('editRoleForm').action = '/role-permission/update/' + id;
            });
        });
        document.querySelectorAll('.assignPermissionBtn').forEach(btn => {
            btn.addEventListener('click', function() 
            {
                let roleId = this.dataset.id;
                document.getElementById('assignPermissionForm').action = '/roles/assign-permissions/' + roleId;
                document.querySelectorAll('#assignPermissionForm input[type=checkbox]').forEach(cb => cb.checked = false);

                fetch('/api/role-permissions/' + roleId)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(pid => {
                            let cb = document.getElementById('perm'+pid);
                            if(cb) cb.checked = true;
                        });
                    });
            });
        });
    });
</script>
@endsection
