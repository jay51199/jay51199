﻿<?php
//匹配手机号的正则表达式 #^(13[0-9]|14[47]|15[0-35-9]|17[6-8]|18[0-9])([0-9]{8})$#
$arr = array(8010,8011,8012,8013,8014,8015,8016,8017,8018,8019,8020,8021,8022,8023,8024,8025,8026,8027,8028,8029,8077,8078,8079,8080,8081,8082,8083,8084,8085,8086,8087,8088,8089,8095,8099,9010,9014,9015,9016,9021,9022,9023,9024,9025,9026,9027,9030,9031,9032,9033,9040,9043,9045,9046,9047,9048,9049,9050,9051,9052,9053,9054,9055,9056,9057,9058,9059,9069,9070,9071,9072,9073,9074,9076,9077,9078,9079,9087,9088,9089,9090
);
//循环拼接
for($i = 0; $i < 100000; $i++) {
    $tmp[] = $arr[array_rand($arr)].''.mt_rand(100,999).''.mt_rand(100,999).'';
}
//去掉重复
$phone = array_unique($tmp);
$name = count($phone);
$str = implode("\r\n",$phone);
//var_export(array_unique($phone));
//写入文档
file_put_contents("jp.txt",$str);
//输出
?>