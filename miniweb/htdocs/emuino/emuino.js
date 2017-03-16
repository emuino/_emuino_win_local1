
// device extensions
var emuino = {
	exts: {}
};

emuino.exts.Arduino = function($elem, guid, args) {
	
	var pinModes = [];
	var pins = [];
	
	
	this.refresh = function() {
		var html = '';
		for(var k in pins) {
			html+= '<div class="arduino-pin"><span class="pin-no">'+k+'</span><span class="pin-mode">'+pinModes[k]+'</span><span class="pin-value">'+pins[k]+'</span></div>';
		}
		$elem.html(html);
	};
	
	this.setPinMode = function(pin, value) {
		pinModes[pin] = value;
		this.refresh();
	};
	
	this.setPin = function(pin, value) {
		pins[pin] = value;
		this.refresh();
	};
	
	console.log('init an arduino - $elem, guid, args: ', $elem, guid, args);
	$elem.html('an Arduino loaded..');
	
	
	
	// devices loops
	//setInterval(function(){
	//	console.log('arduino device is working in loop.. giud:', guid);
	//}, 400);	
};

// TODO: add more extension here or load dynamically



emuino.init = function() {
	var devices = {};
	
	var dndCounter = 0;
	var dndZIndexMax = 0;
	this.make = function(name, guid, args) {
		if(!args) args = {};
		dndCounter++;
		var dndNextID = 'dnd-'+name+'-'+guid;
		if($('#'+dndNextID).length>0) {
			throw "DOM element already exists: #"+name+"-"+guid;
		}
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
		if(!devices[name]) {
			devices[name] = [];
		}
		if(devices[name][guid]) {
			throw "device already exists: "+name+" giud="+guid;
		}
		devices[name][guid] = device;
		console.log(devices);
	};
	
	this.remove = function(name, guid) {
		$('#dnd-'+name+'-'+guid).remove();
		devices[name][guid] = null;
		delete devices[name][guid];
		console.log(devices);
	};
	

	console.log('Emuino client started');

	var ws = new WebSocket('ws://127.0.0.1:8080/');

	ws.onmessage = function(event) {
		console.log("received:", event.data);
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