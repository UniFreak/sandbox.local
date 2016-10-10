// ==================== Old way ====================
function Circle(radius) { // constructor
    this.radius = radius; // public var
    Circle.circlesMade++; // static var
}

Circle.draw = function draw(circle, canvas) { }    // static method

Object.defineProperty(Circle, "circlesMade", {
    get: function() {       // static var getter
        console.log('getting circlesMade');
        return !this._count ? 0 : this._count;
    },

    set: function(val) {    // static var setter
        this._count = val;
    }
});

Circle.prototype = {                                // instance method
    area: function area() {
        return Math.pow(this.radius, 2) * Math.PI;
    }
};

Object.defineProperty(Circle.prototype, "radius", {
    get: function() {       // instance var getter
        return this._radius;
    },

    set: function(radius) { // instance var setter
        if (!Number.isInteger(radius))
            throw new Error("Circle radius must be an integer.");
        this._radius = radius;
    }
});

// ==================== ES6 ====================
// ES6 class is not a new object-oriented inheritance model. They just serve as
// a syntactical sugar over JavaScript's existing prototype-based inheritance
//
// Class declarations are not hoisted
class Circle {
    // constructor is opotional
    // constructor can not be a generator
    constructor(radius) {
        this.radius = radius;
        Circle.circlesMade++;
    }; // semicolon is optional

    static draw(circle, canvas) {
    };

    static get circlesMade() {
        return !this._count ? 0 : this._count;
    };
    static set circlesMade(val) {
        this._count = val;
    };

    // no `function` keyword required when defining functions inside a class definition.
    area() {
        return Math.pow(this.radius, 2) * Math.PI;
    };

    get radius() {
        return this._radius;
    };
    set radius(radius) {
        if (!Number.isInteger(radius))
            throw new Error("Circle radius must be an integer.");
        this._radius = radius;
    };
}

// ==================== Subclassing ====================
/**
 * You can put any expression you want after extends,
 * as long as it’s a valid constructor with a prototype property, for example:
 * - Another class
 * - Class-like functions from existing inheritance frameworks
 * - A normal function
 * - A variable that contains a function or class
 * - A property access on an object
 * - A function call
 * - even `null`, if you don't want instances to inherit from `Object.prototype`
 */
class ScalableCircle extends Circle {
    /**
     * you can also use `super()`
     * only valid inside constructor methods of classes that use extends
     * (like php's parent::__construct())
     *
     * It is important to note that the derived constructor must call super()
     */

    get radius() {
        /**
         * This new super keyword allows us to bypass our own properties,
         * and look for the property starting with our prototype
         * Can be used in any function defined with method definition syntax
         */
        return this.scalingFactor * super.radius;
    }
    set radius() {
        throw new Error("ScalableCircle radius is constant." +
                        "Set scaling factor instead.");
    }
}