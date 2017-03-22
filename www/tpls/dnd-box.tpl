<div id="{{ dndNextID }}" class="dnd-box">
	<div class="dnd-title">
		<span class="label">{{ name }}</span><span class="dbg">#{{ id }}</span>
		<span class="btns"><button onclick="emuino.remove('{{ name }}', {{ id }});">x</button></span>
	</div>
	<div id="{{ dndNextID }}-contents" class="dnd-contents"></div>
</div>