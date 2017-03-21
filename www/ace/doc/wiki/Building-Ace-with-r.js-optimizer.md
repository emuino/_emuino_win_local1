Ace uses require.js and ace modules used in application can be optimized as usual.

However some parts need special handling.
* Ace has many themes and modes, and including them all in minimized file isn't good for performance.
* Some Ace modes use web-workers for syntax validation, and files that need to be loaded into web-worker must be optimized separately.

Easiest way is to keep ace/build as well and let Ace to automatically load files from it when needed.

Add the following snippet in optimized version

```js
var config = require("ace/config")
config.set("packaged",true)

var path = "js/modules/ace/build/src-min"
config.set("basePath", path)
```


Paths for each type of module can be configured separately
```js
config.set("workerPath", path)
config.set("modePath", path)
config.set("themePath", path)
```
this is useful since due to crossorigin restrictions workers must be served from the same domain as main app.



