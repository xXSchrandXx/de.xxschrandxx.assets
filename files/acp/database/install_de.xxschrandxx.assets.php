<?php

use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\TextDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
    DatabaseTable::create('assets1_asset')
        ->columns([
            ObjectIdDatabaseTableColumn::create('assetID'),
            VarcharDatabaseTableColumn::create('legacyID')
                ->length(50),
            NotNullInt10DatabaseTableColumn::create('categoryID'),
            VarcharDatabaseTableColumn::create('title')
                ->length(20)
                ->notNull(),
            NotNullInt10DatabaseTableColumn::create('amount'),
            NotNullInt10DatabaseTableColumn::create('locationID'),
            TextDatabaseTableColumn::create('description')
                ->notNull(),
            DefaultFalseBooleanDatabaseTableColumn::create('isTrashed'),
            NotNullInt10DatabaseTableColumn::create('comments'),
            NotNullInt10DatabaseTableColumn::create('lastCommentTime'),
            NotNullInt10DatabaseTableColumn::create('lastTimeModified'),
            NotNullInt10DatabaseTableColumn::create('time')
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['assetID']),
            DatabaseTableIndex::create('legacyID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['legacyID'])
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['categoryID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['categoryID'])
                ->referencedTable('wcf1_category'),
            DatabaseTableForeignKey::create()
                ->columns(['locationID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['categoryID'])
                ->referencedTable('wcf1_category')
        ])
];
