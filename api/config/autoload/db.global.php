<?php
$provider = new Zend\Db\ConfigProvider();

return array_merge($provider(), [
    'db' => [
        'driver' => 'pdo_sqlite',
        'database' => 'data/grouphealth.sqlite3'
    ],
]);
