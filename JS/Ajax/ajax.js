/**
 * Ajax: Asynchronous JavaScript + XML
 */

/**
 * ==================== XMLHttpRequest ====================
 */
/**
 * 创建 XHR 对象
 */
function createXHR() {
    if (typeof XMLHttpRequest != "undefined") {
        return new XMLHttpRequest(); // 原生 XHR
    } else if (typeof ActiveXObject != "undefined") { // IE 早期版本
        if (typeof arguments.callee.activeXString != "string") {
            var versions = [
                "MSXML2.XMLHttp.6.0",
                "MSXML2.XMLHttp.3.0",
                "MSXML2.XMLHttp"
            ], i, len;

            for (i = 0, len = versions.length; i < len; i++) {
                try {
                    new ActiveXObject(versions[i]);
                    arguments.callee.activeXString = versions[i]; // 缓存可用的版本字符
                    break;
                } catch (ex) {
                    // do nothing
                }
            }

            return new ActiveXObject(arguments.callee.activeXString);
        } else {
            throw new Error("No XHR object available");
        }
    }
}

var xhr = createXHR();
/**
 * 异步请求时, 则可以检测 .readyState 的属性值. 此属性值指示请求/响应过程的当前活动状态:
 * - 0: 未初始化, 尚未调用 open() 方法
 * - 1: 启动, 已调用 open(), 未调用 send()
 * - 2: 发送, 已调用 send(), 未收到响应
 * - 3: 接受, 收到部分响应数据
 * - 4: 完成, 收到全部响应数据
 * 每次 .readyState 变动都会触发 onreadystatechange 事件, 必须在调用 open() 之前绑定
 * onreadystatechange 事件处理程序
 *
 * 收到响应后, 响应数据会自动填充 XHR 对象的属性:
 * - .responseText: 返回的响应主体文本
 * - .responseXML: 如果相应内容类型是 text/xml 或 application/xml, 该属性包含响应数据的 XML DOM
 *    文档
 * - .status: 相应的 HTTP 状态
 * - .statusText: HTTP 状态的说明 (不要依赖此属性)
 */
xhr.onreadystatechange = function() {
    if (xhr.readyState == 4) { // 此处没用 this 对象, 因为在某些浏览器中会导致执行失败
        if ((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304) {
            console.log(xhr.responseText);
            console.log(xhr.getResponseHeader("Date"));
            console.log(xhr.getAllResponseHeaders());
        } else {
            console.warn("request failed: " + xhr.status);
        }
    }
}
/**
 * 参数 1 指定请求方式: get 或 post
 * 参数 2 指定 url: 必须遵循同源策略限制: 同协议, 同域名, 同端口
 * 参数 3 指定是否以异步方式
 */
xhr.open("post", "server.php", true); // 准备请求: 使用 get 方法请求 server.php, 异步方式
/**
 * 如果要模仿表单提交, 需要:
 * 1. 将 Content-Type header 设置为 application/x-www-form-urlencoded
 * 2. 使用 encodeURIComponent 编码 post 字符串
 * 之后在服务端, 可以用 php 的 $_POST 访问请求数据(否则需使用 $HTTP_RAW_POST_DATA)
 *
 * 可以通过 .setRequestHeader() 设置请求的 header, 此函数必须在 open() 之后, send() 之前
 * 收到请求后, 可以使用 .getResponseHeader() 或 .getAllResponseHeaders() 获取响应 header
 */
xhr.setRequestHeader("MyHeader", "MyValue");
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
var fakePost = encodeURIComponent("PostField") + "=" + encodeURIComponent("PostValue");
xhr.send(fakePost); // 发送请求

/**
 * ==================== XMLHttpRequest 2 级 ====================
 */

/**
 * ==================== 进度事件 ====================
 */