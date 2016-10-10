<?php
include '../../utils.php';

$products = array(
    'Cameras' => array(
        'DSLR', 'smartPhone', 'compact'
    ),
    'Lenses' => array(
        'telescope', 'wideAngle', 'fishEye'
    ),
    'Accessories' => array(
        'tripod',
        'cameraBag',
        'Filters' => array(
            'polarizing', 'UV', 'neutral density'
        )
    )
);

$products = new RecursiveArrayIterator($products);
$products = new RecursiveIteratorIterator(
    $products,
    RecursiveIteratorIterator::SELF_FIRST
    );
foreach ($products as $category => $item) {
    $level = $products->getDepth();
    echo str_repeat(' ', $level*2);
    if ($products->hasChildren()) {
        stringln($category);
    } else {
        stringln($item);
    }
}