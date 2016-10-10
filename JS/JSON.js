/**
 * JSON 是一种数据格式, 与 XML 相比, 具有以下优点:
 * 1. JSON 语法和 JS 相像
 * 2. XML 语法冗长繁琐
 * 3. XML 需要创建 DOM 节点, 然后从节点中读取数据, 过程复杂
 *
 * 使用 JSON 要注意:
 * 1. JSON 字符串必须使用**双**引号
 * 2. JSON 中的对象属性必须使用双引号
 * 3. 同一个对象不应有两个相同属性(前者会被覆盖)
 */

var o = [
    {
        title: "professional javascript",
        authors: ["Nicho"],
        year: 2011
    },
    {
        title: "professional ajax",
        authors: ["Nicho", "Joe"],
        year: 2007
    }
];

var json = '[{"title":"professional javascript","authors":["Nicho"],"year":2011},{"title":"professional ajax","authors":["Nicho","Joe"],"year":2007}]';

/**
 * # 序列化: JSON.stringify()
 * 1. 如果有 toJSON 方法, 则调用该方法
 */
console.log('==================== 序列化 ====================');
o.toJSON = function() {
    return this[0];
}
console.log(JSON.stringify(o));

/**
 * 2. 如果提供了第二个参数, 则应用这个过滤器
 */
// 过滤器可以是数组
console.log(JSON.stringify(o, ['title']));
// 也可以是函数
console.log(JSON.stringify(o, function(key, value) {
    switch(key) {
        case 'title':
            return undefined; // 返回 undefined 会删除该属性
        default:
            return value;
    }
}))
/**
 * 3. 如果提供了第三个参数, 则进行缩进(同时也会换行)
 */
// 缩进可以使数字(小于10)
console.log(JSON.stringify(o, null, 4));
// 也可以是字符串(长度小于 10)
console.log(JSON.stringify(o, null, '***'));

/**
 * # 解析
 * - 使用 eval() --> 不推荐, 安全风险
 */
console.log('==================== 解析 ====================')
console.log(eval(json));

/**
 * - 使用 JSON.parse()
 *   parse() 也可以接受第二个参数, 和 stringify() 的过滤器是相反的操作
 */
console.log(JSON.parse(json));

/**
 * - 如果浏览器不支持 JSON 对象, 则需使用 JSON-js shim
 *   https://github.com/douglascrockford/JSON-js
 */