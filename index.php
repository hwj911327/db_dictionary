<?php
header('Content-Type:text/html;Charset=utf-8');
date_default_timezone_set('PRC');

$host = 'localhost';
$db_name = 'we7';
$db_user = 'root';
$db_pwd = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_pwd);
} catch (PDOException $e) {
    exit('错误信息：' . $e->getMessage());
}

//获取所有表名
$sql = "show tables";
try {
    $result = $pdo->query($sql);
    foreach ($result as $row) {
       $munu[]=$tables[]['TABLE_NAME'] = $row[0];
    }
} catch (PDOException $e) {
    exit($e->getMessage());
}

// 循环取得所有表的备注及表中列消息
foreach ($tables as $k => $v) {
    $sql = 'SELECT * FROM ';
    $sql .= 'information_schema.TABLES ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$db_name}'";

    $table_result = $pdo->query($sql);
    foreach ($table_result as $row) {
        $tables[$k]['TABLE_COMMENT'] = $row[20];
    }


    $sql = 'SELECT * FROM ';
    $sql .= 'information_schema.COLUMNS ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$db_name}'";
    $fields = array();

    $field_result = $pdo->query($sql);
    foreach ($field_result as $row) {
        $fields[] = $row;
    }
    $tables[$k]['COLUMN'] = $fields;


}


$munu_html = '';

$html = '';
// 循环所有表
//print_r($tables);
foreach ($tables as $k => $v) {

    $munu_html.='<li><a href="#munu_'.$v['TABLE_NAME'].'"><cite>'.$v['TABLE_NAME'].'</cite><p style="color: #d2d2d2">'.$v['TABLE_COMMENT'].'</p></a></li>';


    $html .= '<table class="layui-table">';
    $html .= '<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">';
    $html .= '<a name ="munu_'.$v['TABLE_NAME'] .'"></a>';
    $html .= '<legend>表名：' . $v['TABLE_NAME'] . ' ' . $v['TABLE_COMMENT'] . '</legend>';
    $html .='</fieldset>';
    $html .= '<thead><tr><th>字段名</th><th>数据类型</th><th>默认值</th><th>允许非空</th><th>自动递增</th><th>备注</th></tr></thead>';
    $html .= '<tbody>';
    foreach ($v['COLUMN'] AS $f) {
        $html .= '<tr>';
        $html .= '<td>' . $f['COLUMN_NAME'] . '</td>';
        $html .= '<td>' . $f['COLUMN_TYPE'] . '</td>';
        $html .= '<td>' . $f['COLUMN_DEFAULT'] . '</td>';
        $html .= '<td>' . $f['IS_NULLABLE'] . '</td>';
        $html .= '<td>' . ($f['EXTRA'] == 'auto_increment' ? '是' : ' ') . '</td>';
        $html .= '<td>' . $f['COLUMN_COMMENT'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '<tbody></table>';
}


include "main.html";
