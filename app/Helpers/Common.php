<?php



function findDuplicates($arr) {
    // Đếm số lần xuất hiện của từng phần tử
    $counts = array_count_values($arr);
    
    // Lọc ra các phần tử xuất hiện nhiều hơn 1 lần
    $duplicates = array_filter($counts, function($count) {
        return $count > 1;
    });

    // Lấy ra các giá trị phần tử xuất hiện nhiều hơn 1 lần
    return array_keys($duplicates);
}

function menuConfiguration($row, $options = []) {
    $html = ' <a href="'.route('configuration.edit',$row['_id']).'" class="dropdown-item call_ajax_modal">Sửa </a>';
   
    $html .= ' <a href="#" method="delete" content="Bạn có chắc muốn xóa không ?"  action="'.route('configuration.destroy',$row['_id']).'"  class="dropdown-item quick-action-confirm">Xóa </a>';
    return $html;
}