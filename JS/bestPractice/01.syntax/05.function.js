dec(); // this is fine because of function hoisting
expr();// this is not, becuase of expr is hoisted as variabe, and initialized as
       // undefined. see `variable.js`

function dec() {
    console.log('hi from declaration');
}
var expr = function() {
    console.log('hi from expression');
}

/**
 * conclusion:
 *     put all variable first, then function, then run code in each scope(gloabl
 *     or function)
 */