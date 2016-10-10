var task = {
    title: 'task title',
    description: 'task description'
}
Object.defineProperty(task, 'toString', {
    value: function() {
        return this.title + ' ' + this.description;
    },
    writable: false,
    enumerable: false,
    configurable: true,
});

var urgentTask = Object.create(task);
Object.defineProperty(urgentTask, 'toString', {
    value: function() {
        return 'urgent:' + this.title;
    },
    writable: false,
    enumerable: false,
    configurable: true
})

console.log(task);
console.log(task.toString());
console.log(urgentTask);
console.log(urgentTask.toString());