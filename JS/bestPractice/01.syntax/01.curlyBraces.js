/**
 * you already know that
 * ```
 * function ()
 * {
 *     return
 *     {
 *         hi: "hello"
 *     }
 * }
 * ```
 * won't work(see semicolon.js)
 *
 * so for consistency, all opening curly braces goes to the same line
 */
function doSomething() {
    return {
        hi: "hello"
    }
}