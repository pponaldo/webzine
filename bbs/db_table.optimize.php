<?php
if (!defined('_WONDER_')) exit; // 개별 페이지 접근 불가

// 최고관리자일 때만 실행
if($config['cf_admin'] != $member['mb_id'] || $is_admin != 'super')
    return;

// 실행일 비교
if(isset($config['cf_optimize_date']) && $config['cf_optimize_date'] >= G5_TIME_YMD)
    return;

// 설정일이 지난 접속자로그 삭제
if($config['cf_visit_del'] > 0) {
    $tmp_before_date = date("Y-m-d", G5_SERVER_TIME - ($config['cf_visit_del'] * 86400));
    $sql = " delete from {$g5['visit_table']} where vi_date < '$tmp_before_date' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['visit_table']}`, `{$g5['visit_sum_table']}` ");
}

// 설정일이 지난 인기검색어 삭제
if($config['cf_popular_del'] > 0) {
    $tmp_before_date = date("Y-m-d", G5_SERVER_TIME - ($config['cf_popular_del'] * 86400));
    $sql = " delete from {$g5['popular_table']} where pp_date < '$tmp_before_date' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['popular_table']}` ");
}

// 설정일이 지난 최근게시물 삭제
if($config['cf_new_del'] > 0) {
    $sql = " delete from {$g5['board_new_table']} where (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS(bn_datetime)) > '{$config['cf_new_del']}' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['board_new_table']}` ");
}

// 설정일이 지난 쪽지 삭제
if($config['cf_memo_del'] > 0) {
    $sql = " delete from {$g5['memo_table']} where (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS(me_send_datetime)) > '{$config['cf_memo_del']}' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['memo_table']}` ");
}

// 탈퇴회원 자동 삭제
if($config['cf_leave_day'] > 0) {
    $sql = " select mb_id from {$g5['member_table']} where (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS(mb_leave_date)) > '{$config['cf_leave_day']}' ";
    $result = sql_query($sql);
    while ($row=sql_fetch_array($result))
    {
        // 회원자료 삭제
        member_delete($row['mb_id']);
    }
}

// 실행일 기록
if(isset($config['cf_optimize_date'])) {
    sql_query(" update {$g5['config_table']} set cf_optimize_date = '".G5_TIME_YMD."' ");
}
?>