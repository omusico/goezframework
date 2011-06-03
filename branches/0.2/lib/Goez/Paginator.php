<?php
/**
 * Goez
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 * @version    $Id$
 */

/**
 * 分頁類別
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Paginator implements Countable, IteratorAggregate
{

}

// ----------------------
// 製做分頁 Bar 類別
// ----------------------
// 使用方式：
//
        // 1.取得目前資料集，並產生分頁物件
// $query = "SQL"
// $rs = sql_query($query);
// $total_nums = sql_num_rows($rs);
// $navbar = new CNavBar($total_nums, 10, 5);
// # CNavBar(資料數, 每頁顯示幾筆資料, 最多顯示幾個頁數選項);
// $mybar = $navbar->makeBar();
// $rs = sql_query($query . $mybar['sql']);
//
        // 2.輸入分頁連結
// echo $mybar['center'];
// echo $mybar['left'];
// echo $mybar['right'];
// echo $mybar['current'];
// echo $mybar['total'];
//

class PageBar
{

    // 目前所在頁碼
    var $current;
    // 所有的資料數量 (rows)
    var $total;
    // 每頁顯示幾筆資料
    var $limit;
    // 目前在第幾層的頁數選項？
    var $pCurrent;
    // 總共分成幾頁？
    var $pTotal;
    // 每一層最多有幾個頁數選項可供選擇，如：3 = {$1]$2]$3]}
    var $pLimit;
    // var $prev  = '<img src="icon/button_prev.gif" border="0" alt="上一頁">';
    // var $next  = '<img src="icon/button_next.gif" border="0" alt="下一頁">';
    // var $first = '<img src="icon/button_first.gif" border="0" alt="最前頁">';
    // var $last  = '<img src="icon/button_last.gif" border="0" alt="最後頁">';
    var $prev = '上一頁';
    var $next = '下一頁';
    var $prev_layer = '<<';
    var $next_layer = '>>';
    var $first = '第一頁';
    var $last = '最後一頁';
    var $bottons = array();
    // 要使用的 URL 頁數參數名？
    var $url_page = "gotopage";
    // 要使用的 URL 讀取時間參數名？
    var $url_loadtime = "loadtime";
    // 會使用到的 URL 變數名，給 process_query() 過濾用的。
    var $used_query = array();
    // 目前頁數顏色
    var $act_color = "#990000";
    var $query_str; // 存放 URL 參數列

    function PageBar($total, $limit, $page_limit)
    {
        $this->limit = $limit;
        $this->total = $total;
        $this->pLimit = $page_limit;
    }

    function init()
    {

        $this->used_query = array($this->url_page, $this->url_loadtime);
        $this->query_str = $this->processQuery($this->used_query);
        $this->glue = ($this->query_str == "") ? '?' :
                '&';
        $this->current = (isset($_GET["$this->url_page"])) ? $_GET["$this->url_page"] :
                1;
        $this->pTotal = ceil($this->total / $this->limit);
        $this->pCurrent = ceil($this->current / $this->pLimit);
    }

    // 設定
    function set($active_color = "none", $buttons = "none")
    {
        if ($active_color != "none") {
            $this->act_color = $active_color;
        }
        if ($buttons != "none") {
            $this->buttons = $buttons;
            $this->prev = $this->buttons['prev'];
            $this->next = $this->buttons['next'];
            $this->prev_layer = $this->buttons['prev_layer'];
            $this->next_layer = $this->buttons['next_layer'];
            $this->first = $this->buttons['first'];
            $this->last = $this->buttons['last'];
        }
    }

    // 處理 URL 的參數，過濾會使用到的變數名稱
    function processQuery($used_query)
    {

        // 將 URL 字串分離成二維陣列
        $vars = explode("&", $_SERVER['QUERY_STRING']);
        for ($i = 0; $i < count($vars); $i++) {
            $var[$i] = explode("=", $vars[$i]);
        }
        // 過濾要使用的 URL 變數名稱
        for ($i = 0; $i < count($var); $i++) {
            for ($j = 0; $j < count($used_query); $j++) {
                if (isset($var[$i][0]) && $var[$i][0] == $used_query[$j])
                    $var[$i] = array();
            }
        }
        // 合併變數名與變數值
        for ($i = 0; $i < count($var); $i++) {
            $vars[$i] = implode("=", $var[$i]);
        }
        // 合併為一完整的 URL 字串
        $processed_query = "";
        for ($i = 0; $i < count($vars); $i++) {
            $glue = ($processed_query == "") ? '?' :
                    '&';
            // 開頭第一個是 '?' 其餘的才是 '&'
            if ($vars[$i] != "")
                $processed_query .= $glue . $vars[$i];
        }
        return $processed_query;
    }

    // 製作 sql 的 query 字串 (LIMIT)
    function sqlQuery()
    {
        $row_start = ($this->current * $this->limit) - $this->limit;
        $sql_query = " LIMIT {$row_start}, {$this->limit}";
        return $sql_query;
    }

    // 製作 bar
    function makeBar($url_page = "none")
    {

        if ($url_page != "none") {
            $this->url_page = $url_page;
        }
        $this->init();

        // 取得目前時間
        $loadtime = '&loadtime=' . time();

        // 取得目前頁框(層)的第一個頁數啟始值，如 6 7 8 9 10 = 6
        $i = ($this->pCurrent * $this->pLimit) - ($this->pLimit - 1);

        $bar_center = "";
        while ($i <= $this->pTotal && $i <= ($this->pCurrent * $this->pLimit)) {
//                    if ($i == $this->current) $bar_center = "{$bar_center}<font color="{$this->act_color}">${$i}]</font>";
//                    else $bar_center .= " <a href="{$_SERVER['PHP_SELF']}{$this->query_str}{$this->glue}{$this->url_page}={$i}{$loadtime}" title="{$i}">{$i}</a> n";
            $i++;
        }
        $bar_center = $bar_center . "";

        // 往前跳一頁
        if ($this->current <= 1) {
            $bar_left = " {$this->prev} n";
            $bar_first = " {$this->first} n";
        } else {
            $i = $this->current - 1;
//                    $bar_left = " <a href="{$_SERVER['PHP_SELF']}{$this->query_str}{$this->glue}{$this->url_page}={$i}{$loadtime}" title="前 1 頁">{$this->prev}</a> n";
//                    $bar_first = " <a href="{$_SERVER['PHP_SELF']}{$this->query_str}{$this->glue}{$this->url_page}=1{$loadtime}" title="最前頁">{$this->first}</a> n";
        }

        // 往後跳一頁
        if ($this->current >= $this->pTotal) {
            $bar_right = " {$this->next} n";
            $bar_last = " {$this->last} n";
        } else {
            $i = $this->current + 1;
//                    $bar_right = " <a href="{$_SERVER['PHP_SELF']}{$this->query_str}{$this->glue}{$this->url_page}={$i}{$loadtime}" title="後 1 頁">{$this->next}</a> n";
//                    $bar_last = " <a href="{$_SERVER['PHP_SELF']}{$this->query_str}{$this->glue}{$this->url_page}={$this->pTotal}{$loadtime}" title="最後頁">{$this->last}</a> n";
        }

        // 往前跳一整個頁框(層)
        if (($this->current - $this->pLimit) < 1) {
            $bar_l = " {$this->prev_layer} n";
        } else {
            $i = $this->current - $this->pLimit;
//                    $bar_l = " <a href="{$_SERVER['PHP_SELF']}{$this->query_str}{$this->glue}{$this->url_page}={$i}{$loadtime}" title="前 {$this->pLimit} 頁">{$this->prev_layer}</a> ";
        }

        //往後跳一整個頁框(層)
        if (($this->current + $this->pLimit) > $this->pTotal) {
            $bar_r = " {$this->next_layer} n";
        } else {
            $i = $this->current + $this->pLimit;
//                    $bar_r = " <a href="{$_SERVER['PHP_SELF']}{$this->query_str}{$this->glue}{$this->url_page}={$i}{$loadtime}" title="後 {$this->pLimit} 頁">{$this->next_layer}</a> ";
        }

        $page_bar['center'] = $bar_center;
        $page_bar['left'] = $bar_first . $bar_l . $bar_left;
        $page_bar['right'] = $bar_right . $bar_r . $bar_last;
        $page_bar['current'] = $this->current;
        $page_bar['total'] = $this->pTotal;
        $page_bar['sql'] = $this->sqlQuery();
        return $page_bar;
    }

}

?>