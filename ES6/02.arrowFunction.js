// ES5
var multiply = function(x, y) {
    return x * y;
}
// ES6
var multiply = (x, y) => { return x * y };
// curly brackets are not required if only one expression is present
var multiply = (x, y) => x * y;
// parentheses are optional when only one parameter is present
var multiplyBy2 = x => 2 * x;
// parentheses are required when no parameters are present
var fixedResult = () => 2 * 4;
// parameter can be `rest parameters` or `defaults` or `destructuring`


// ES5
var setNameId  = function (id, name) {
    return {
        id: id,
        name: name
    };
};
// ES6
// if return an object literal, need to wrap in parentheses
// otherwise JS will parse it as block statement and trigger errors
var setNameId = (id, name) => ({ id: id, name: name });


// NOTE
// 1. arrow functions can't be used as constructors(use new ES6 classes instead)
// 2. `this` never change in a arrow function:
function Person() {
    // The Person() constructor defines `this` as an instance of itself.
    this.age = 0;

    setInterval(function growUp() {
        // In non-strict mode, the growUp() function defines `this`
        // as the global object, which is different from the `this`
        // defined by the Person() constructor.
        // In ECMAScript 3/5, this issue was fixed by assigning the value
        // in `this` to a variable that could be closed over, say `var self=this`;
        // then use self
        // But in ES6, `this` properly refers to the person object
        this.age++;
    }, 1000);
}
// 3. arrow functions, like built-in functions, don't have a prototype property or other internal methods
// 4. arrow functions cannot be used as generators
// 5. arrow functions do not have `arguments` object(use new ES6 `rest parameter` or `defaults`)
// 6. use arrow functions only when it's improving readability