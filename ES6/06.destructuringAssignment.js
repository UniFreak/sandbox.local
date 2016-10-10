// 1. Destructuring array
var someArray = ['one', 'two', 'three', ['fourOne', 'fourTwo']];
var [                   // you can use `var` `let` or `const` before `[]`
        ,               // skippable
        second,         // normal usage
        third,
        [               // nestable
            nestFirst,
            nestSecond
        ],
        outBound='true', // can supply a default value
        outBoundNoDefault// outbounds are `undefined`
    ] = someArray;
console.log(second, third, nestFirst, nestSecond, outBound, outBoundNoDefault);

function* fibs() {       // also works for any iterable
    var a = 0;
    var b = 1;
    while (true) {
        yield a;
        var [a, b] = [b, a + b];
    }
}
var [first, second, third, fourth, fifth, sixth] = fibs();
console.log(first, second, third, fourth, fifth, sixth);

// 2. Destructuring objects
var complicateObject = {
    arrayProperty: [
        'one',
        {second: 'two'}
    ],
}
var {                       // if you are not using `var` `let` or `const`,
                            // you must wrap it in parenthesis
                            // otherwise JS will parse them as block statement
                            // and generate syntax error
        notExists=true,     // unskippable, can supply a default value
        arrayProperty: [    // nestable
            first, {
                second:b,   // alias
                third:c='c' // alias and defaults
            }
        ],
        outBoundNoDefault   // outbounds are `undefined`
    } = complicateObject;
console.log(notExists, first, b, c, outBoundNoDefault);

// 3. can destructure other primitive types, but not `null`

// 4. real world use case
function removeBreakpoint({url, line, column}) {
    // this avoid repeating the single parameter object whenever
    // we want to reference one of its properties
}
function ajax(url, {
    aysnc = true,
    beforeSend = noop,
    cache = true,
    complete = noop,
    crossDomain = false,
    global = ture,
    // ... more config
}) {
    // by providing default values, this avoid repeating
    // `var foo = config.foo || theDefaultFoo` code
    // very useful passing a config object as parameter
}

var map = new Map();
map.set('window', 'the global');
map.set('document', 'the document');
for (var [key, value] of map) {
    // iterate through map item
    console.log(`${key} is ${value}`);
}
for (var [key] of map) {
    // iterate only key
    console.log(`key is ${key}`);
}
for (var [, value] of map) {
    // iterate only value
    console.log(`value is ${value}`);
}

function returnMultipleValues() {
    return [1, 2];
}
var [foo, bar] = returnMultipleValues();
console.log(foo, bar);
// better than
// ```
// var result = returnMultipleValues();
// var foo = result[0];
// var bar = result[1];
// ```