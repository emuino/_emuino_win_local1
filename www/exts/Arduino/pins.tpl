<span>Pins:</span>
{{# var pin in pins }}
<div class="arduino-pin no-{{ pin }}">
	<span class="pin-no">{{ pin }}</span>
	<span class="pin-name">{{ pins[pin]['name'] }}</span>
	<span class="pin-mode">{{ pins[pin]['mode'] }}</span>
	<span class="pin-value">{{ pins[pin]['value'] }}</span>
	<span class="pin-btn"><input type="button" value="Send"></span>
	<span class="pin-swch"><input type="checkbox"></span>
	<span class="pin-inp"><input type="number" value="0"></span>
</div>
{{#}}
-----