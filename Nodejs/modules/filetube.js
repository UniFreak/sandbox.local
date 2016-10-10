function say(filename, cb)
{
    return fs.readFile(filename, function(err, contents) {
        if (err) {
            cb(err);
        } else {
            // simulate another async thing(doing db, ajax...)
            setTimeout(function() {
                cb(null, contents);
            }, 1000);
        }
    })
    console.log(fs.readFileSync(filename).toString());
}

var fs = require('fs');

module.exports.say = say;