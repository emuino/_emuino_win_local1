## Howtos

How do you get access to the editor (not the DOM element, but the ace.edit() editor)?

```javascript
window.onload = function() {
  window.aceEditor = ace.edit("editor");
}

// Then elsewhere...
window.aceEditor.getSession().insert("Awesome!");
```