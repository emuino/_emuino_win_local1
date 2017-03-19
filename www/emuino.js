var ajax = {
	getNoCache: function() {
		var $script = $('head script[src$="emuino.js?_nocache="]');
		return $script.lenght > 0 ? $script.attr('src').split('emuino.js?_nocache=')[1] : null;
	},
	getUrl: function(url) {
		var nocacheArg = '_ajax_nocache=';
		if(ajax.getNoCache() && url.split(nocacheArg).lenght>1) {
			if(url.split('?').length>1) {
				url += '&';
			} else {
				url += '?';
			}
			url += nocacheArg+parseInt(Math.random()*100000);
		}
		return url;
	},
	get: function(url, a,b,c) {
		url = ajax.getUrl(url);
		$.get(url,a,b,c);
	},
	// TODO POST..
};

var	tpl = {
	cfg: {
		prefix: '{{\\s*',
		suffix: '\\s*}}',
	},
	replace: function (str, from, to) {
		return str.split(from).join(to);	
	},
	parseStr: function (str, data) {
		
		var inp = str;

		var rep = function(str, pattern, replacement) {
			var finish = false;
			while(!finish) {
				finish = true;
				var regex = new RegExp(tpl.cfg.prefix+pattern+tpl.cfg.suffix);
				var match = str.match(regex);
				if(match) {
					str = tpl.replace(str, match[0], tpl.replace(replacement, '{match}', match[1]));
					finish = false;
				}
			}
			return str;
		};

		// escape
		str = tpl.replace(str, '\r', '\\r');
		str = tpl.replace(str, '\n', '\\n');
		str = tpl.replace(str, '"', '\\"');		
		
		// replace loops
		str = rep(str, '#\\s*([^}]+)', '"; for({match}){ __tpl_output += "');

		// replace if
		str = rep(str, '\\?\\s*([^}]+)', '"; if({match}){ __tpl_output += "');

		// replace else
		str = rep(str, ':', '"; } else { __tpl_output += "');

		// replace end sequences
		str = rep(str, '[#?/]', '"; } __tpl_output += "');

		// replace variables
		str = rep(str, '([^}]+)', '"+{match}+"');

		var vardefs = '';
		for(k in data) {
			vardefs += k+' = '+JSON.stringify(data[k])+";";
		}

		try {
			str = eval('(function(){'+vardefs+' var __tpl_output = "'+str+'"; return __tpl_output;})');
		} catch(e) {
			console.log(inp);
			console.log(str);
			console.error(e);
			return inp;
		}
		
		return str;
	},
	cacheUrl: {},
	parseUrl: function(url, data, callback) {
		url = ajax.getUrl(url);
		if(typeof this.cacheUrl[url] == 'undefined') {
			var _this = this;
			var _url = url;
			var _data = data;
			var _callback = callback;
			ajax.get(url, function(resp){
				_this.cacheUrl[_url] = resp;
				_this.parseUrl(_url, _data, function(results){
					_callback(results)
				});
			});
		} else {
			callback(this.parseStr(this.cacheUrl[url], data));
		}
	},
};


var	loadStyle = function(url, cb) {
	var found = false;
	$('link').each(function(i,e){
		if(!found && $(e).attr('href') == url) {
			found = true;
			return;
		}
	});
	if(!found) {
		$('head').append('<link rel="stylesheet" type="text/css" href="'+url+'">');
	}
};

var	loadScript = function(url, cb) {
	var found = false;
	$('script').each(function(i,e){
		if(!found && $(e).attr('src') == url) {
			found = true;
			return;
		}
	});
	if(!found) {
		$('head').append('<script src="'+url+'"></script>');
	}
};


var dialog = function(title, tplUrl, tplData, settings) {
	if(typeof settings == 'undefined' || !settings) {
		settings = {
			buttons: {
				Close: function() {
					$(this).dialog( "close" );
				}
			}
		}
	}
	if(!$('#msgbox').length) {
		$('body').append('<div id="msgbox" style="display: none;"></div>');
	}
	$('#msgbox').attr('title', title);
	tpl.parseUrl(tplUrl, tplData, function(html){
		$('#msgbox').html(html);
		$('#msgbox select').selectmenu();
		$('#msgbox .autocomplete').each(function(i,e){
			$(e).autocomplete({
				source: $(e).attr('data-source')
			});
		});
		$('#msgbox').dialog(settings);
	});
	
};



// TODO refact msgbox and dialog functions
var msgbox = function(title, msg, settings) {
	if(typeof settings == 'undefined' || !settings) {
		settings = {
			buttons: {
				Close: function() {
					$(this).dialog( "close" );
				}
			}
		}
	}
	if(!$('#msgbox').length) {
		$('body').append('<div id="msgbox" style="display: none;"></div>');
	}
	$('#msgbox').attr('title', title);
	$('#msgbox').html(msg);
	$('#msgbox select').selectmenu();
	$('#msgbox .autocomplete').each(function(i,e){
		$(e).autocomplete({
			source: $(e).attr('data-source')
		});
	});
	$('#msgbox').dialog(settings);
};


