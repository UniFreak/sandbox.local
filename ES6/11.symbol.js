// `symbol` is the seventh type of JS
// symbol value is not equal to any other value
//   programs can create and use symbol as property keys without risking name collisions
// symbols can't be automatically converted to string
//   but you can use `String(symbol)` or `symbol.toString()` to convert it
// like array elements, symbol-keyed properties can't be accessed using dot syntax
// JS's most common object-inspection feature simply ignore symbol keys
//   like `for-in` loop, `Object.keys()` and `Object.getOwnPropertyName()`
//   but you can use `Object.getOwnPropertySymbols()` or `Reflect.ownKeys()` to inspect symbol keys

var obj = {};
// three ways to obtain a symbol
// 1. create new unique symbol, you can pass a description to help debugging
var noCollide = Symbol('description');
// 2. obtain an user defined existing one
var same = Symbol.for('description');
// 3. or use language defined one like `Symbol.iterator`. see forOfLoopAndIterator.js

// `symbol-keyed property` is guaranteed not to collide
obj[noCollide] = "ok!";
console.log(noCollide == same, obj[noCollide], noCollide in obj);
delete obj[noCollide];