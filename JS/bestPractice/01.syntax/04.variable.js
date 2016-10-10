/**
 * Hosting:
 *     A var statement declares variables that are scoped to the running
 *     execution context's VariableEnviroment. (1) Var variables are created
 *     when their containing LexicalEnvironment is instantiated (2) and are
 *     initialized to undefined when created
 */

console.log(myVar); // this won't cause error, becuase of (1)
                    // it also won't be 10, becuase of (2)
var myVar = 10;

function func() {
    myVar = 25;
    var myVar;
}
func(); // this don't change myVar, because they are in different scope

console.log(myVar);

/**
 * Conclusion:
 *   all var declarations go to the top of your scope
 */