/**
 * Generator allow a function to generate many values over time by returning an
 * object which can be iterated over to pull values from the function one value
 * at a time
 *
 * generator are not threads
 */

// generator functions start with `function*`
function* range(start, stop) {
    try {
        for (var i = start; i < stop; i++) {
            yield i;
        }
    } finally {
        console.log('cleaning up...');
    }
    // reaching the end of generator is just like returning `undefined`
}

// generator are iterators.
// all generators have a built-in implementation of .next() and [Symbol.iterator]()
for (var value of range(1, 5)) {
    console.log(value);
    if (value == 2) {
        break;
    }
}
// you can pass `.throw()` or `.return()` to `.next()`
// there is also `yield*` syntax