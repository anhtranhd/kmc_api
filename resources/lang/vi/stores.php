<?php

return [
    'title' => [
        'store' => 'cửa hàng/ nhà cung cấp',
        'stores' => 'Danh sách cửa hàng/ nhà cung cấp',
        'index' => 'Cửa hàng/ nhà cung cấp',
        'create' => 'Thêm mới cửa hàng/ nhà cung cấp',
        'show' => 'Thông tin cửa hàng/ nhà cung cấp',
        'edit' => 'Chỉnh sửa cửa hàng/ nhà cung cấp'
    ],
    'table' => [
        'search' => 'Tên cửa hàng, tên đăng nhập',
        'name' => 'Tên cửa hàng/ nhà cung cấp',
        'username' => 'Tên đăng nhập (email)',
        'email' => 'Email',
        'phone' => 'Số điện thoại',
        'status' => 'Trạng thái',
        'created_at' => 'Ngày tạo mới',
        'action' => 'Thao tác',
        'empty_data_table' => 'Không có dữ liệu'
    ],
    'status' => [
        'pending' => 'Chờ duyệt',
        'lock' => 'Bị khóa',
        'active' => 'Đang hoạt động',
        'new' => 'Tạo mới',
        'deleted' => 'Đã xóa',
        'all' => '-- Chọn trạng thái --'
    ],
    'form' => [
        'info store' => 'Thông tin cửa hàng',
        'name' => 'Tên cửa hàng/ nhà cung cấp',
        'full_name' => 'Họ tên người đại diện',
        'city' => 'Địa chỉ',
        'address' => 'Địa chỉ chi tiết',
        'categories' => 'Ngành hàng kinh doanh chính',
        'description' => 'Mô tả cửa hàng',

        'info user' => 'Thông tin tài khoản',
        'user_name' => 'Tên đăng nhập (email)',
        'email' => 'Email',
        'phone' => 'Số điện thoại',

        'avatar' => 'Ảnh đại diện',
        'background' => 'Ảnh bìa',

        'info image' => 'Mô tả hình ảnh',
        'file' => 'Thêm hình ảnh',

        'info video' => 'Mô tả video',
        'placeholder' => [
            'name' => 'Nhập tên cửa hàng/ nhà cung cấp',
            'full_name' => 'Nhập họ tên người đại diện',
            'city' => '-- Chọn địa chỉ --',
            'address' => 'Nhập địa chỉ chi tiết',

            'user_name' => 'Nhập tên đăng nhập (email)',
            'phone' => 'Nhập số điện thoại',
            'email' => 'Nhập email'
        ]
    ],
    'categories' => [
        'table' => [
            'name' => 'Tên',
            'type' => 'Ngành hàng chính'
        ]
    ],
    'messages' => [
        'confirm_delete' => 'Bạn có chắc chắn muốn xóa cửa hàng này không?',
        'alert' => 'Cửa hàng này đang hiển thị, không được phép xóa!',
        'created' => 'Thêm mới thành công',
        'create_fail' => 'Thêm mới thất bại',
        'updated' => 'Cập nhật thành công',
        'update_fail' => 'Cập nhật thất bại',
        'delete_fail' => 'Xóa thất bại',
        'status' => [
            '1' => 'Khóa cửa hàng/ nhà cung cấp',
            '2' => 'Mở khóa cửa hàng/ nhà cung cấp',
            'confirm' => [
                '1' => 'Bạn có chắc chắn muốn khóa cửa hàng/ nhà cung cấp này?',
                '2' => 'Bạn có chắc chắn muốn mở khóa cửa hàng/ nhà cung cấp này?'
            ],
            'success' => [
                '1' => 'Mở khóa cửa hàng/ nhà cung cấp thành công',
                '2' => 'Khóa cửa hàng/ nhà cung cấp thành công'
            ]
        ]
    ],
    'validation' => [
        'name' => [
            'required' => 'Tên cửa hàng là bắt buộc',
            'max' => 'Tên cửa hàng không được quá :max ký tự',
            'unique' => 'Tên cửa hàng bị trùng'
        ],
        'categories' => [
            'required' => 'Ngành hàng kinh doanh chính là bắt buộc',
        ],
        'description' => [
            'max' => 'Mô tả cửa hàng không được quá :max ký tự'
        ],
        'address' => [
            'max' => 'Địa chỉ chi tiết không được quá :max ký tự',
        ],
        'user_name' => [
            'required' => 'Tên đăng nhập là bắt buộc',
            'unique' => 'Tên đăng nhập bị trùng',
            'max' => 'Tên đăng nhập không được quá :max ký tự',
            'email' => 'Email không đúng định dạng',
        ],
        'full_name' => [
            'max' => 'Họ tên người đại diện không được quá :max ký tự'
        ],
        'avatar' => [
            'mines' => 'File bạn chọn chưa đúng định dạng. Vui lòng chọn các file có định dạng jpeg, jpg, png',
            'max' => 'Dung lượng file không được vượt quá :max'
        ],
        'background' => [
            'mines' => 'File bạn chọn chưa đúng định dạng. Vui lòng chọn các file có định dạng jpeg, jpg, png',
            'max' => 'Dung lượng file không được vượt quá :max'
        ],
        'files' => [
            'mines' => 'File bạn chọn chưa đúng định dạng. Vui lòng chọn các file có định dạng jpeg, jpg, png',
            'max' => 'Dung lượng file không được vượt quá :max'
        ],
        'email' => [
            'required' => 'Email là bắt buộc',
            'email' => 'Email không đúng định dạng',
            'max' => 'Email không được quá :max ký tự',
            'unique' => 'Email bị trùng'
        ],
        'phone' => [
            'max' => 'Số điện thoại không được quá :max ký tự',
            'regex' => 'Số điện thoại không đúng định dạng'
        ],
        'video' => [
            'mines' => 'File bạn chọn chưa đúng định dạng. Vui lòng chọn các file có định dạng mp4',
            'max' => 'Dung lượng file không được vượt quá :max'
        ]
    ],
    'note' => [
        'address' => '-- Chọn địa chỉ --',
        'category' => '-- Chọn từ danh mục ngành hàng --',
        'email' => 'Tài khoản của cửa hàng'
    ]
];
