// ==================== Read/Write ====================
var obj = {};
// using dot notation:
obj.attrOne = 'attr by dot';
console.log(obj.attrOne);

// using bracket notation:
obj['attrTwo'] = 'attr by bracket';
console.log(obj['attrTwo']);
// this way you can manipulate attributes like this:
var attrName = 'attrThree';
obj[attrName] = 'attr by bracket, too';
console.log(obj[attrName]);

Object.defineProperty(obj, 'attrFour', {
    value: 'attr by defineProperty',
    writable: false,
    enumerable: true,
    configurable: true
});
console.log(obj.attrFour);