var preloader = {
	counter: 0,
	msg: function(msg) {
		if(preloader.counter<=0) {
			throw "tried to show a preloader message but there is no any preloader open";
		}
		$('#preloader .stat').html(msg);
	},
	on: function(msg) {
		if(!$('#preloader').length) {
			$('body').append('<div id="preloader"><div class="stat"></div></div>');
		}
		$('#preloader').css('z-index', $('#msgbox').css('z-index') + 1);
		$('#preloader').show();
		preloader.counter++;
		
		if(typeof msg != 'undefined') {
			preloader.msg(msg);
		}
	},
	off: function() {
		preloader.msg('');
		preloader.counter--;
		if(preloader.counter < 0) {
			throw "tried to close a preloader but there is no more open..";
		}
		if(preloader.counter == 0) {
			$('#preloader').hide();
		}
	},
	get: function(url, cb, msg) {
		emuino.statmsg('loading url: '+url);
		preloader.on(msg);
		ajax.get(url, function(resp){
			emuino.statmsg('OK');
			preloader.off();
			if(cb) {
				cb(resp);
			} else if(resp) {
				alert(resp);
			}
		});
	},
};





var emuino = {
	// device extensions
	exts: {},
};

emuino.exts.Arduino = function($elem, id, args) {

	var pins = [];
	
	var emptyPin = {
		'name': '',
		'mode': 0,
		'value': 0,
	};
	
	this.refreshPins = function(cb) {
		tpl.parseUrl('exts/Arduino/pins.tpl', {
			'id': id,
			'pins': pins,
		}, function(html){
			$elem.html(html);
			if(typeof cb !== 'undefined') {
				cb();
			}
		});
	};
	
	this.setPinMode = function(pin, mode) {
		var setMode = function(pin, mode){			
			pins[pin]['mode'] = mode;
			$('.arduino-pin.no-'+pin+' .pin-mode').html(mode);
		};
		if(typeof pins[pin] == 'undefined') {
			pins[pin] = emptyPin;
			this.refreshPins(function(){
				setMode(pin, mode);
			});
		} else {
			setMode(pin, mode);
		}
	};
	
	this.setPinValue = function(pin, value) {
		var setValue = function(pin, value) {		
			pins[pin]['value'] = value;
			$('.arduino-pin.no-'+pin+' .pin-value').html(value);	
		};
		if(typeof pins[pin] == 'undefined') {
			pins[pin] = emptyPin;
			this.refreshPins(function(){
				setValue(pin, value);
			});
		} else {
			setValue(pin, value);
		}
	};
	
	this.btnPinValueSetterMouseDown = function(e) {
		console.log(e);
		$pin = $(e).closest('.arduino-pin');
		$pin.find('input[name=\"vset\"]').val($pin.find('input[name=\"vold\"]').val());
		emuino.send('sendPinValue', [id, $pin.attr('data-pin'), $pin.find('input[name=\"vset\"]').val()]);
	};
	
	this.btnPinValueSetterMouseUp = function(e) {
		console.log(e);
	};
	
	console.log('init an arduino - $elem, id, args: ', $elem, id, args);
	
	loadStyle('exts/Arduino/arduino.css');
	
	$elem.html('an Arduino loading..');
	
	
};

// TODO: add more extension here or load dynamically



