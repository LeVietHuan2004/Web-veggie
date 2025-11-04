@extends('layouts.admin')

@section('title', 'Quản lý mã giảm giá')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý mã giảm giá</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Thêm mã mới</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form action="{{ route('admin.coupons.store') }}" method="POST" class="form-horizontal form-label-left">
                                @csrf
                                <div class="item form-group">
                                    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-code">Mã giảm giá <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-6">
                                        <input type="text" id="coupon-code" name="code" value="{{ old('code') }}" required class="form-control">
                                    </div>
                                    @error('code')
                                        <div class="col-md-6 col-sm-3"><span class="text-danger">{{ $message }}</span></div>
                                    @enderror
                                </div>
                                <div class="item form-group">
                                    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-percentage">Giảm (%) <span class="required">*</span></label>
                                    <div class="col-md-2 col-sm-3">
                                        <input type="number" id="coupon-percentage" name="discount_percentage" min="1" max="100" value="{{ old('discount_percentage', 5) }}" required class="form-control">
                                    </div>
                                    @error('discount_percentage')
                                        <div class="col-md-6 col-sm-6"><span class="text-danger">{{ $message }}</span></div>
                                    @enderror
                                </div>
                                <div class="item form-group">
                                    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-expires">Thời hạn</label>
                                    <div class="col-md-4 col-sm-6">
                                        <input type="datetime-local" id="coupon-expires" name="expires_at" value="{{ old('expires_at') }}" class="form-control">
                                    </div>
                                    @error('expires_at')
                                        <div class="col-md-6 col-sm-3"><span class="text-danger">{{ $message }}</span></div>
                                    @enderror
                                </div>
                                <div class="item form-group">
                                    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-usage-limit">Giới hạn lượt dùng</label>
                                    <div class="col-md-2 col-sm-3">
                                        <input type="number" id="coupon-usage-limit" name="usage_limit" min="1" value="{{ old('usage_limit') }}" class="form-control">
                                    </div>
                                    @error('usage_limit')
                                        <div class="col-md-6 col-sm-6"><span class="text-danger">{{ $message }}</span></div>
                                    @enderror
                                </div>
                                <div class="item form-group">
                                    <label class="col-form-label col-md-2 col-sm-3 label-align">Trạng thái</label>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}> Kích hoạt ngay
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 offset-md-2">
                                        <button type="submit" class="btn btn-success">Thêm mã</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách mã giảm giá</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Mã</th>
                                            <th>Giảm (%)</th>
                                            <th>Thời hạn</th>
                                            <th>Đã dùng</th>
                                            <th>Giới hạn</th>
                                            <th>Trạng thái</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($coupons as $coupon)
                                            <tr>
                                                <td>{{ $coupon->code }}</td>
                                                <td>{{ $coupon->discount_percentage }}%</td>
                                                <td>
                                                    @if ($coupon->expires_at)
                                                        {{ $coupon->expires_at->format('d/m/Y H:i') }}
                                                        @if ($coupon->isExpired())
                                                            <span class="badge badge-danger">Hết hạn</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">Không giới hạn</span>
                                                    @endif
                                                </td>
                                                <td>{{ $coupon->times_used }}</td>
                                                <td>{{ $coupon->usage_limit ?? 'Không giới hạn' }}</td>
                                                <td>
                                                    @if ($coupon->is_active)
                                                        <span class="badge badge-success">Đang kích hoạt</span>
                                                    @else
                                                        <span class="badge badge-secondary">Đã tắt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editCouponModal-{{ $coupon->id }}">
                                                        <i class="fa fa-edit"></i> Sửa
                                                    </button>
                                                </td>
                                                <td>
                                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa mã này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="editCouponModal-{{ $coupon->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cập nhật mã giảm giá</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="coupon-code-{{ $coupon->id }}">Mã giảm giá</label>
                                                                    <input type="text" id="coupon-code-{{ $coupon->id }}" name="code" class="form-control" value="{{ $coupon->code }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="coupon-percentage-{{ $coupon->id }}">Giảm (%)</label>
                                                                    <input type="number" id="coupon-percentage-{{ $coupon->id }}" name="discount_percentage" class="form-control" min="1" max="100" value="{{ $coupon->discount_percentage }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="coupon-expires-{{ $coupon->id }}">Thời hạn</label>
                                                                    <input type="datetime-local" id="coupon-expires-{{ $coupon->id }}" name="expires_at" class="form-control" value="{{ optional($coupon->expires_at)->format('Y-m-d\TH:i') }}">
                                                                    <small class="form-text text-muted">Để trống nếu không giới hạn thời gian.</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="coupon-usage-limit-{{ $coupon->id }}">Giới hạn lượt dùng</label>
                                                                    <input type="number" id="coupon-usage-limit-{{ $coupon->id }}" name="usage_limit" class="form-control" min="1" value="{{ $coupon->usage_limit }}">
                                                                    <small class="form-text text-muted">Để trống nếu không giới hạn số lượt.</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="checkbox">
                                                                        <label>
                                                                            <input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}> Kích hoạt
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Chưa có mã giảm giá nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->

    @if (session('success'))
        <script>
            toastr.success({{ json_encode(session('success')) }});
        </script>
    @endif

    @if ($errors->any())
        <script>
            toastr.error({{ json_encode($errors->first()) }});
        </script>
    @endif
@endsection