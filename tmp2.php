<?php
$persons = array(
    array(
        'id' => 1,
        'name' => 'ni',
        'age' => '27',
    ),
    array(
        'id' => 2,
        'name' => 'wo',
        'age' => '26',
    )
);

foreach ($persons as $person) {
    $newPersons[$person['id']] = $person;
}

$genders = array(
    array(
        'id' => 1,
        'gender' => 'male',
    ),
    array(
        'id' => 2,
        'gender' => 'unknown',
    )
);
foreach ($genders as $gender) {
    $newPersons[$gender['id']]['gender'] = $gender['gender'];
}
var_dump($newPersons);