
// device extensions
var emuino = {
	exts: {}
};

emuino.exts.Arduino = function($elem, guid, args) {
	
	console.log('init an arduino - $elem, guid, args: ', $elem, guid, args);
	$elem.html('An Arduino info here..');
	
	// devices loops
	setInterval(function(){
		console.log('arduino device is working in loop.. giud:', guid);
	}, 400);
};

// TODO: add more extension here or load dynamically



emuino.init = function() {
	var devices = [];
	
	var dndCounter = 0;
	var dndZIndexMax = 0;
	this.make = function(name, guid, args) {
		if(!args) args = {};
		dndCounter++;
		var dndNextID = 'dnd-'+dndCounter+'-'+name+'-'+guid;
		$('.dnd-container').append(
			'<div id="'+dndNextID+'" class="dnd-box">'+
			'	<div class="dnd-title">'+name+' (guid:'+guid+')</div>'+
			'	<div id="'+dndNextID+'-contents" class="dnd-contents"></div>'+
			'</div>'
		);		
		$('#'+dndNextID).draggable({
			handle: '.dnd-title'
		});
		$('#'+dndNextID+' .dnd-title').disableSelection();
		$('#'+dndNextID).resizable();
		$('#'+dndNextID).css({
			top: ((dndCounter%10)*30) + 'px',
			left: ((dndCounter%10)*30) + 'px',
			'z-index': (++dndZIndexMax)
		});
		$('#'+dndNextID).mousedown(function(){			
			$(this).css('z-index', (++dndZIndexMax));
		});
		var device = new emuino.exts[name]($('#'+dndNextID+'-contents'), guid, args);
		devices.push(device);
	};
	

	console.log('Emuino client started');

	var ws = new WebSocket('ws://127.0.0.1:8080/');

	ws.onmessage = function(event) {
		console.log(event);
		//document.getElementById('msgBox').innerHTML = event.data;
		//document.getElementById('outMsg').value='';
		eval(event.data);
	}

	function send()
	{
		// todo...
		ws.send(document.getElementById('outMsg').value);
	}	
};

$(function(){	
	emuino.init();
});