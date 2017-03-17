<span>Pins:</span>
{{# k in values }}
<div class="arduino-pin">
	<span class="pin-no">{{ k }}</span>
	<span class="pin-mode">{{ modes [ k ] }}</span>
	<span class="pin-value">{{ values [ k ] }}</span>
</div>
{{#}}
-----