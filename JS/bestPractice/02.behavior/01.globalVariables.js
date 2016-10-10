/**
 * javascript try to be helpful, but that can sometime be frustrating
 *
 * 'use strict':
 *   don't try to help me, I know what I'm doing, and I'm prefered to
 *   know if I'm doing things wrong
 */
function func(param) {
    /**
     * js try to help:
     *   this won't blow up, js created noExist in global for us
     */
    nonExist = param;
}

function func2(param) {
    /**
     * conclusion:
     *   use strict(can be scoped)
     */
    'use strict';
    nonExist2 = param;
}