<?php
$accountFees = json_decode('{"xinzhigou_2":1066200,"detail":{"xindktip_13":0,"xinyqweizhang_13":150000,"finance002yq_13":100000,"xinpublicyf_13":12000,"xinmortgageyf_13":60000,"xinnonlocalyg_13":0,"xinlistingyf_13":0,"xintheftinsys_13":83200,"xininsdeposit_13":0}}', true);

// 清分项目 => 清分账户名
$accounts = [
    'dktip' => ['xindktip_8'], // 带看费
    'gps' => ['finance002yq_4', 'finance002_4', 'xinzhizugps_4'], // gps
    'irregularity' => ['xinyqweizhang_7', 'xinweizhang_7', 'xinzzweizhang_7'], // 违章押金
    'assets' => ['xinpublicyf_10', 'xinpublicyg_10'], // 资产管理费
    'mortgage' => ['xinmortgageyf_12', 'xinmortgageyg_12'], // 抵押费
    'migration' => ['xinnonlocalyg_14'], // 外迁费
    'license' => ['xinlistingyf_3', 'xinlistingyg_3'], // 上牌费
    'safety' => ['xintheftinsys_15'], // 盗强险
];
$result = [];
// foreach ($accounts as $item => $names) {
//     $result[$item] = 0;
//     foreach ($names as $account) {
//         $result[$item] += array_get($accountFees, $account, 0) / 100;
//     }
// }

// 加入车商虚拟号
$last = array_pop($accountFees);
var_dump((int) $last);

return $result;