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
                                    <th>Menu</th>
                                    <th>Read</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menus as $menu)
                                    <tr data-menu-id="{{ $menu->menu_id }}">
                                        <td>{{ $menu->menu }}</td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][read]">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][create]">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][update]">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                                name="permissions[{{ $menu->menu_id }}][delete]">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading Overlay and Spinner -->
                    <div id="loadingOverlay"></div>
                    <div id="loadingSpinner" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <!-- Button Simpan Akses di sisi kanan -->
                    <div class="mt-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" id="saveAccessButton">Simpan Akses</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#menuAccessTable').DataTable();

            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 4000);

            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 4000);

            // Function to load menu access based on role
            function loadMenuAccess(roleId) {
                $('input[type=checkbox]').prop('checked', false);
                var formData = new FormData();
                formData.append('data', JSON.stringify({ "role_id": roleId }));
                formData.append('_token', '{{ csrf_token() }}');

                $('#loadingOverlay, #loadingSpinner').show(); // Show loading spinner and overlay

                $.ajax({
                    url: "{{ route('api.menu.access') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var data = response.data;

                        $.each(data, function(menuId, access) {
                            var row = $('tr[data-menu-id="' + access.menu_id + '"]');
                            row.find('input[name="permissions[' + access.menu_id + '][read]"]')
                                .prop('checked', access.can_read == 1);
                            row.find('input[name="permissions[' + access.menu_id + '][create]"]')
                                .prop('checked', access.can_create == 1);
                            row.find('input[name="permissions[' + access.menu_id + '][update]"]')
                                .prop('checked', access.can_update == 1);
                            row.find('input[name="permissions[' + access.menu_id + '][delete]"]')
                                .prop('checked', access.can_delete == 1);
                        });
                    },
                    error: function(xhr) {
                        console.error("An error occurred:", xhr.responseText);
                    },
                    complete: function() {
                        $('#loadingOverlay, #loadingSpinner').hide(); // Hide spinner and overlay after completion
                    }
                });
            }

            // When page loads, automatically load access for the first role
            var firstRoleId = $('#selectRole option:first').val();
            if (firstRoleId) {
                loadMenuAccess(firstRoleId);
            }

            $('#selectRole').change(function() {
                var roleId = $(this).val();
                if (roleId) {
                    loadMenuAccess(roleId);
                }
            });

            $('input[type=checkbox]').on('change', function() {
                var row = $(this).closest('tr');
                var readCheckbox = row.find('input[name$="[read]"]');
                var createCheckbox = row.find('input[name$="[create]"]');
                var updateCheckbox = row.find('input[name$="[update]"]');
                var deleteCheckbox = row.find('input[name$="[delete]"]');

                if (createCheckbox.is(':checked') || updateCheckbox.is(':checked') || deleteCheckbox.is(':checked')) {
                    readCheckbox.prop('checked', true);
                }
            });

            $('#saveAccessButton').click(function() {
                var roleId = $('#selectRole').val();
                var accessData = [];

                $('#menuAccessTable tbody tr').each(function() {
                    var menuId = $(this).data('menu-id');
                    var canRead = $(this).find('input[name="permissions[' + menuId + '][read]"]').is(':checked') ? 1 : 0;
                    var canCreate = $(this).find('input[name="permissions[' + menuId + '][create]"]').is(':checked') ? 1 : 0;
                    var canUpdate = $(this).find('input[name="permissions[' + menuId + '][update]"]').is(':checked') ? 1 : 0;
                    var canDelete = $(this).find('input[name="permissions[' + menuId + '][delete]"]').is(':checked') ? 1 : 0;

                    accessData.push({
                        menu_id: menuId,
                        can_read: canRead,
                        can_create: canCreate,
                        can_update: canUpdate,
                        can_delete: canDelete,
                        role_id: roleId
                    });
                });

                var formData = new FormData();
                formData.append('data', JSON.stringify(accessData));
                formData.append('_token', '{{ csrf_token() }}');

                $('#loadingOverlay, #loadingSpinner').show(); // Show loading spinner and overlay

                $.ajax({
                    url: "{{ route('api.update.menu.access') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        snackbar('success', 'Akses berhasil disimpan.', 3000);
                    },
                    error: function(xhr) {
                        snackbar('error', 'Akses Gagal disimpan.', 3000);
                        console.error("An error occurred:", xhr.responseText);
                    },
                    complete: function() {
                        $('#loadingOverlay, #loadingSpinner').hide(); // Hide spinner and overlay after completion
                    }
                });
            });
        });
    </script>
@endsection

@section('styles')
<style>
    #loadingSpinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
    }
    /* Overlay to prevent interaction during loading */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9998;
        display: none;
    }
</style>
@endsection