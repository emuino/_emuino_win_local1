##Embedding Ace

Ace can be easily embedded into any existing web page. You can either use one of pre-packaged versions of [ace](https://github.com/ajaxorg/ace-builds/) (just copy one of `src*` subdirectories somewhere into your project), or use requireJS to load contents of [lib/ace](https://github.com/ajaxorg/ace/tree/master/lib/ace) as `ace`
Also take a look at the one of [included](https://github.com/ajaxorg/ace-builds/blob/master/editor.html) [demos](https://github.com/ajaxorg/ace/blob/master/demo/kitchen-sink/demo.js) of how to use Ace.

The easiest version is simply:

```html
<div id="editor" style="height: 500px; width: 500px">some text</div>
<script src="src/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("editor");
</script>
```

To change the theme simply configure the editor to use the theme using its module name. The Theme file will be loaded on demand:

```javascript
editor.setTheme("ace/theme/twilight");
```

By default the editor only supports plain text mode. However all other language modes are available as separate modules. Modes are also loaded on demand.

The mode can be used like this:

```javascript
editor.session.setMode("ace/mode/javascript");
```

Ace keeps all the editor state (selection, scroll position, etc.) in `editor.session` which is very useful for making tabbed editor

```javascript
var EditSession = require("ace/edit_session").EditSession
var js = new EditSession("some js code")
var css = new EditSession(["some", "css", "code here"])
// and then to load document into editor just call
editor.setSession(js)
```


## API
Set/get content:

```javascript
editor.setValue("the new text here"); // or session.setValue
editor.getValue(); // or session.getValue
```

Get selected text:

```javascript
editor.session.getTextRange(editor.getSelectionRange());
```

Insert at cursor:

```javascript
editor.insert("Something cool");
```

Get the current cursor line and column:

```javascript
editor.selection.getCursor();
```

Go to line:

```javascript
editor.gotoLine(lineNumber);
```

Get total number of lines:

```javascript
editor.session.getLength();
```

Tab size:

```javascript
editor.getSession().setTabSize(4);
```

Use soft tabs:

```javascript
editor.getSession().setUseSoftTabs(true);
```

Font size:

```javascript
document.getElementById('editor').style.fontSize='12px';
```

Toggle Word Wrap:

```javascript
editor.getSession().setUseWrapMode(true);
```

Toggle Highlight line:

```javascript
editor.setHighlightActiveLine(false);
```

Set Print Margin Visibility:

```javascript
editor.setShowPrintMargin(false);
```

Set to read-only:

```javascript
editor.setReadOnly(true);  // false for the editable
```

Resize

ACE only resizes itself on window events. If you resize the editor div in another manner and need ACE to resize use the following:

```javascript```
editor.resize()
```

Find

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

Replace:

```javascript
editor.find('foo');
editor.replace('bar');
```

Replace All:

```javascript
editor.replaceAll('bar');
```

## Events
OnChange:

```javascript
editor.getSession().on('change', callback);
```

Selection change:

```javascript
editor.getSession().selection.on('changeSelection', callback);
```

Cursor change:

```javascript
editor.getSession().selection.on('changeCursor', callback);
```

Assign key binding to custom function:

```javascript
editor.commands.addCommand({
    name: 'myCommand',
    bindKey: {win: 'Ctrl-M',  mac: 'Command-M'},
    exec: function(editor) {
        //...
    }
});
```

## Still to work out


## Howtos

How do you get access to the editor (not the DOM element, but the ace.edit() editor)?

```javascript
window.onload = function() {
  window.aceEditor = ace.edit("editor");
}

// Then elsewhere...
window.aceEditor.getSession().insert("Awesome!");
```