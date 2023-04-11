<?php

require "assignment1.php";
require "assignment2.php";

/* ---- Test assignment 1 ---- */

$input = [40, 20, 0, 10, 50];

$index = balanceIndex($input);

$left = array_sum(array_slice($input, 0, $index));

$right = array_sum(array_slice($input, $index));

echo $index . ": " . $left . " | " . $right;


/* ---- Test assignment 2 ---- */

// Generate and store many test entities
$entities = Entity::generateTestEntities(1e4);
Entity::storeMultiple($entities, 500);

// Use existing name to update entity
$entity = new Entity('N:6435d928a1bd9', 'T:6435d767bff37', '... ABC', 150);
Entity::update($entity);