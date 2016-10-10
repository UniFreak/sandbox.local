// ==================== Spread/Rest operator ====================
// `...` operator is referred to as spread or rest operator,
// depending on how and where it is used.

// SPREAD: it can be used on anything that’s an iterable,
// to "spread" it into individual elements
console.log(1, ...[2, 3, 4], 5);

// REST: gathering a set of values together into an array
//
// the `...` before `needles` indicates it's a `rest parameter`
// all other passed parameters are put into an array and assigned to the variable `needles`
//
// only the last parameter of a function may be marked as a rest parameter
// if there are not extra arguments, the rest parameter will simply be an empty array
function containsAll(haystack, ...needles) {
    console.log(needles);
    for (var needle of needles) {
        if (haystack.indexOf(needle) === -1) {
            return false;
        }
    }
    return true;
}
containsAll('banana', 'nan', 'a');

// ==================== Defauts ====================
// use `=` to assign a default parameter value
//
// default value expressions are evaluated at function call time
// this means that default expressions can use the values of previously-filled parameters
// it can even be a function call or inline functions(but that's ugly)
//
// passing `undefined` is considered to be equivalent to not passing anything at all
// a parameter without a default implicitly defaults to undefined
function animalSentence(
    animals2=(animals3 == 'tigers') ? 'bears' : 'tigers',
    animals3='tigers'
) {
    console.log(`Lions and ${animals2} and ${animals3}! Oh my!`);
}
animalSentence(undefined, 'tigers');

// we can't use rest parameter and defaults at the same time. this will cause error
// function fun(...arr=[1, 2]) {}


// `rest parameter` and `default` is preferred to using `arguments` object
// `arguments` object notoriously causes headaches for optimizing js VMs