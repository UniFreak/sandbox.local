/**
 * Ajax: Asynchronous JavaScript + XML
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

