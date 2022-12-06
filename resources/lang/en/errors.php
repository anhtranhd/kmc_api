<?php

return [
    'application'   => '',
    'unauthorized'     => 'Đăng nhập để được sử dụng chức năng này',
    'forbidden'  => '403 forbidden',
    'notFound' => 'Không tìm thấy',
    'credentials' => [
        'invalid' => 'Sai tên đăng nhập/ mật khẩu. Vui lòng nhập lại',
        'inactive' => 'Tài khoản của bạn đang bị khóa'
    ],
    'validation' => [
        'name' => [
            'required' => 'Tên tài khoản là bắt buộc',
        ],
        'email' => [
            'invalid' => 'Tên email không hợp lệ',
            'required' => 'Tên email là bắt buộc',
            'registered' => 'Email đã tồn tại',
            'notRegistered' => 'Email chưa đăng ký tài khoản'
        ],
        'oldPassword' => [
            'required' => 'Mật khẩu cũ là bắt buộc',
            'invalid' => 'Mật khẩu cũ không đúng',
        ],
        'password' => [
            'invalid' => 'Mật khẩu không đúng, vui lòng nhập lại',
            'min8' => 'Mật khẩu tối thiểu gồm 8 kí tự',
            'required' => 'Mật khẩu là bắt buộc',
            'complexity' => ''
        ],
        'gender' => [
            'required' => 'Giới tính là bắt buộc',
        ],
        'retypePassword' => [
            'required' => 'Mật khẩu nhắc lại là bắt buộc',
            'same' => 'Mật khẩu nhắc lại không đúng'
        ],
        'activateEmail' => [
            'required' => '',
            'invalid' => '',
            'notRegistered' => ''
        ],
        'activateCode' => [
            'activated' => '',
            'sendCooldown' => '',
            'wrong' => ''
        ],
        'rate' => [
            'required' => 'Số đánh giá là bắt buộc',
            'integer' => 'Tham số đánh giá phải là dạng số',
        ],
        'content' => [
            'required' => 'Nội dung đánh giá là bắt buộc',
            'string' => 'Tham số nội dung phải là dạng chuỗi',
            'max' => 'Nội dung tối đa 300 kí tự'
        ],
        'status' => [
            'required' => 'Trạng thái là bắt buộc',
            'integer' => 'Trạng thái phải là dạng số',
        ],
    ],
    'artifacts' => [
        'not found' => 'Sản phẩm không tồn tại',
        'id required' => 'Sản phẩm là bắt buộc',
        'id invalid' => 'Sản phẩm không hợp lệ',
        'commented' => 'Bạn đã đánh giá cho sản phẩm này rồi.',
        'review not found' => 'Không tìm thấy đánh giá sản phẩm'
    ],
    'content' => [
        'not found' => 'Nội dung không tồn tại',
    ],
    'store' => [
        'not found' => 'Không tìm thấy cửa hàng',
        'invalid' => 'Thông tin cửa hàng không hợp lệ'
    ],
    'categories' => [
        'not found' => 'Danh mục ngành hàng không tồn tại',
        'id required' => 'ID danh mục ngành hàng là bắt buộc'
    ],
];
