/**
 * A promise is an object that is waiting for an asynchronous operation to complete,
 * and when that operation completes, the promise is either fulfilled(resolved)
 * or rejected
 */

// The standard way to create a Promise is by using the new Promise() constructor
// which accepts a handler that is given two functions as parameters. The first
// handler (typically named resolve) is a function to call with the future value
// when it's ready; and the second handler (typically named reject) is a function
// to call to reject the Promise if it can't resolve the future value.
var p = new Promise(function(resolve, reject) {
    if ( /* condition */ ) {
        resolve( /* value */ ); // fulfilled successfully
    } else {
        reject( /* reason */ ); // error, rejected
    }
});

// Every Promise has a method named then which takes a pair of callbacks.
// The first callback is called if the promise is resolved, while the second
// is called if the promise is rejected.
p.then((val) => console.log("Promise Resolved", val),
    (err) => console.log("Promise Rejected", err));

// Returning a value from then callbacks will pass the value to the next then callback.
var hello = new Promise(function(resolve, reject) {
    resolve("Hello");
});

hello.then((str) => `${str} World`)
    .then((str) => `${str}!`)
    .then((str) => console.log(str)) // Hello World!

// When returning a promise, the resolved value of the promise will get passed
// to the next callback to effectively chain them together. This is a simple
// technique to avoid "callback hell".
var p = new Promise(function(resolve, reject) {
    resolve(1);
});

var eventuallyAdd1 = (val) => {
    return new Promise(function(resolve, reject) {
        resolve(val + 1);
    });
}

p.then(eventuallyAdd1)
    .then(eventuallyAdd1)
    .then((val) => console.log(val)) // 3