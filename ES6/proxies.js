/**
 * understand those **internal**(emphasized by [[]] notation) methods
 * you can't call, delete or overwite these method like ordinary methods
 *
 * 1. called when `obj.prop` or `obj[key]`
 *    `obj` might be an object on `receiver`'s prototype chain
 *    `receiver` is sthe object where we first stared searching for this property
 * obj.[[Get]](key, receiver)
 *
 * 2. called when `obj.prop = value` or `obj[key] = value`
 * obj.[[Set]](key, value, receiver)
 *
 * `obj.prop += 2` or `++` or `--` will call [[Get]] first then [[Set]]
 *
 * 3. called when `key in obj`
 * obj.[[HasProperty]](key)
 *
 * 4. called when `for (key in obj)`, return an iterator object
 * obj.[[Enumerate]]()
 *
 * 5. called when `obj.__proto__` or `Object.getPrototypeOf(obj)`
 * obj.[[GetPrototypeOf]]()
 *
 * 6. called when `functionObj()` or `x.method()`
 *    not every object is a function
 * functionObj.[[Call]](thisValue, arguments)
 *
 * 7. called when `new Something()`
 *    `newTarget` play a role in subclassing
 * constructObj.[[Construct]](arguments, newTarget)
 *
 * others are:
 * 8.  [[SetPrototypeOf]]
 * 9.  [[GetOwnProperty]]
 * 10. [[DefineOwnProperty]]
 * 11. [[OwnPropertyKeys]]
 * 12. [[IsExtensible]]
 * 13. [[PreventExtensions]]
 * 14. [[Delete]]
 */


var target = {
    a: 1,
    b: 2,
    c: 3
};

// the handler object’s methods can override any of the proxy’s internal methods
// known as `traps`
var handler = {
    get: function(target, name) {
        return (
            name in target ? target[name] : 42
        );
    },
    set: function(target, key, value, reciever) {
        throw new Error('Pleas dont set properties on this objec');
    }
    // other traps including(line up with the 14 internal methods above)
    //   .getPrototypeOf()
    //   .setPrototypeOf()
    //   .isExtensible()
    //   .preventExtensions()
    //   .getOwnPropertyDescriptor()
    //   .defineProperty()
    //   .has()
    //   .deleteProperty()
    //   .ownKeys()
    //   .apply()
    //   .construct()
};

// all of proxy’s internal methods are forwarded to target
var proxy = new Proxy(target, handler);

console.log(proxy.a);
console.log(proxy.b);
console.log(proxy.c);
console.log(proxy.meaningOfLife);
proxy.a = 'wtf';