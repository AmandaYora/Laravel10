@extends('layouts.app')

@section('title', 'Menu Management')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title">Menu List</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal" id="addMenuButton">
                        Tambah
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="menuTable" class="table">
                        <thead>
                            <tr>
                                <th>SORT</th>
                                <th>ICON</th>
                                <th>JUDUL</th>
                                <th>TYPE</th>
                                <th>REDIRECT</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($listMenu as $index => $menu)
                            <tr>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-success me-2 arrow-up" data-menu-id="{{ $menu->menu_id }}" {{ $index == 0 ? 'disabled' : '' }}>
                                            <i data-feather="arrow-up"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success arrow-down" data-menu-id="{{ $menu->menu_id }}" {{ $index == count($menus) - 1 ? 'disabled' : '' }}>
                                            <i data-feather="arrow-down"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <i data-feather="{{ $menu->menu_icon }}"></i>
                                </td>
                                <td>{{ $menu->menu }}</td>
                                <td>
                                    {{ $menu->menuType->menu_type }}
                                </td>
                                <td>
                                    @if ($menu->submenus->count() > 0)
                                    @foreach ($menu->submenus as $submenu)
                                    <span class="badge bg-info">{{ $submenu->submenu }}</span>
                                    <span class="badge bg-light text-dark">{{ $submenu->submenu_redirect }}</span><br>
                                    @endforeach
                                    @else
                                    <span class="badge bg-light text-dark">{{ $menu->menu_redirect }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary me-2 btn-sm edit-menu-button" data-menu-id="{{ $menu->menu_id }}" data-menu="{{ json_encode($menu) }}" data-bs-toggle="modal" data-bs-target="#addMenuModal"><i data-feather="edit"></i></button>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-menu-button" data-menu-id="{{ $menu->menu_id }}"><i data-feather="trash-2"></i></button>
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

<!-- Modal Tambah/Edit Data -->
<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuModalLabel">Tambah Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMenuForm">
                    @csrf
                    <input type="hidden" id="menuId" name="menu_id">
                    <div class="mb-3">
                        <label for="menuIcon" class="form-label">Icon Menu</label><br>
                        <small class="form-text text-muted">
                            Cari & salin nama dari Icon <a href="https://feathericons.com/" target="_blank">https://feathericons.com/</a>
                        </small>
                        <input type="text" class="form-control" id="menuIcon" name="menu_icon" required placeholder="fe-home">
                    </div>                    
                    <div class="mb-3">
                        <label for="menuName" class="form-label">Nama Menu</label>
                        <input type="text" class="form-control" id="menuName" name="menu" required placeholder="Judul Menu">
                    </div>
                    <div class="mb-3">
                        <label for="menuRedirect" class="form-label">Redirect URL</label>
                        <input type="text" class="form-control" id="menuRedirect" name="menu_redirect" required placeholder="/redirect">
                    </div>
                    <div class="mb-3">
                        <label for="menuType" class="form-label">Type</label>
                        <select class="form-select" id="menuType" name="menu_type_id" required>
                            @foreach ($type as $t)
                                <option value="{{ $t->menu_type_id }}">{{ $t->menu_type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="submenuSection" class="mb-3" style="display: none;">
                        <button type="button" class="btn btn-light btn-sm mb-2" id="addSubMenuButton">
                            Tambah Sub Menu <i data-feather="plus"></i>
                        </button>
                        <div id="submenuContainer"></div>
                    </div>
                    <div id="additionalPlaceholder" class="mb-3" style="display: none;">
                        <label for="additionalInfo" class="form-label">Column Table</label>
                        <input type="text" class="form-control" id="additionalInfo" name="additional_info" placeholder="user_id, name, username, ...">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveMenuButton">Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        feather.replace(); // Replace all feather icons
        $('#additionalPlaceholder').show();
        $('#menuType').on('change', function() {
            if ($(this).val() == '2') {
                $('#submenuSection').show();
                $('#additionalPlaceholder').hide();
            } else {
                $('#submenuSection').hide();
                $('#submenuContainer').empty();
                $('#additionalPlaceholder').show();
            }
        });

        $('#addMenuButton').on('click', function() {
            $('#addMenuModalLabel').text('Tambah Menu');
            $('#menuId').val('');
            $('#addMenuForm')[0].reset();
            $('#submenuContainer').empty();
            $('#submenuSection').hide();
        });

        $(document).on('click', '.edit-menu-button', function() {
            var menu = $(this).data('menu');
            $('#addMenuModalLabel').text('Edit Menu');
            $('#menuId').val(menu.menu_id);
            $('#menuIcon').val(menu.menu_icon);
            $('#menuName').val(menu.menu);
            $('#menuRedirect').val(menu.menu_redirect);
            $('#menuType').val(menu.menu_type_id).change();
            $('#submenuContainer').empty();

            if (menu.submenus.length > 0) {
                $('#submenuSection').show();
                $.each(menu.submenus, function(index, submenu) {
                    var subMenuHtml = `
                        <div class="input-group mb-2 submenu-item">
                            <input type="text" class="form-control" name="submenus[][submenu]" value="${submenu.submenu}" placeholder="Sub Menu" required>
                            <input type="text" class="form-control" name="submenus[][submenu_redirect]" value="${submenu.submenu_redirect}" placeholder="/redirect" required>
                            <button class="btn btn-danger btn-remove-submenu" type="button"><i data-feather="trash-2"></i></button>
                        </div>`;
                    $('#submenuContainer').append(subMenuHtml);
                });
            } else {
                $('#submenuSection').hide();
            }

            $('#addMenuModal').modal('show');
        });

        $('#addSubMenuButton').on('click', function() {
            var subMenuHtml = `
                <div class="input-group mb-2 submenu-item">
                    <input type="text" class="form-control" name="submenus[][submenu]" placeholder="Sub Menu" required>
                    <input type="text" class="form-control" name="submenus[][submenu_redirect]" placeholder="/redirect" required>
                    <button class="btn btn-danger btn-remove-submenu" type="button"><i data-feather="trash-2"></i></button>
                </div>`;
            $('#submenuContainer').append(subMenuHtml);
        });

        $(document).on('click', '.btn-remove-submenu', function() {
            $(this).closest('.submenu-item').remove();
        });

        $('#saveMenuButton').on('click', function() {
            var menuId = $('#menuId').val();
            var isUpdate = menuId ? true : false;

            var menuData = {
                menu_icon: $('#menuIcon').val(),
                menu: $('#menuName').val(),
                menu_redirect: $('#menuRedirect').val(),
                menu_type_id: $('#menuType').val(),
                column_table: $('#additionalInfo').val(),
                submenus: []
            };

            if (isUpdate) {
                menuData.menu_id = menuId;
            }

            $('#submenuContainer .submenu-item').each(function() {
                var submenu = {
                    submenu: $(this).find('input[name="submenus[][submenu]"]').val(),
                    submenu_redirect: $(this).find('input[name="submenus[][submenu_redirect]"]').val()
                };
                menuData.submenus.push(submenu);
            });

            $.ajax({
                url: "{{ route('api.menu.save') }}",
                type: 'POST',
                data: {
                    data: JSON.stringify(menuData),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.code === 0) {
                        $('#addMenuModal').modal('hide');
                        updateTable(response.data);
                        snackbar('success', 'Menu berhasil disimpan.', 3000);
                        setTimeout(function() {
                                window.location.reload();
                            }, 3000);
                    } else {
                        snackbar('error', 'Terjadi kesalahan: ' + response.info, 5000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    snackbar('error', 'Terjadi kesalahan saat menyimpan data.', 5000);
                }
            });
        });

        $(document).on('click', '.delete-menu-button', function() {
            var menuId = $(this).data('menu-id');

            if (confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
                $.ajax({
                    url: "{{ route('api.menu.destroy') }}",
                    type: 'POST',
                    data: {
                        data: JSON.stringify({ "menu_id": menuId }),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.code === 0) {
                            updateTable(response.data);
                            snackbar('success', 'Menu berhasil dihapus.', 3000);
                            setTimeout(function() {
                                window.location.reload();
                            }, 3000);
                        } else {
                            snackbar('error', 'Terjadi kesalahan: ' + response.info, 5000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        snackbar('error', 'Terjadi kesalahan saat menghapus data.', 5000);
                    }
                });
            }
        });

        function sendMenuRequest(route, menuId) {
            $.ajax({
                url: route,
                type: 'POST',
                data: {
                    data: JSON.stringify({ "menu_id": menuId }),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.code === 0) {
                        updateTable(response.data);
                        snackbar('success', 'Menu berhasil diperbarui.', 3000);
                    } else {
                        snackbar('error', 'Terjadi kesalahan: ' + response.info, 5000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    snackbar('error', 'Terjadi kesalahan saat mengirim data.', 5000);
                }
            });
        }

        function updateTable(menus) {
            var tbody = $('#menuTable tbody');
            tbody.empty();

            $.each(menus, function(index, menu) {
                var row = '<tr>' +
                    '<td>' +
                        '<div class="btn-group" role="group">' +
                            '<button type="button" class="btn btn-sm btn-success me-2 arrow-up" data-menu-id="' + menu.menu_id + '"' + (index === 0 ? ' disabled' : '') + '><i data-feather="arrow-up"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-success arrow-down" data-menu-id="' + menu.menu_id + '"' + (index === menus.length - 1 ? ' disabled' : '') + '><i data-feather="arrow-down"></i></button>' +
                        '</div>' +
                    '</td>' +
                    '<td><i data-feather="' + menu.menu_icon + '"></i></td>' +
                    '<td>' + menu.menu + '</td>' +
                    '<td>' +
                        (menu.submenus.length > 0 ? '<span class="badge bg-success">Sub Menu</span>' : '<span class="badge bg-primary">Menu</span>') +
                    '</td>' +
                    '<td>';

                if (menu.submenus.length > 0) {
                    $.each(menu.submenus, function(subIndex, submenu) {
                        row += '<span class="badge bg-info">' + submenu.submenu + '</span>' +
                               '<span class="badge bg-light text-dark">' + submenu.submenu_redirect + '</span><br>';
                    });
                } else {
                    row += '<span class="badge bg-light text-dark">' + menu.menu_redirect + '</span>';
                }

                row += '</td>' +
                    '<td>' +
                        '<button type="button" class="btn btn-outline-primary btn-sm edit-menu-button" data-menu-id="' + menu.menu_id + '" data-menu=\'' + JSON.stringify(menu) + '\'><i data-feather="edit"></i></button>' +
                        '<button type="button" class="btn btn-outline-danger btn-sm delete-menu-button" data-menu-id="' + menu.menu_id + '"><i data-feather="trash-2"></i></button>' +
                    '</td>' +
                '</tr>';

                tbody.append(row);
            });

            bindButtonEvents();
        }

        function bindButtonEvents() {
            $('.arrow-up').off('click').on('click', function() {
                var menuId = $(this).data('menu-id');
                sendMenuRequest("{{ route('api.menu.up') }}", menuId);
            });

            $('.arrow-down').off('click').on('click', function() {
                var menuId = $(this).data('menu-id');
                sendMenuRequest("{{ route('api.menu.down') }}", menuId);
            });
        }

        bindButtonEvents();

        feather.replace();
    });
</script>
@endsection
