<?php

use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\option\Option;
use wcf\data\option\OptionEditor;
use wcf\system\WCF;

$sql = "SELECT  objectTypeID
        FROM    wcf" . WCF_N . "_object_type
        WHERE   definitionID = ?
            AND objectType = ?";
// add default category
$statementCategory = WCF::getDB()->prepareStatement($sql, 1);
$statementCategory->execute([
    ObjectTypeCache::getInstance()->getDefinitionByName('com.woltlab.wcf.category')->definitionID,
    'de.xxschrandxx.assets.category'
]);
CategoryEditor::create([
    'objectTypeID' => $statementCategory->fetchColumn(),
    'title' => 'Default Category',
    'time' => TIME_NOW,
]);

// add default category
$statementLocation = WCF::getDB()->prepareStatement($sql, 1);
$statementLocation->execute([
    ObjectTypeCache::getInstance()->getDefinitionByName('com.woltlab.wcf.category')->definitionID,
    'de.xxschrandxx.assets.location'
]);
CategoryEditor::create([
    'objectTypeID' => $statementLocation->fetchColumn(),
    'title' => 'Default Location',
    'time' => TIME_NOW,
]);

// set install time
$option = Option::getOptionByName('assets_install_time');
$editor = new OptionEditor($option);
$editor->update([
    'optionValue' => TIME_NOW
]);
