@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Manage Role Access</h6>
                    </div>

                    <!-- Dropdown Role -->
                    <div class="mb-4">
                        <label for="selectRole" class="form-label">Pilih Role</label>
                        <select class="form-select" id="selectRole">
                            @foreach ($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
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

                    <!-- Table for Menu Access Control -->
                    <div class="table-responsive">
                        <table id="menuAccessTable" class="table">
                            <thead>
                                <tr>
                                    <th>Menu / Sub-Menu</th>
                                    <th>Read</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menus as $menu)
                                    <tr>
                                        <td>{{ $menu->menu }}</td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][read]"
                                                {{ isset($roleAccess[$menu->menu_id]) && $roleAccess[$menu->menu_id]->can_read == 1 ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][create]"
                                                {{ isset($roleAccess[$menu->menu_id]) && $roleAccess[$menu->menu_id]->can_create == 1 ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][update]"
                                                {{ isset($roleAccess[$menu->menu_id]) && $roleAccess[$menu->menu_id]->can_update == 1 ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][delete]"
                                                {{ isset($roleAccess[$menu->menu_id]) && $roleAccess[$menu->menu_id]->can_delete == 1 ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Button Simpan Akses di sisi kanan -->
                    <div class="mt-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary">Simpan Akses</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#menuAccessTable').DataTable();

            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 4000);

            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 4000);

            $('#selectRole').change(function() {
                var roleId = $(this).val();
                // Fetch the permissions for the selected role and update the checkboxes accordingly
                // This could involve an AJAX request to fetch permissions from the server
            });

            // Logic to automatically check "read" when "create", "update", or "delete" is checked
            $('input[type=checkbox]').on('change', function() {
                var row = $(this).closest('tr'); // Get the row of the checkbox that was clicked
                var readCheckbox = row.find(
                    'input[name$="[read]"]'); // Find the read checkbox in the same row
                var createCheckbox = row.find('input[name$="[create]"]');
                var updateCheckbox = row.find('input[name$="[update]"]');
                var deleteCheckbox = row.find('input[name$="[delete]"]');

                // If any of the create, update, or delete checkboxes is checked, check the read checkbox
                if (createCheckbox.is(':checked') || updateCheckbox.is(':checked') || deleteCheckbox.is(
                        ':checked')) {
                    readCheckbox.prop('checked', true);
                }
            });
        });
    </script>
@endsection
