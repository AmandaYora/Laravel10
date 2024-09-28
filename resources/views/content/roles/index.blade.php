@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Roles</h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            Tambah Role
                        </button>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Role ID</th>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->role_id }}</td>
                                        <td>{{ $role->role_name }}</td>
                                        <td>{{ $role->description }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#editRoleModal" data-id="{{ $role->role_id }}"
                                                data-name="{{ $role->role_name }}" data-description="{{ $role->description }}">
                                                Edit
                                            </button>
                                            @if($role->role_id != $code_superadmin && $role->role_id != $code_admin)
                                                <form action="{{ route('roles.delete', $role->role_id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Role -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('roles.save') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addRoleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="addRoleName" name="role_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="addDescription" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambah Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Role -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('roles.save') }}">
                    @csrf
                    <input type="hidden" id="editRoleId" name="role_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editRoleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="editRoleName" name="role_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#editRoleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var roleId = button.data('id');
            var roleName = button.data('name');
            var description = button.data('description');

            var modal = $(this);
            modal.find('#editRoleId').val(roleId);
            modal.find('#editRoleName').val(roleName);
            modal.find('#editDescription').val(description);
        });

        $(document).ready(function() {
            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 4000);

            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 4000);
        });
    </script>
@endsection
