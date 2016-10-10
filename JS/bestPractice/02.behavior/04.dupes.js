function dupes(a, b, a) {
    /**
     * js try to help:
     *   no error, duplicated parameter a is the last seen value
     *   here is 3
     * conclusion:
     *   use strict
     */
    console.log(a);
}

dupes(1, 2, 3);