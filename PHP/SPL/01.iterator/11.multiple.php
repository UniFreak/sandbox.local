<?php
/**
 * multipleIterator combine several array parellelly
 */
include '../../utils.php';

$boys = new ArrayIterator(array('Ian', 'Mark', 'Davlid'));
$girls = new ArrayIterator(array('Jennifer', 'Alice', 'Susan'));
$unisex = new ArrayIterator(array('Jody', 'Alex'));

$multiple = new MultipleIterator(MultipleIterator::MIT_KEYS_ASSOC);
$multiple->attachIterator($boys, 'boys');
$multiple->attachIterator($girls, 'girls');
$multiple->attachIterator($unisex, 'unisex');
foreach ($multiple as $names) {
    writeln($names);
}