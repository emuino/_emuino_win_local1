Core ace components (`editor`, `session`, `renderer`, `mouseHandler`) implement optionProvider interface

    setOption(optionName, optionValue)
    setOptions({
        optionName : optionValue
        ...
    })
    getOption(optionName)
    getOptions()

here's a list of all supported options. Where not indicated otherwise option values are boolean.

### editor options
    selectionStyle: "line"|"text"
    highlightActiveLine: 
    highlightSelectedWord: 
    readOnly: 
    cursorStyle: "ace"|"slim"|"smooth"|"wide"
    mergeUndoDeltas: false true "always"
    behavioursEnabled: 
    wrapBehavioursEnabled: 
    autoScrollEditorIntoView: // this is needed if editor is inside scrollable page

### renderer options
    hScrollBarAlwaysVisible:
    vScrollBarAlwaysVisible:
    highlightGutterLine:
    animatedScroll:
    showInvisibles:
    showPrintMargin:
    printMarginColumn:
    printMargin:
    fadeFoldWidgets:
    showFoldWidgets:
    showLineNumbers:
    showGutter:
    displayIndentGuides:
    fontSize: number or css font-size string
    fontFamily: css 
    maxLines: 
    minLines:
    scrollPastEnd: 
    fixedWidthGutter:
    theme: path to a theme e.g "ace/theme/textmate"

### mouseHandler options
    scrollSpeed: number
    dragDelay:  number
    dragEnabled:
    focusTimout: number
    tooltipFollowsMouse:

### session options
    firstLineNumber: number
    overwrite:
    newLineMode:
    useWorker:
    useSoftTabs:
    tabSize: number
    wrap: 
    foldStyle:
    mode: path to a mode e.g "ace/mode/text"

## editor options defined by extensions
    enableMultiselect: 
    enableEmmet: 
    enableBasicAutocompletion:
    enableSnippets:
    spellcheck:
    useElasticTabstops:
    
`editor.setOption` will also modify options of `editor.session`, `editor.renderer` and `editor.$mouseHandler`