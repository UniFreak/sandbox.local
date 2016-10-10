<?php
class UnorderedListBuilder extends RecursiveIteratorIterator
{
    public function beginIteration()
    {
        echo "<ul>\n";
    }

    public function endIteration()
    {
        echo "<ul>\n";
    }

    public function beginChildren()
    {
        echo "<ul>\n";
    }

    public function endChildren()
    {
        echo "</ul></li>\n";
    }

    // another method you can override is
    // public function nextElement()
    // {
    // }
}


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
$products = new UnorderedListBuilder(
    $products,
    RecursiveIteratorIterator::SELF_FIRST
    );
foreach ($products as $category => $item) {
    if ($products->hasChildren()) {
        echo "<li>$category";
    } else {
        echo "<li>$item</li>\n";
    }
}