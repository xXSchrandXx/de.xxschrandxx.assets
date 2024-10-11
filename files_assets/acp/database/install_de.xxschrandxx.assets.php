<?php

use wcf\system\database\table\column\DateDatabaseTableColumn;
use wcf\system\database\table\column\DatetimeDatabaseTableColumn;
use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar191DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
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
            NotNullVarchar191DatabaseTableColumn::create('title'),
            NotNullInt10DatabaseTableColumn::create('amount'),
            NotNullInt10DatabaseTableColumn::create('locationID'),
            TextDatabaseTableColumn::create('description')
                ->notNull(),
            DefaultFalseBooleanDatabaseTableColumn::create('isTrashed'),
            NotNullInt10DatabaseTableColumn::create('comments')
                ->defaultValue(0),
            DatetimeDatabaseTableColumn::create('lastComment'),
            DatetimeDatabaseTableColumn::create('lastModification')
                ->notNull(),
            DatetimeDatabaseTableColumn::create('lastAudit')
                ->notNull(),
            DateDatabaseTableColumn::create('nextAudit')
                ->notNull(),
            DatetimeDatabaseTableColumn::create('time')
                ->notNull(),
            NotNullInt10DatabaseTableColumn::create('attachments')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('hasEmbeddedObjects')
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
        ]),
    DatabaseTable::create('assets1_option')
        ->columns([
            ObjectIdDatabaseTableColumn::create('optionID'),
            NotNullVarchar255DatabaseTableColumn::create('optionTitle')
                ->defaultValue(''),
            TextDatabaseTableColumn::create('optionDescription'),
            NotNullVarchar255DatabaseTableColumn::create('optionType')
                ->defaultValue(''),
            MediumtextDatabaseTableColumn::create('defaultValue'),
            TextDatabaseTableColumn::create('validationPattern'),
            MediumtextDatabaseTableColumn::create('selectOptions'),
            DefaultFalseBooleanDatabaseTableColumn::create('required'),
            NotNullInt10DatabaseTableColumn::create('showOrder')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['optionID']),
        ]),
    DatabaseTable::create('assets1_option_value')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('assetID'),
            NotNullInt10DatabaseTableColumn::create('optionID'),
            MediumtextDatabaseTableColumn::create('optionValue')
                ->notNull(),
        ])
        ->indices([
            DatabaseTableIndex::create('assetID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['assetID', 'optionID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['optionID'])
                ->referencedTable('assets1_option')
                ->referencedColumns(['optionID'])
                ->onDelete('CASCADE')
                ->onUpdate('NO ACTION'),
            DatabaseTableForeignKey::create()
                ->columns(['assetID'])
                ->referencedTable('assets1_asset')
                ->referencedColumns(['assetID'])
                ->onDelete('CASCADE')
                ->onUpdate('NO ACTION')
        ])
];
