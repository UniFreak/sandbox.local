<?php
function eVar($var) {
    echo $var . '<br />';
}
function dVar($var) {
    var_dump($var);
}


// ==================== Define and print ====================
$ary = ['apple' => ['iphone', 'ipad'], 'banana', 'orange'];
// if you use float as key, they will be round down, so 1.5 got overrided
// if you must use float, use it as string such as '1.5', '1.6'
$ary[1.5] = 'overrided';
$ary[1.6] = 'new';
$range = range(1, 40);
$rangeWithStep = range(1, 40, 2);
$rangeDown = range(40, 1);

echo '<pre>';
print_r($ary);
print_r($range);
print_r($rangeWithStep);
print_r($rangeDown);

/**
 * use {} to when use array in a string, to clarify you want outpu $ary[2]
 * instead of $ary, then `[2]`
 */
eVar("I love {$ary[0]}");

// pass `true` as second param to save output
$output = print_r($ary, true);
echo $output;
echo '</pre>';

var_dump($ary);

// var_export prints out variable info that can be used as PHP code directely
var_export($ary);

// *you can pass the second param as 1 to count() to let it count recursively
var_dump(count($ary, 1));
// ==================== Loop through ====================
foreach ($ary as $val) {
    dVar($val);
}
foreach ($ary as $key => $val) {
    eVar('val in ' . $key . ' is: ');
    dVar($val);
}
// this should be avoid, cuz array's keys can out of order or missed
for ($i = 0; $i < count($ary); $i++) {
    eVar('val in ' . $i . ' is:');
    dVar($ary[$i]);
}
// the difference between foreach and list/each is
// that foreach auto start at the front of the array
// whereas list/each does not(start from where the cursor leaved)
while (list($key, $val) = each($ary)) {
    eVar('val in ' . $key . ' is ' . $val);
}
/**
 * or by manipulating array cursor
 *
 * when use `for` or cursor to loop a array, if they run into a `hole`(empty value item)
 * they will stop the loop prematurely. to solve this, use list/while like this:
 */
eVar('==================== loop by using cursor backward ====================');
dVar(end($ary));
while($val = prev($ary)) {
    dVar($val);
}
eVar('==================== loop by using cursor forward ====================');
dVar(reset($ary));
while($val = next($ary)) {
    dVar($val);
}

// ==================== Manipulate ====================
$ary1 = array('pepperoni', 'cheese', 'anchovies', 'tomatos');
$ary2 = array('ham', 'cheese', 'peppers', 'and', 'another');
dVar(array_diff($ary1, $ary2)); // in ary1, but not 2
dVar(array_intersect($ary1, $ary2)); // both in ary1 and 2
dVar(array_merge($ary1, $ary2)); // either in ary1 or 2
// the difference between + and array_merge is:
// item in ary2 whose key clash with ary1 will be ignored
dVar($ary1 + $ary2);

dVar(array_unique(array('same', 'diff', 'same')));

function endWithY($val) {
    return (substr($val, -1) == 'y');
}
dVar(array_filter(array('tony', 'honey', 'tammy', 'jone', 'me'), 'endWithY'));

$capitals = array('China' => 'BeiJing', 'Japan' => 'Tokyo', 'England' => 'London');
// availabel second parameter is:
// EXTR_OVERWRITE: On collision, overwrite the existing variable
// EXTR_SKIP: On collision, do not overwrite the existing variable
// EXTR_PREFIX_SAME: On collision, prefix the variable name with the prefix specified by parameter three
// EXTR_PREFIX_ALL: Prefix all variables with the prefix specified by parameter three, whether or not there is a collision
// EXTR_PREFIX_INVALID: Only use the prefix specified by parameter three when variables names would otherwise be illegal (e.g. "$9")
// EXTR_IF_EXISTS: Only set variables if they already exist
// EXTR_PREFIX_IF_EXISTS: Only create prefixed variables if non-prefixed version already exists
// EXTR_REFS: Extract variables as references
extract($capitals, EXTR_PREFIX_ALL, 'capitals');
dVar($capitals_China);
dVar($capitals_Japan);
dVar($capitals_England);

dVar(in_array('Tokyo', $capitals));

dVar(array_flip($capitals));

dVar(array_keys($capitals, 'BeiJing', 1));
dVar(array_values($capitals));


// they all affect the origin array
// you can pass them second param: SORT_STRING or SORT_NUMERIC to specify how sort behave
asort($capitals); // sort by val
dVar($capitals);
arsort($capitals);// sort by val reversely
dVar($capitals);
ksort($capitals); // sort by key
dVar($capitals);
krsort($capitals);// sort by key reversely
dVar($capitals);
sort($capitals);  // sort by val, drop association with key
dVar($capitals);

// deque(double-ended queue, pronounced 'deck')
$ary = array('origin');
dVar(array_unshift($ary, 'unshifted'));
dVar($ary);
dVar(array_push($ary, 'pushed'));
dVar($ary);
dVar(array_shift($ary));
dVar($ary);
dVar(array_pop($ary));
dVar($ary);

$ary = array('one' => 'one', 'two' => 'two', 'three' => 'three');
// if you only pass param 1, it return a value
// if you pass param two and make it > 1, it return a array
// if param two is larger than array length, error will occur
dVar(array_rand($ary, 2)); // array_rand leave original ary untouched
shuffle($ary);  // shuffle will mess up your key!
dVar($ary);