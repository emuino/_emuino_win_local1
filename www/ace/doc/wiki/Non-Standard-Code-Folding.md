All modes that use C style folding (C, JavaScript, CSS, etc...) *(SQL Server mode also supports this)* support non-standard code folding using special comment notations:

### Region Folding

 - Supported using lineComment or blockComment (blockComment is needed primarily for CSS mode)
 - The pound symbol `#` is optional before `region` and `endregion`
 - lineComment example:

```javascript
//#region [optional description]
     [code that will be folded here]
//#endregion 
```

 - blockComment example:
```javascript
/*#region [optional description]*/
     [code that will be folded here]
/*#endregion*/
```

A simpler alternative to `#region` sintax is to add `{` or `[` at the end of line comment
```javascript
// [optional description] {
     [code that will be folded here]
// }
```

### Triple Star (Asterisk) Folding

 - Single line blockComments that start with 3 stars will render a fold widget that folds the comment and the code below it
```javascript
/*** [section title] ***/
[code that will be folded here]

/*** [next section title] ***/
```

