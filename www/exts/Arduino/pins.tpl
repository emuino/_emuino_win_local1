<span>Pins:</span>
<table>
	<tr>
		<td><span>No.</span></td>
		<td><span>Name</span></td>
		<td><span>Mode</span></td>
		<td><span>Value</span></td>
		<td><span></span></td>
	</tr>
	{{# var pin in pins }}
	<tr class="arduino-pin" data-pin="{{ pin }}">
		<td><span class="pin-no">{{ pin }}</span></td>
		<td><span class="pin-name">{{ pins[pin]['name'] }}</span></td>
		<td><span class="pin-mode">{{ pins[pin]['mode'] }}</span></td>
		<td><span class="pin-value">{{ pins[pin]['value'] }}</span></td>
		<td>
			<span class="pin-inp">
				<input type="number" name="vset" value="0">
				<input type="hidden" name="vold" value="{{ pins[pin]['value'] }}">
			</span>
			<span class="pin-btn">
				<input type="button" value="Set" 
					onmousedown="emuino.getDevices().Arduino[{{id}}].btnPinValueSetterMouseDown(this);" 
					onmouseup="emuino.getDevices().Arduino[{{id}}].btnPinValueSetterMouseUp(this);">
			</span>
			<span class="pin-swch"><input type="checkbox"></span>
		</td>
	</tr>
	{{#}}
</table>