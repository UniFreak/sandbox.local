// ==================== for of ====================
var array = ['apple', 'banana', 'pear', '---'];
array.name = ['one', 'two', 'three'];

// old way
for (var index = 0; index < array.length; index++) {
    console.log(array[index]);
}

// ES5
// cons:
//  - can't break.
//  - can't return from the enclosing function
array.forEach(function(value) {
    console.log(value);
});

// DON'T DO THIS!
// badness:
//  - `index` will be string, not actual numbers
//  - will loop through expando properties even properties on the array's prototype chain
//  - loop by arbitrary order can happen
//  - for-in was designed to work on plain old `Objects` with string keys. not for array
for (var index in array) {
    console.log(array[index]);
}

// ES6
// `for-of` is for looping over data
//      like the values in an array or array-like object
//      like DOM `Nodelists`, Map and Set, or even string
for (var value of array) {
    console.log(value);
}
// ==================== iterator ====================
// An iterator accesses the items from a collection one at a time, while keeping
// track of its current position within that sequence. It provides a next() method
// which returns the next item in the sequence. This method returns an object
// with two properties: done and value.
//
// ES6 has Symbol.iterator which specifies the default iterator for an object.
// Whenever an object needs to be iterated (such as at the beginning of a for..of loop),
// its @@iterator method is called with no arguments, and the returned iterator
// is used to obtain the values to be iterated.
var arr = [11,12,13];
var itr = arr[Symbol.iterator]();

console.log(itr.next()); // { value: 11, done: false }
console.log(itr.next()); // { value: 12, done: false }
console.log(itr.next()); // { value: 13, done: false }

console.log(itr.next()); // { value: undefined, done: true }

// you can also make **any** object `iterable` by define a `[Symbol.iterator]`, like this:
var zeroesForeverIterator = {
    [Symbol.iterator]: function() {
        return this;
    },
    next: function() {
        return {
            done: false,
            value: 0
        };
    }
    // other supported methods are `return()` and `throw()`
    //   The for–of loop calls .return() if the loop exits prematurely,
    //   due to an exception or a break or return statement.
    //   The iterator can implement .return() if it needs to do some cleanup
    //   or free up resources it was using.
    //   Most iterator objects won’t need to implement it
};