/**
 * Property Value Shorthands:
 * You can omit the property value if it matches the property name
 */
var foo = 'bar'
var baz = { foo }
console.log(baz.foo)


/**
 * Computed property names:
 * allow you to write an expression wrapped in square brackets instead of the
 * regular property name. Whatever the expression evaluates to will become
 * the property name
 */
 var foo = 'bar'
 var baz = { [foo]: 'ponyfoo' }
 console.log(baz)
/**
 * You can not use property value shorthand expression with it
 */
// var foo = 'bar'
// var bar = 'ponyfoo'
// var baz = { [foo] }
// console.log(baz)
// <- SyntaxError

/**
 *
 */