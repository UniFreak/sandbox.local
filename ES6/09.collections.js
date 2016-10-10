// `keyed collections` inclues `Set`, `Map`, `WeakSet` and `WeakMap`

// Collection VS Object VS Plain Object
// 1. expressions like `obj.key` or `obj[key]` cannot be used to access hash table data.
//    you have to write `map.get(key)`
// 2. hash table entries, unlike properties, are not inherited via the prototype chain
// 3. unlike plain Objects, Map and Set do have methods

// ==================== SET ====================
var fruits = new Set(['apple', 'banana', 'pear', 'apple']);
// you can pass any `iterable`
// this allow you:
// 1. eliminating duplicate values of an array with a signle line of code
// 2. pass a generator and get a set containing all yielded values
// 3. copy another existing set
var another = new Set(fruits);
console.log(fruits, another);
// if you try to add a duplicate value, nothing will happen
fruits.add('apple');
fruits.delete('pear');

console.log(fruits, fruits.size, fruits.has('apple'), fruits.keys(), fruits.values(), fruits.entries());
// sets DON'T support indexing
console.log(fruits[6]);
for (var v of fruits) {
    console.log(v);
}
fruits.forEach(function(value, alsoValue, set) {
    console.log(value);
    console.log(alsoValue);
    console.log(set);
})
fruits.clear();

// ==================== Map ====================
// An object is made of keys (always strings) and values, whereas in Map,
// any value (both objects and primitive values) may be used as either a key or a value
var peoples = new Map([['zhangsan', {age: '23', sex: 'male'}], ['lisi', {age: '23', sex: 'femail'}]]);
console.log(
    peoples,
    peoples.size,
    peoples.has('zhangsan'),
    peoples.get('zhangsan'),
    peoples.keys(),
    peoples.values(),
    // this is in fact another name of `peoples[Symbol.iterator]()`
    peoples.entries(),
    // also don't support indexing
    peoples.zhangsan
    );
peoples.set('zhangsan', null);
peoples.delete('zhangsan');
peoples.forEach(function(value, key, map) {
    console.log(value);
    console.log(key);
    console.log(map);
});
for (let [key, value] of peoples) {
    console.log(key);
    console.log(value);
}
peoples.clear();

// ==================== WeakMap & WeakSet ====================
// they behave exactly like Map and Set
// but:
//   WeakMap supports only new, .has(), .get(), .set(), .delete()
//   WeakSet supports only new, .has(), .add(), .delete()
//   the values stored in WeakSet and the keys stored in a WeakMap must be objects
//   neither type of weak collection is iterable