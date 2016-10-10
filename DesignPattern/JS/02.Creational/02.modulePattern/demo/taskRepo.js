/**
 * responsible for all DB work relating to task
 * but for simplicity, all actual DB work is simplified
 */
var repo = function() {
    var DB = {};

    var get = function(id) {
        console.log('Getting task: ' + id);
        // in reality, here should be the DB work
        return {
            name: 'new task from db'
        }
    };

    var save = function(task) {
        console.log('Saving task: ' + task.name + ' to DB');
    };

    // revealing
    return {
        get: get,
        save: save
    }
}

// notice repo got excuted right here
module.exports = repo();