emuino.init = function() {
	
	
	
	this.statmsg = function(msg) {
		$('.emu-stat').html(msg);
	};
	
	emuino.statmsg('Emuino client started, initialize..');
	
	preloader.on('loading..');	
	
	var ws = new WebSocket('ws://127.0.0.1:8080/');
	
	ws.onerror = function(event) {
		emuino.statmsg('websocket error');
		emuino.wsdRestart();
	};
	
	this.wsdRestart = function() {
		emuino.statmsg('WSD Restart: stop..');
		preloader.get('runbat.php?f=wsdstop.bat', function(){
			emuino.statmsg('WSD Restart: start..');
			preloader.get('runbat.php?f=wsdstart.bat', function(){
							
			}, 'Lost connection to WebSocket server, please wait to reconnect..<br>WSD Restart: start..');
			emuino.statmsg('WSD Restart: waiting for reconnect..');
			
			msgbox('Refres...', 'Connection refresh soon.. if the page doesn\'t refresh automatically please refresh it...', {buttons:{}});
			setTimeout(function(){
				document.location.href = document.location.href;
			}, 8000);
		}, 'Lost connection to WebSocket server, please wait to reconnect..<br>WSD Restart: stop..');
	};
	
	var handleWsdCmd = function(cmd) {
		try {
			eval(cmd);
		} catch(e) {
			// console.log('stucked command: '+cmd+', full exception: ', e);
			// console.log('try to run cmd after 1 sec again: '+cmd);
			var cmd = cmd;
			setTimeout(function(){
				handleWsdCmd(cmd);
			}, 1000);
		}
	}
	
	ws.onmessage = function(event) {		
		if(event.data) {
			emuino.statmsg("received: "+event.data);
			handleWsdCmd(event.data);
		}
	};
	
	ws.onopen = function(event) {
		preloader.off();
		emuino.statmsg("wsd connected.");
		emuino.start();
	}
	
	
	window.addEventListener("beforeunload", function (e) {	
		emuino.close();
	});
	//window.addEventListener("unload", function (e) {	
	//	emuino.close();
	//});
	//$(window).unload(function(){
	//	emuino.close();
	//});
	
	var devices = {};
	this.getDevices = function(){
		return devices;
	};
	
	var dndCounter = 0;
	var dndZIndexMax = 0;
	this.make = function(name, id, args) {
		if(!args) args = {};
		dndCounter++;
		var dndNextID = 'dnd-'+name+'-'+id;
		if($('#'+dndNextID).length>0) {
			throw "DOM element already exists: #"+name+"-"+id;
		}
		
		tpl.parseUrl('tpls/dnd-box.tpl', {
			'dndNextID' : dndNextID,
			'name': name,
			'id': id,
		}, function(results){
			$('.dnd-container').append(results);
			$('#'+dndNextID).draggable({
				handle: '.dnd-title',
				stop: function() {
					$('.dnd-box').each(function(i,e){
						if(parseInt($(e).css('top')) < 0) {
							$(e).css('top', '0px');
						}
					});
				}
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
			var device = new emuino.exts[name]($('#'+dndNextID+'-contents'), id, args);
			if(!devices[name]) {
				devices[name] = [];
			}
			if(devices[name][id]) {
				throw "device already exists: "+name+" giud="+id;
			}
			devices[name][id] = device;
			console.log(devices);
		});
	};
	
	this.remove = function(name, id) {
		$('#dnd-'+name+'-'+id).remove();
		devices[name][id] = null;
		delete devices[name][id];
		console.log(devices);
	};
	


	
	this.start = function() {
		dialog('Start', 'tpls/start.tpl', {}, {
			modal: true,
			buttons: {
				'Rebuild and Run': function() {
					emuino.loadArduino($('[name=device]').val(), $('[name=sketch]').val());
					$(this).dialog( "close" );
				},
				'Cancel': function() {
					$(this).dialog( "close" );
				}
			}
		});
	};
	
	this.create = function(fname) {
		if(fname.substr(-4)!='.ino') {
			alert('File extension should be a ".ino"!');
			return;
		}
		preloader.get('create.php?fname='+fname, null, 'Create sketch file: '+fname);
	};
	
	this.loadArduino = function(device, sketchf) {
		preloader.get('repair.php?device='+device+'&fname='+sketchf, function(){
			//var elemsAtStart = $('.dnd-container *').length;
			//msgbox('Rebuild and run', 'Please wait..');
			preloader.get('runbat.php?f=rebuild.bat', function(){
				preloader.get('run.php'/*'runbat.php?f=run.bat'*/, function(){ }, 'Run virtual device..');
			}, 'Rebuild source files and make virtual device..');
			//window.open('download.php?fname=rebuild_run.bat');
			// var inter = setInterval(function(){
				// if(elemsAtStart != $('.dnd-container *').length) {
					// clearInterval(inter);
					// $('#msgbox').dialog('close');
				// }
			// }, 300);
			// todo watch the file time...
		}, 'Repair source files..');
		//alert('TODO: load Arduino (rebuild cpp source with sketch.ino) and run: '+deviceType+', '+sketchFileName);
	};
	
	this.close = function() {
		preloader.get('runbat.php?f=stop.bat', function() {  }, 'Server shut down..');
		setTimeout(function(){
			$('html').html('Connection closed, <button onclick="window.open(\"\", \"_self\").close();">click here to close</button> this browser tab<br>bye..');
			window.open("", "_self").close();
		}, 5000);
	};
	
	

	this.send = function(cmd, args)	{
		var cmdMap = {
			sendPinValue: 0
		};
		// todo...
		//ws.send(document.getElementById('outMsg').value);
		c = cmdMap[cmd] + ',' + args.join(',');
		emuino.statmsg('send command "'+cmd+'": '+c);
		ws.send(c);
	}	
};

$(function(){	
	emuino.init();
});