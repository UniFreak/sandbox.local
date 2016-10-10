var json = '[1, "string", 3, {"name": "fanghao"}]';
var a = eval(json);
var o = a[3];
console.log(a, o);