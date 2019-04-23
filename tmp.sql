SELECT a.userid,a.carid,a.loan_term,a.applyid,a.product_stcode,a.fund_channel,a.purchase_type
FROM car_loan_order a
left join car_half_service c on c.carid=a.carid
    where a.status>=4 and a.finance_status >=200 and a.channel_type=1
    and a.product_stcode in ('PS54','PS52','PS56','PS46')
    and a.fund_channel in(1,10)
    and from_unixtime(a.interest_start) between date_sub(now(), interval 5 MINUTE) and now()
    and c.sign=1 and a.userid=c.userid and c.purchase_source!=100

-- 先查 clo 最近五分钟单子的 uid, 取得 <applyids>, <uids>
select applyid, userid
from car_loan_order
where status>=4 and finance_status >=200 and channel_type=1
and product_stcode in ('PS54','PS52','PS56','PS46')
and fund_channel in(1,10)
and from_unixtime(a.interest_start) between date_sub(now(), interval 5 MINUTE) and now();

-- 再查 service 淘宝单的 uid <tbuids>
select userid
from car_half_service
where sign=1 and purchase_type = 100
and userid in <uids>

-- 再回去筛选掉淘宝单
select *
from car_loan_order
where applyid in <applyids>
and userid not in <tbuids>