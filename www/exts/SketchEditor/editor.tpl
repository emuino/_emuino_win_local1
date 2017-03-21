<div class="sketch">

	<div class="menu">
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnNewClick(this);">New</button>
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnOpenClick(this);">Open..</button>
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnSaveClick(this);">Save</button>
		<button onclick="emuino.getDevices().SketchEditor[{{id}}].btnSaveAsClick(this);">Save as..</button>
		<span class="saved"></span><span class="fname"></span>
	</div>

	<div class="frame">
<div class="editor" id="editor-{{ id }}">
// TODO: add wiki: .ino code highlight in Dev-C++ settings at Tools -> Editor Option -> Colors -> bottom of window..

// the setup function runs once when you press reset or power the board
void setup() {
  // initialize digital pin LED_BUILTIN as an output.
  pinMode(LED_BUILTIN, OUTPUT);
}

// the loop function runs over and over again forever
void loop() {
  digitalWrite(LED_BUILTIN, HIGH);   // turn the LED on (HIGH is the voltage level)
  delay(1000);                       // wait for a second
  digitalWrite(LED_BUILTIN, LOW);    // turn the LED off by making the voltage LOW
  delay(1000);                       // wait for a second
}
</div>
	</div>

	<div class="status">..</div>
</div>