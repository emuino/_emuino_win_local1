This is a list of useful Ace related code. Feel free to add your project, or a project you found that seems relevant.

###Externally-hosted extensions:
+ **paredit-js** structured navigation and editing of s-expressions
  - http://robert.kra.hn/projects/paredit-js 


###Built in extensions:

extensions from [lib/ace/ext](https://github.com/ajaxorg/ace/tree/master/lib/ace/ext) which are included in the core repository but not loaded by ace.js

+ **Emmet**: [emmet](https://github.com/emmetio/emmet) plugin for Ace 
  - [usage example](https://github.com/ajaxorg/ace/blob/master/demo/emmet.html#L26-L40)
  - [demo](http://ace.c9.io/demo/emmet.html)

+ **beautify**: code formatter
  - Supports only PHP 

+ **chromevox**: Support for http://www.chromevox.com/ screen reader.
  - [usage example](https://github.com/ajaxorg/ace/blob/master/demo/chromevox.html)
  - [demo](http://ace.c9.io/demo/chromevox.html)

+ **keybinding_menu**: Generates a popup menu with current keybindings
  - [usage example](https://github.com/ajaxorg/ace/blob/master/demo/keyboard_shortcuts.html)
  - [demo](http://ace.c9.io/demo/keyboard_shortcuts.html)

+ **Language Tools**: adds support for autocompletion and snippets
  - [usage example](https://github.com/ajaxorg/ace/blob/master/demo/autocompletion.html)
  - [demo](http://ace.c9.io/demo/autocompletion.html)

+ **searchbox** used for default find replace dialog
  - loaded automatically when pressing `ctrl-f` (`cmd-f` on mac)

+ **[Statusbar](https://github.com/ajaxorg/ace/blob/master/lib/ace/ext/statusbar.js#L32)**: simple status widget showing selection and keyboard handler status
  - [usage example](https://github.com/ajaxorg/ace/blob/master/demo/statusbar.html)
  - [demo](http://ace.c9.io/demo/statusbar.html)


+ **Static Highlighter**: static code highlighter
  - can be used on [client side](https://github.com/ajaxorg/ace/blob/master/demo/static-highlighter.html) or  from [nodejs server](https://github.com/ajaxorg/ace/blob/master/demo/static-highlighter/server.js) 
  - [demo](http://ace.c9.io/demo/static-highlighter.html)

+ **modelist** detect mode based on file path
  -  [usage example](https://github.com/ajaxorg/ace/blob/master/demo/modelist.html)


+ **whitespace** helps to detect indentation based on file contents, convert between tabs and spaces, trim trailing whitespace
  - https://github.com/ajaxorg/ace/blob/v1.2.0/lib/ace/ext/whitespace.js
  - https://github.com/ajaxorg/ace/blob/v1.2.0/demo/kitchen-sink/demo.js#L208
  - https://github.com/ajaxorg/ace/blob/v1.2.0/demo/kitchen-sink/demo.js#L344