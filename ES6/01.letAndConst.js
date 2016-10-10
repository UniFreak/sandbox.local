/**
 * `var`'s problem
 * #1. Blocks are not scopes
 * #2. Variable oversharing in loops
 *
 * `let` is the new `var`
 * You should just stop using var and use let everywhere instead
 * - `let` variables are block-scoped
 *   (fixing #1)
 * - Loops of the form for (let x...) create a fresh binding for x in each iteration
 *   (fixing #2)
 * - Global `let` variables are not properties on the global object
 * - It’s an error to try to use a let variable before its declaration is reached
 * - Redeclaring a variable with `let` is a `SyntaxError`
 * - Apart from those differences, `let` and `var` are pretty much the same
 *
 * `const`
 * - Variables declared with `const` are just like `let` except that you can’t assign to them,
 *   except at the point where they’re declared
 * - You can’t declare a `const` without giving it a value
 */



// We're declaring `PI` to be a constant variable.
const PI = 3.141592653589793;

// Any attempt to assign a new value to `PI`
// fails because `PI` is a constant variable.
// PI = 0;
// PI++;

// All of the variable declarations below fail
// because we can't declare a new variable with the
// same identifier as an existing constant variable.
// var PI = 0;
// let PI = 0;
// const PI = 0;

// We're declaring a constant variable
// to hold a settings object.
const settings = {
    baseUrl: "https://example.com"
};

// Since `settings` is a constant variable,
// an attempt to assign a new value will fail.
// settings = {};

// However, the object is **not** immutable.
// This means we can change its properties!
settings.baseUrl = "https://evil.example.com";
console.log(settings);