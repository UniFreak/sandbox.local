function printHelp()
{
    console.log('fileIo (c) FangHao');
    console.log('');
    console.log('usage:');
    console.log('--help         print this help');
    console.log('--file         read and output {FILE}');
    console.log('');
}

var args = require('minimist')(
    process.argv.slice(2),
    { string: 'file'}
);

if (args.help || !args.file) {
    printHelp();
    process.exit();
}

var hello = require('./modules/filetube.js');
hello.say(args.file, function(err, contents) {
    if (err) {
        console.error(err);
    } else {
        console.log(contents.toString());
    }
});
