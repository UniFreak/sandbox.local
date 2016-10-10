var user = {name: 'fanghao&me', age: '26'};
var getName = function(data) {
    return data.name;
}

// 1. template literal
//   `${user.name}` and `${user.age}` are `template substitution`
//   template substitution can be *any* javascript expression
//   use \ to escape if you want a real ` or $ or {
//   any whitespace is included verbatim in the output
//   multiple line, finally
console.log(`User
    ${getName(user)}
    is ${user.age} old years now`);

// 2. tagged template
console.log(
    // `SaferHTML` is the tag
    // any ES6 memberExpression or callExpression can serve as a tag
    // the code below is equivalent to `Safter(templateData, user.name. user.age)`
    SaferHTML`<p>User ${user.name} is ${user.age} old years now`
    )
function SaferHTML(templateData) {
    console.log(templateData);

    var s = templateData[0];
    for (var i = 1; i < arguments.length; i++) {
        var arg = String(arguments[i]);

        // Escape special characters in the substitution.
        s += arg.replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Don't escape special characters in the template.
        s += templateData[i];
    }
    return s;
}