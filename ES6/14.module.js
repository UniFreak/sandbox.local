/**
 * ES6 modules are automatically strict-mode code,
 * even if you don’t write "use strict"; in them.
 *
 * You can use import and export in modules
 *
 * You don’t have to put everything in an IIFE or a callback.
 * Just go ahead and declare everything you need.
 * Since the code is a module, not a script,
 * all the declarations will be scoped to that module,
 * not globally visible across all scripts and modules.
 * Export the declarations that make up the module’s public API, and you’re done
 */

// ==================== file: kittydar.js module | export ====================
/**
 * There are two way to export feature
 * - use `export` keyword like below
 * - use export list(can be anywhere of the top-level scope)
 *   so you don't need the `export` keyword
 *   `export {detectCats, Kittydar}`
 *   this way, you can gave them alias:
 *   ```
 *   export {
 *     KittyDar as kitty,
 *     detectCats as tellCats
 *   };
 *   ```
 *
 * You can mix them up
 */
function detectCats(canvas, options) {
  var kittydar = new Kittydar(options);
  return kittydar.detectCats(canvas);
}

export class Kittydar {
}

// This helper function isn't exported.
function resizeCanvas() {
}

export {
    detectCats as tellCats
}

// ==================== file: module client | import ====================
/**
 * several way to import
 * - `import {tellCats as detectCats} from "kittydar.js"`
 * - `import {tellCats} from "kittydar.js"`
 * - `import * as kitty from "kittydar.js"`
 */
import {tellCats} from "kittydar.js";

function go() {
    var canvas = document.getElementById("catpix");
    var cats = detectCats(canvas);
    drawRectangles(canvas, cats);
}

// ==================== file:world-foods.js | export from ====================
/**
 * Each one of these export-from statements is similar to
 * an import-from statement followed by an export. Unlike a real import,
 * this doesn’t add the re-exported bindings to your scope.
 * So don’t use this shorthand if you plan to write some code in world-foods.js
 * that makes use of Tea. You’ll find that it’s not there
 */
 export {Tea, Cinnamon} from "sri-lanka";

 export {Coffee, Cocoa} from "equatorial-guinea";

 export * from "singapore";