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

var hello = require('./modules/filetube_asynquence.js');
hello.say(args.file)
    .val(function(contents) {
        console.log(contents.toString());
    })
    .or(function(err) {
        console.error(err);
    })