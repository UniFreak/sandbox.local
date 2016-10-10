var obj = {
    val: 'hi there',
    printVal: function() {
        console.log(this.val);
    }
};

var obj2 = {
    val: 'whats up'
};

obj2.printVal = obj.printVal;
/**
 * 1. this is the object that the function runs in
 */
console.log(obj.printVal());
console.log(obj2.printVal());

/**
 * 2. if there is no object context:
 *    - in non strict mode, this will be the global
 *    - in strict mode, this will be undefined
 */
var print = obj.printVal;
// print();

/**
 * 3. you can also bind `this` to an specific object
 */
var bindPrint = obj.printVal.bind(obj2);
bindPrint();


var objNew = function() {
    this.hello = 'hello';

    this.greet = function() {
        console.log(this.hello);
    }

    this.delayGreetUnexpected = function() {
        /**
         * @issue1: won't do what you expected. becuase `this` is this is global
         */
        setTimeout(this.greet, 1000);
    }

    this.delayGreetCorrect = function() {
        /**
         * @issue2: as expected, but painful: any time you want to pass a
         * callback, you need manually bind `this`
         * see best practice
         */
        setTimeout(this.greet.bind(this), 1000);
    }
}

/**
 * 4. when using new, js create a new this scope onto the function (here is obj())
 *    then it also implicitly does a `return this;`
 */
var greeter = new objNew();
console.log(greeter.greet());
console.log(greeter.delayGreetUnexpected());

/**
 * best practice:
 *   copy this to _this, then use _this all the place so you don't lose this scope
 *   and worry about what `this` is referencing all the time
 */
var objBest = function() {
    var _this = this;

    _this.hello = 'helloBest';

    _this.greet = function() {
        console.log(_this.hello);
    }

    _this.delayGreet = function() {
        setTimeout(_this.greet, 1000);
    }
}
var greeterBest = new objBest();
greeterBest.delayGreet();