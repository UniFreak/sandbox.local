function printHelp()
{
    console.log('helloWorld (c) FangHao');
    console.log('');
    console.log('usage:');
    console.log('--help         print this help');
    console.log('--name         say hello to {NAME}');
    console.log('');
}

// read in command arguments
// 1. the native way(0 is `node`, 1 is filename, 2 will be the one)
var name = process.argv[2];
// 2. using `minimist` module(args are passed as `--arg=value`)
var args = require('minimist')(
    process.argv.slice(2),
    { string: 'name'}
);

if (args.help || !args.name) {
    printHelp();
    process.exit();
}

console.log('hello ' + name);
console.log(' and ' + args.name);