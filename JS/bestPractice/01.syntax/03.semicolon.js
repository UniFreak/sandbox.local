/**
 * automate semicolon insertation(ASI) happen when:
 * - 1. as a Script or Module is parsed from left to right, a token (called the
 *   offending token) is encountered that is __not allowed__ by any production of
 *   the grammar, and
 *      - a. the offending token is separated from the previous token by at
 *           least one LineTerminator
 *      - b. the offending token is }
 * - 2. as the Script or Module is parsed from left to right, the end of the
 *      input stream of tokens is encountered
 * - 3. a token is encountered that is allowed by some production of the grammar,
 *   but the production is a restricted production(means continue, break, return,
 *   or throw) and the token would be the first token of a restricted production,
 *   and the restricted token is seperated from the previous token by at least
 *   one LineTerminator
 */
var a = 12      // ; by 1a
var b = 13      // ; by 1a
var c = b + a   // @issue: ; is not inserted, because `a[` is a valid expression
                // in this context. so this will generate error

['menu', 'items', 'listed']
    .forEach(function(element) {
        console.log(element)
    })



var d = c       // @issue: same as above

(function() {
    console.log(d);
}())



function()
{
    return // ; by 3 --> @issue: returned `undefined`, instead of what you expected
    {
        hi: 'hello'
    }
}


if (a) {console.log(a)/* ; by 1b */}

console.log(a+b) // ; by 2



/**
 * Conclusion:
 *   use semicolon in conjunction with JSHint(or ESLint) to prevent potential
 *   issues
 */