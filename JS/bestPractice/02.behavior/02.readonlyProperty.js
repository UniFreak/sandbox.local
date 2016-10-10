var obj = {};

Object.defineProperty(obj, 'readOnly', {
    enumerable: false,
    configurable: false,
    writable: false,
    value: 'this var is readonly'
});

/**
 * js try to help:
 *   this won't blow up, the assignment simply ignored
 *   you should use strict if you want to know exactly
 * conclusion:
 *   use strict
 */
obj.readOnly = 'overriden';
console.log(obj.readOnly);