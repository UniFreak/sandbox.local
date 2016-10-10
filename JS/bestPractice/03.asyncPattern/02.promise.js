/**
 * the big thing promise do for us, is that they extract callbacks from within
 * the parameters we are passing into a function, and allow us to seperate concern
 * a little bit
 */

/**
 * Before(callback hell):
 */
function asyncMethod(message, callback) {
    setTimeout(function() {
        console.log(message);
        callback();
    }, 500)
}

asyncMethod('Open DB Connection', function() {
    asyncMethod('Find User', function() {
        asyncMethod('Validate User', function() {
            asyncMethod('Do Stuff', function() {})
        })
    })
})

/**
 * Better:
 *   Use promise, thanable
 */
function asyncMethodBetter(message) {
    return new Promise(function(fulfill, reject) {
        setTimeout(function() {
            console.log(message);
            fulfill();
        }, 500);
    });
}

asyncMethodBetter('Open DB Connection').then(function() {
    asyncMethodBetter('Find User').then(function() {
        asyncMethodBetter('Validate User').then(function() {
            asyncMethodBetter('Do Stuff').then(function() {})
        })
    })
})

/**
 * even better:
 *   use named function
 */
function asyncMethodEvenBetter(message) {
    return new Promise(function(fulfill, reject) {
        setTimeout(function() {
            console.log(message);
            fulfill();
        }, 500);
    });
}

function findUser() {
    asyncMethodEvenBetter('Find User')
        .then(validateUser)
}

function validateUser() {
    asyncMethodEvenBetter('Validate User')
        .then(doStuff)
}

function doStuff() {
    asyncMethodEvenBetter('Do Stuff')
        .then(function() {})
}

asyncMethodEvenBetter('Open DB Connection')
    .then(findUser)

/**
 * best for now:
 *   return promise, line up then
 */
function asyncMethodBest(message) {
    return new Promise(function(fulfill, reject) {
        setTimeout(function() {
            console.log(message);
            fulfill();
        }, 500);
    });
}

function findUserBest() {
    return asyncMethodBest('Find User')
}

function validateUserBest() {
    return asyncMethodBest('Validate User')
}

function doStuffBest() {
    return asyncMethodBest('Do Stuff')
}

asyncMethodBest('Open DB Connection')
    .then(findUserBest)
    .then(validateUserBest)
    .then(doStuffBest)