/**
 * Simple way to encapsulate methods
 * Create a 'toolbox' of functions to use
 * You generally only create one module, as Service
 */

var Module = function() {
    var privateVar = 'I\'m private var';

    var method = function() {
        console.log(privateVar);
    }

    // revealing:
    return {
        method: method
    }
}

var module = new Module();
console.log(module.method());