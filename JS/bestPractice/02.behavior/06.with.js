var obj = {
    a: {
        b: {
            c: 'hello'
        }
    }
}

var c = 'this is important';

/**
 * Don't use with statement
 * use strict will ensure that
 */
with(obj.a.b) {
    console.log(c);
}

/**
 * instead of using `with`, you can use IIFE
 */
(function(newVar) {
    console.log(newVar)
}(obj.a.b.c))