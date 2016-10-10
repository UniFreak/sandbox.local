/**
 * Simple way to encapsulate methods
 * Create a 'toolbox' of functions to use
 * You generally only create one module, as Service
 */

var Module = function() {
    var privateVar = 'I\'m private var';

    return {
        method: function() {
            console.log(privateVar);
        },
    }
}

var module = new Module();
console.log(module.method());