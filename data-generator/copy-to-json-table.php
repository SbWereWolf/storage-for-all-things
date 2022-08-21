<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-08-21
 */

declare(strict_types=1);

use Environment\Database\PdoConnection;

$pathParts = [__DIR__, '..', 'vendor', 'autoload.php',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
require_once($path);

$pathParts = [__DIR__, '..', 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$linkToData = (new PdoConnection($path))->get();

$essences = [
    'MANY' => 'rug',
    'AVERAGE' => 'cream',
    'FEW' => 'helicopter',
];

foreach ($essences as $type => $essence) {
    $destination = "json_t_$essence";
    $pickup = "auto_t_$essence";

    $linkToData->exec("DROP TABLE IF EXISTS $destination");
    $linkToData->exec(
        "
CREATE TABLE $destination
(
    id       bigserial constraint $destination" . "_pk primary key,
    thing_id bigint not null
        constraint $destination" . "_thing_id_fk
            references thing,
    data     jsonb  not null
);
create unique index $destination" . "_thing_id_uindex
    on $destination (thing_id);
"
    );

    $result = $linkToData->query(
        "
select to_json(array_agg(C)) as data
from (select * from $pickup ) C;
",
        PDO::FETCH_ASSOC
    );

    $content = $result->fetchColumn();
    $result->closeCursor();

    $collection = json_decode($content, true);
    foreach ($collection as $record) {
        $thing_id = $record['thing_id'];
        $data = json_encode($record);

        $linkToData->exec(
            "
INSERT INTO $destination(thing_id,data)
VALUES($thing_id,'$data')
"
        );
    }

    $linkToData->exec(
        "
CREATE INDEX $destination" . "_data_gx
ON $destination USING GIN (data)"
    );

    $columns = array_keys($record);
    foreach ($columns as $column) {
        $probe = $record[$column];
        $sql = '';
        $isInt = is_int($probe);
        if ($isInt) {
            $sql =
                "
CREATE INDEX $destination" . "_$column" . "_btree
ON $destination USING BTREE (((data->>'$column')::int8))
";
        }

        $isFloat = is_float($probe);
        if ($isFloat) {
            $sql =
                "
CREATE INDEX $destination" . "_$column" . "_btree
ON $destination USING BTREE (((data->>'$column')::float8))
";
        }

        if (!$isInt && !$isFloat) {
            $sql =
                "
CREATE INDEX $destination" . "_$column" . "_btree
ON $destination USING HASH ((data->'$column'))
";
        }

        $linkToData->exec($sql);
    }
}
