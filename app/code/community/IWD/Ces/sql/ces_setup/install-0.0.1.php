<?php

$installer = $this;
$table = $installer->getTable('iwd_ces');

$installer->startSetup();

$installer->getConnection()->dropTable($table);
$table = $installer->getConnection()
    ->newTable($table)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ])
    ->addColumn('imported', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
    ]);
$installer->getConnection()->createTable($table);

$installer->endSetup();