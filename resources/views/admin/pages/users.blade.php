@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý người dùng</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="x_panel">
                <div class="x_content">
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Tìm kiếm theo tên, email hoặc số điện thoại"
                                        value="{{ $search ?? '' }}">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                @if ($search)
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"
                                        style="margin-left: 10px;">
                                        <i class="fa fa-times"></i> Xóa lọc
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        @forelse ($users as $user)
                            <div class="col-md-4 col-sm-4  profile_details">
                                <div class="well profile_view">
                                    <div class="col-sm-12">
                                        <h4 class="brief text-uppercase"><i>{{ optional($user->role)->name }}</i></h4>
                                        <div class="left col-md-7 col-sm-7">
                                            <h2>{{ $user->name }}</h2>
                                            <p><strong>Email: </strong>{{ $user->email }} </p>
                                            <ul class="list-unstyled">
                                                <li><i class="fa fa-building"></i> Address: {{ $user->address }}</li>
                                                <li><i class="fa fa-phone"></i> Phone : {{ $user->phone_number }}</li>
                                            </ul>
                                        </div>
                                        <div class="right col-md-5 col-sm-5 text-center">
                                            <img src="{{ asset('storage/' . ($user->avatar ?? 'uploads/users/default-avatar.png')) }}"
                                                alt="" class="img-circle img-fluid">
                                        </div>
                                    </div>
                                    <div class=" profile-bottom text-center">
                                        <div class="col-sm-4 emphasis"></div>
                                        <div class=" col-sm-8 emphasis">
                                            @if ($user->role && $user->role->name == 'customer')
                                                <button type="button" class="btn btn-primary btn-sm upgradeStaff"
                                                    data-userid="{{ $user->id }}" data-role="staff">
                                                    <i class="fa fa-user"> </i> Nhân viên
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm upgradeStaff"
                                                    data-userid="{{ $user->id }}" data-role="delivery_staff">
                                                    <i class="fa fa-truck"> </i> Nhân viên giao hàng
                                                </button>
                                                @if ($user->status == 'banned')
                                                    <button type="button" class="btn btn-success btn-sm changeStatus"
                                                        data-userid="{{ $user->id }}" data-status="active">
                                                        <i class="fa fa-check"> </i> Bỏ chặn
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-warning btn-sm changeStatus"
                                                        data-userid="{{ $user->id }}" data-status="banned">
                                                        <i class="fa fa-check"> </i> Chặn
                                                    </button>
                                                @endif

                                                @if ($user->status == 'deleted')
                                                    <button type="button" class="btn btn-success btn-sm changeStatus"
                                                        data-userid="{{ $user->id }}" data-status="active">
                                                        <i class="fa fa-check"> </i> Khôi phục
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm changeStatus"
                                                        data-userid="{{ $user->id }}" data-status="deleted">
                                                        <i class="fa fa-check"> </i> Xóa
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-md-12">
                                <div class="alert alert-info text-center">
                                    Không tìm thấy người dùng phù hợp.
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection
