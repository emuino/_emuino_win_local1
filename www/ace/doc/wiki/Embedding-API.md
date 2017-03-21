ACE can be easily embedded into any existing web page. You can either use one of pre-packaged versions of [ace](https://github.com/ajaxorg/ace-builds/) (just copy one of `src*` subdirectories somewhere into your project), or use requireJS to load the contents of [lib/ace](https://github.com/ajaxorg/ace/tree/master/lib/ace) as `ace`.

Also, take a look at the one of the included demos for how to use Ace:

* [Quick and dirty](https://github.com/ajaxorg/ace-builds/blob/master/editor.html) 
* [Full kitchen-sink](https://github.com/ajaxorg/ace/blob/master/demo/kitchen-sink/demo.js)

The easiest version to embed Ace is to simply call:

```html
<div id="editor" style="height: 500px; width: 500px">some text</div>
<script src="src/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("editor");
</script>
```

## Setting Theme and Language Mode

To change the theme, configure the editor to use the theme using its module name. The theme file will be loaded on demand:

```javascript
editor.setTheme("ace/theme/twilight");
```

By default, the editor only supports plain text mode. However, all other language modes are available as separate modules. Modes are also loaded on demand, and can be included like this:

## Retrieving Editor States

Ace keeps all the editor states (selection, scroll position, etc.) in `editor.session`, which is very useful for making a tabbed editor:

```javascript
var EditSession = require("ace/edit_session").EditSession
var js = new EditSession("some js code")
var css = new EditSession(["some", "css", "code here"])
// and then to load document into editor, just call
editor.setSession(js)
```

## Quickstart

To set and get content:`

```javascript
editor.setValue("the new text here"); // or session.setValue
editor.getValue(); // or session.getValue
```

To get selected text:

```javascript
editor.session.getTextRange(editor.getSelectionRange());
```

To insert at cursor:

```javascript
editor.insert("Something cool");
```

To get the current cursor line and column:

```javascript
editor.selection.getCursor();
```

To go to a line:

```javascript
editor.gotoLine(lineNumber);
```

To get total number of lines:

```javascript
editor.session.getLength();
```

To set the default tab size:

```javascript
editor.getSession().setTabSize(4);
```

To use soft tabs:

```javascript
editor.getSession().setUseSoftTabs(true);
```

To set the font size:

```javascript
document.getElementById('editor').style.fontSize='12px';
```

To toggle word wrapping:

```javascript
editor.getSession().setUseWrapMode(true);
```

To set line highlighting:

```javascript
editor.setHighlightActiveLine(false);
```

To set the print margin visibility:

```javascript
editor.setShowPrintMargin(false);
```

To set the editor to read-only:

```javascript
editor.setReadOnly(true);  // false to make it editable
```

### Triggering Resizes

ACE only resizes itself on window events. If you resize the editor div in another manner, and need ACE to resize, use the following:

```javascript```
editor.resize()
```

### Searching in Code

The main ACE search functionality is defined in [_search.js_](https://github.com/ajaxorg/ace/blob/master/lib/ace/search.js). The following options are available to you for your search parameters:

 * `needle`: The string or regular expression you're looking for
 * `backwards`: Whether to search backwards from where cursor currently is. Defaults to `false`.
 * `wrap`: Whether to wrap the search back to the beginning when it hits the end. Defaults to `false`.
 * `caseSensitive`: Whether the search ought to be case-sensitive. Defaults to `false`.
 * `wholeWord`: Whether the search matches only on whole words. Defaults to `false`.
 * `range`: The [[Range]] to search within. Set this to `null` for the whole document
 * `regExp`: Whether the search is a regular expression or not. Defaults to `false`.
 * `start`: The starting [[Range]] or cursor position to begin the search
 * `skipCurrent`: Whether or not to include the current line in the search. Default to `false`.
 
Here's an example of how you can set up search on the editor object:

```javascript
editor.find('needle',{
  backwards: false,
  wrap: false,
  caseSensitive: false,
  wholeWord: false,
  regExp: false
});
editor.findNext();
editor.findPrevious();
```

Here's how you can perform a replace:

```javascript
editor.find('foo');
editor.replace('bar');
```

And here's a replace all: 

```javascript
editor.replaceAll('bar');
```

(`editor.replaceAll` uses the needle set by `searchoptions.needle.`)

### Listening to Events

To listen for an `onchange`:

```javascript
editor.getSession().on('change', callback);
```

To listen for an `selection` change:

```javascript
editor.getSession().selection.on('changeSelection', callback);
```

To listen for a `cursor` change:

```javascript
editor.getSession().selection.on('changeCursor', callback);
```

### Adding New Commands and Keybindings

To assign key bindings to a custom function:

```javascript
editor.commands.addCommand({
    name: 'myCommand',
    bindKey: {win: 'Ctrl-M',  mac: 'Command-M'},
    exec: function(editor) {
        //...
    }
});
```