<!-- resources/views/content/menu/menu.blade.php -->
@extends('layouts.app')

@section('title', 'Menu Management')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Menu List</h6>
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
                                @foreach ($menus as $menu)
                                    <tr>
                                        <td>
                                            <!-- Sort buttons -->
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success me-2"><i class="fa-solid fa-arrow-up"></i></button>
                                                <button type="button" class="btn btn-sm btn-success"><i class="fa-solid fa-arrow-down"></i></button>
                                            </div>                                            
                                        </td>
                                        <td>
                                            <!-- Icon -->
                                            <i class="fa-solid fa-{{ $menu->menu_icon }}"></i>
                                        </td>
                                        <td>
                                            <!-- Judul (Menu Name) -->
                                            {{ $menu->menu }}
                                        </td>
                                        <td>
                                            <!-- Type (Menu/Sub Menu) -->
                                            @if ($menu->submenus->count() > 0)
                                                <span class="badge bg-success">Sub Menu</span>
                                            @else
                                                <span class="badge bg-primary">Menu</span>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Redirect URL -->
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
                                            <!-- Action buttons -->
                                            <a href="{{ url('menus.edit', $menu->menu_id) }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-pencil"></i></a>
                                            <form action="{{ url('menus.destroy', $menu->menu_id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-delete"></i></button>
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
    </div>
@endsection
