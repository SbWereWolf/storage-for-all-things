/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 11.01.2022, 6:34
 */

/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 07.01.2022, 3:56
 */

/* purge db after auto testing */
DELETE
FROM essence_thing
where essence_id in (
    select essence_id
    from essence_thing et
             join essence e on e.id = et.essence_id
    where code = 'cake'
);
DELETE
FROM essence_attribute
where essence_id in (
    select ea.essence_id
    from essence_attribute ea
             join essence e on e.id = ea.essence_id
    where code = 'cake'
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
FROM essence
where code = 'cake';

DROP VIEW IF EXISTS auto_v_cake;
DROP MATERIALIZED VIEW IF EXISTS auto_mv_cake;
DROP TABLE IF EXISTS auto_t_cake;
