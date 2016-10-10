var obj = {a: 100, b: 200},
    myVar = 10;

delete obj.a;
/**
 * js try to help:
 *   myVar and obj is not deleted in order to let code keep runing
 * conclusion:
 *   use strict
 */
delete myVar;
delete obj;
console.log(obj);
console.log(myVar);