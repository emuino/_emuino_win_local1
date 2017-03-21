Ace supports live syntax checking using WebWorker.
Currently it is available for JavaScript, JSON, PHP, CoffeeScript, CSS, XQuery modes.

>  Important! due to cross origin restrictions on webWorkers worker files must be served from the main domain.

In packaged version path to the folder with `worker-*` files can be configured with

```js
require("ace/config").set("workerPath", "path/to/ace");
```
By default ace searches for ace.js script on the page and uses it's path as workerPath, so setting it explicitly is only required if workerPath is different or the script is missing.

Syntax checking is enabled by default. To disable syntax checking use

```js
editor.session.setOption("useWorker", false);
```

or to disable for all new sessions at once
```js
require("ace/config").setDefaultValue("session", "useWorker", false);
```

To add syntax validation to a new language 

1. Find a syntax checker for the language. E.g. for javascript Ace uses jsHint

2. add createWorker method to the mode
```js
    var WorkerClient = require("ace/worker/worker_client").WorkerClient;
    this.createWorker = function(session) {
        var worker = new WorkerClient(["ace"], "path/to/worker", "WorkerModule");
        worker.attachToDocument(session.getDocument());

        worker.on("lint", function(results) {
            session.setAnnotations(results.data);
        });

        worker.on("terminate", function() {
            session.clearAnnotations();
        });

        return worker;
    };
```

3.  create a module that invokes linter and converts it's output to the format that ace can understand

```js
define(function(require, exports, module) {
"use strict";

var oop = require("ace/lib/oop");
var Mirror = require("ace/worker/mirror").Mirror;
var lint = require("./my_language/lint");

var WorkerModule = exports.WorkerModule = function(sender) {
    Mirror.call(this, sender);
    this.setTimeout(500);
    this.setOptions();
};

// Mirror is a simple class which keeps main and webWorker versions of the document in sync
oop.inherits(WorkerModule, Mirror);

(function() {
    this.onUpdate = function() {
        var value = this.doc.getValue();
        var errors = [];
        var results = lint(value);

        for (var i = 0; i < results.length; i++) {
            var error = results[i];
            // convert to ace gutter annotation
            errors.push({
                row: error.line-1, // must be 0 based
                column: error.character,  // must be 0 based
                text: error.message,  // text to show in tooltip
                type: "error"|"warning"|"info"
            });
        }
        this.sender.emit("lint", errors);
    };
}).call(WorkerModule.prototype);

});

```


For an example take a look at 
https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/javascript.js#L100-L113 and
https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/javascript_worker.js


