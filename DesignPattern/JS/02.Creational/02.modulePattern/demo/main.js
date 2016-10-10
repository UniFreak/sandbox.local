var Task = require('./Task');
var Repo = require('./taskRepo');

var task1 = new Task(Repo.get(1));
var task2 = new Task({name: 'demo2'});
var task3 = new Task({name: 'demo3'});
var task4 = new Task({name: 'demo4'});

task1.complete();
task2.save();
task3.save();
task4.save();