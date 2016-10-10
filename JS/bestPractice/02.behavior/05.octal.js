var x = 120;
    /**
     * js try to help:
     *   it treat 0-leading number as octal
     * conslusion:
     *   use strict, to prevent that
     *   use `parseInt(12, 8)` to make a octal
     */
    y = 012;

console.log(x + y);