<div class="sketch">

	<div class="menu">
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnNewClick(this);">New</button>
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnOpenClick(this);">Open..</button>
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnSaveClick(this);">Save</button>
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnSaveAsClick(this);">Save as..</button>
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnRunClick(this);">Run</button>
		<span class="saved"></span>
		<span class="fname"></span>
	</div>

	<div class="frame">
		<div class="editor" id="editor-{{ id }}"></div>
	</div>

	<div class="status">..</div>
</div>