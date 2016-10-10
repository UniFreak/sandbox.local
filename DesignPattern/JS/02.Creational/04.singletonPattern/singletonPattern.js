var Singleton = (function() {
    var instance;
    function createInstance() {
        return new Object('singleton');
    }

    return {
        getInstance: function() {
            if (!instance) {
                instance = createInstance();
            }
            return instance;
        }
    }
})()

var one = Singleton.getInstance();
var two = Singleton.getInstance();
if (one === two) {
    console.log('same one');
}