<?php

if (!function_exists('utf8Convert')) {
    function utf8Convert($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        return $str;
    }
}

if (!function_exists('deleteFolderAll')) {
    function deleteFolderAll($str)
    {
        //It it's a file.
        if (is_file($str)) {
            //Attempt to delete it.
            return unlink($str);
        } //If it's a directory.
        elseif (is_dir($str)) {
            //Get a list of the files in this directory.
            $scan = glob(rtrim($str, '/') . '/*');
            //Loop through the list of files.
            foreach ($scan as $index => $path) {
                //Call our recursive function.
                deleteFolderAll($path);
            }
            //Remove the directory itself.
            return @rmdir($str);
        }
    }

}

if (!function_exists('recursiveElements')) {
    function recursiveElements($data = []) {
        $elements = [];
        $tree = [];
        foreach ($data as &$element) {
            $element['children'] = [];
            $id = $element['id'];
            $parent_id = $element['parent_id'];
            $elements[$id] =& $element;
            if (isset($elements[$parent_id])) { $elements[$parent_id]['children'][] =& $element; }
            else { $tree[] =& $element; }
        }

        return $tree;
    }
}

if (!function_exists('flattenDown')) {
    function flattenDown($data = [], $index = 0) {
        $elements = [];
        foreach ($data as $element) {
            $temp = [];
            $temp['id'] = $element['id'];
            $temp['name'] = str_repeat('-- ', $index) . $element['name'];

            $elements[] = $temp;
            if (!empty($element['children'])) {
                $elements = array_merge($elements, flattenDown($element['children'], $index+1));
            }
        }

        return $elements;
    }
}
