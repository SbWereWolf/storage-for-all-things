/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 20.11.2021, 13:49
 */

/* poor */
with const as (select 5 as POOR, 40 as RICH)
select code
from const c,
     essence e
         join essence_attribute ea on e.id = ea.essence_id
group by code, c.POOR, c.RICH
having count(*) >= c.POOR
   and count(*) <= (c.POOR * 2 - 1)
;
/* average */
with const as (select 5 as POOR, 40 as RICH)
select code
from const c,
     essence e
         join essence_attribute ea on e.id = ea.essence_id
group by code, c.POOR, c.RICH
having count(*) >= c.POOR * 2
   and count(*) <= (c.RICH / 2)
;
/* rich */
with const as (select 5 as POOR, 40 as RICH)
select code
from const c,
     essence e
         join essence_attribute ea on e.id = ea.essence_id
group by code, c.POOR, c.RICH
having count(*) >= (c.RICH / 2)
   and count(*) <= c.RICH
;

/* few */
with const as (select 3 as FEW, 24 as MANY)
select code
from const c,
     essence e
         join essence_thing ea on e.id = ea.essence_id
group by code, c.FEW, c.MANY
having count(*) >= c.FEW
   and count(*) <= (c.FEW * 2 - 1)
;
/* average */
with const as (select 3 as FEW, 24 as MANY)
select code
from const c,
     essence e
         join essence_thing ea on e.id = ea.essence_id
group by code, c.FEW, c.MANY
having count(*) >= c.FEW * 2
   and count(*) <= (c.MANY / 2)
;
/* many */
with const as (select 3 as FEW, 24 as MANY)
select code
from const c,
     essence e
         join essence_thing ea on e.id = ea.essence_id
group by code, c.FEW, c.MANY
having count(*) >= (c.MANY / 2 + 1)
   and count(*) <= c.MANY
;

/* FEW + POOR */
with const as (select 3 as FEW, 24 as MANY, 5 as POOR, 40 as RICH),
     few as (select code, count(*) as items
             from const c,
                  essence e
                      join essence_thing ea on e.id = ea.essence_id
             group by code, c.FEW, c.MANY
             having count(*) <= (c.FEW * 2 - 1)),
     poor as (select code, count(*) as params
              from const c,
                   essence e
                       join essence_attribute ea on e.id = ea.essence_id
              group by code, c.POOR, c.RICH
              having count(*) <= (c.POOR * 2 - 1))
select t.code, t.items, p.params
from few as t
         join poor as p on t.code = p.code
order by t.items, p.params
;
/* shirt */

/* AVERAGE + AVERAGE*/
with const as (select 3 as FEW, 24 as MANY, 5 as POOR, 40 as RICH),
     items as (select code, count(*) as items
               from const c,
                    essence e
                        join essence_thing ea on e.id = ea.essence_id
               group by code, c.FEW, c.MANY
               having count(*) >= c.FEW * 2
                  and count(*) <= (c.MANY / 2)),
     params as (select code, count(*) as params
                from const c,
                     essence e
                         join essence_attribute ea on e.id = ea.essence_id
                group by code, c.POOR, c.RICH
                having count(*) >= c.POOR * 2
                   and count(*) <= (c.RICH / 2))
select t.code, t.items, p.params
from items as t
         join params as p on t.code = p.code
order by t.items, p.params
;
/* elephant */

/* MANY and RICH */
with const as (select 3 as FEW, 24 as MANY, 5 as POOR, 40 as RICH),
     many as (select code, count(*) as items
              from const c,
                   essence e
                       join essence_thing ea on e.id = ea.essence_id
              group by code, c.FEW, c.MANY
              having count(*) >= (c.MANY / 2 + 1)),
     rich as (select code, count(*) as params
              from const c,
                   essence e
                       join essence_attribute ea on e.id = ea.essence_id
              group by code, c.POOR, c.RICH
              having count(*) >= (c.RICH / 2 + 1))
select t.code, t.items, p.params
from many as t
         join rich as p on t.code = p.code
order by t.items, p.params
;
/* roof */