/**
 * ==: if variables are two different types, it will convert them to the same type
 * ===: no type conversion, this should be your default choice
 * if you wann check if a variable `x` exist, use `typeof x !== 'undefined'`
 */
var x = 1;
console.log(x == '1');
console.log(x === '1');

if (typeof y !== 'undefined') {
    console.log('y exists');
} else {
    console.log('y doesn\'t exists');
}
