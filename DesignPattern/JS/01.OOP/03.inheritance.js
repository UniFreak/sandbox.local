var task = {};
task.toString = function() {
    return 'plain task';
}

var urgentTask = Object.create(task);
urgentTask.toString = function() {
    return 'urgent task';
}

console.log(task.toString());
console.log(urgentTask.toString());