var Repo = require('./taskRepo');

/**
 * you will see this a lot: passing in json object
 * instead of twelve parameters
 */
var Task = function(data) {
    this.name = data.name;
    this.completed = false;
}

Task.prototype.complete = function() {
    console.log('completing task:' + this.name);
    this.completed = true;
}
Task.prototype.save = function() {
    console.log('saving task: ' + this.name);
    console.log(Repo.save(this));
}


module.exports = Task;