/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-08-21
 */

/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 11.01.2022, 6:34
 */

/* purge db after benchmarking */
DELETE
FROM essence_thing
where id in (
    select et.id
    from essence_thing et
             join thing t on t.id = et.thing_id
    where code like 'new-thing-%'
);
DELETE
FROM number
where thing_id in (
    select t.id
    from thing t
             left join essence_thing e on e.thing_id = t.id
    where e.id is null
);
DELETE
FROM time_interval
where thing_id in (
    select t.id
    from thing t
             left join essence_thing e on e.thing_id = t.id
    where e.id is null
);
DELETE
FROM time_moment
where thing_id in (
    select t.id
    from thing t
             left join essence_thing e on e.thing_id = t.id
    where e.id is null
);
DELETE
FROM word
where thing_id in (
    select t.id
    from thing t
             left join essence_thing e on e.thing_id = t.id
    where e.id is null
);

DELETE
FROM thing
where id in (
    select t.id
    from thing t
             left join essence_thing e on e.thing_id = t.id
    where e.id is null
);

DELETE
FROM essence_attribute
where attribute_id in (select ea.attribute_id
                       from essence_attribute ea
                                join attribute a on a.id = ea.attribute_id
                       where code like 'test-%');

/* STOP */
/* manual operations */

SELECT id
FROM attribute
where id in (select a.id
             from attribute a
                      left join essence_attribute ea on ea.attribute_id = a.id
             where ea.id is null);

DELETE
from word
where attribute_id in (SELECT id
                       FROM attribute
                       where id in (select a.id
                                    from attribute a
                                             left join essence_attribute ea on ea.attribute_id = a.id
                                    where ea.id is null));

DELETE
FROM attribute
where id in (select a.id
             from attribute a
                      left join essence_attribute ea on ea.attribute_id = a.id
             where ea.id is null);

DELETE
FROM auto_t_cream
where thing_id in (select et.thing_id
                   from auto_t_cream et
                            left join thing t on t.id = et.thing_id
                   where et.code like 'new-thing-%');
DELETE
FROM auto_t_helicopter
where thing_id in (select et.thing_id
                   from auto_t_helicopter et
                            left join thing t on t.id = et.thing_id
                   where et.code like 'new-thing-%');
DELETE
FROM auto_t_rug
where thing_id in (select et.thing_id
                   from auto_t_rug et
                            left join thing t on t.id = et.thing_id
                   where et.code like 'new-thing-%');
