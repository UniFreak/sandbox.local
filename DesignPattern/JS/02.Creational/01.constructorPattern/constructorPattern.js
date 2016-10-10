function MyClass(param) {
    var privateVar = 'foo';    // private variable
    this.publicVar = param;    // public variable


    this.privilegedMethod = function() { // privileged public method
        console.log(privateVar);
    }
}

MyClass.staticVar = 'baz'; // static variable
MyClass.staticMethod = function() {  // static method
    console.log('calling static method');
}

// instance public method will be available to all instance but only load once in memory
MyClass.prototype.publicMethod = function() {
    console.log(this.publicVar);
}


var obj = new MyClass('testing');
console.log(obj.publicMethod());
console.log(MyClass.staticVar);