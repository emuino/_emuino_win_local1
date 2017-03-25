var ajax = {
	getNoCache: function() {
		var $script = $('head script[src$="emuino.js?_nocache="]');
		return $script.lenght > 0 ? $script.attr('src').split('emuino.js?_nocache=')[1] : null;
	},
	getUrl: function(url) {
		if(ajax.getNoCache() && url.split(nocacheArg).lenght>1) {
			var nocacheArg = '_ajax_nocache=';
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
		var cacheNeed = url!=url;
		if(url==url || typeof this.cacheUrl[url] === 'undefined') {
			var _this = this;
			var _url = url;
			var _data = data;
			var _callback = callback;
			//emuino.preloader.on('loading..');
			ajax.get(url, function(resp){
				//emuino.preloader.off();
				if(cacheNeed) {
					_this.cacheUrl[_url] = resp;
					_this.parseUrl(_url, _data, function(results){
						_callback(results)
					});
				}
				else {
					_callback(_this.parseStr(resp, _data));
				}
			});
		} else {
			callback(this.parseStr(this.cacheUrl[url], data));
		}
	},
};


var	loadStyle = function(url, cb) {
	if(typeof cb === 'undefined' || !cb) cb = function(){}; // todo
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
	if(typeof cb === 'undefined' || !cb) cb = function(){};
	var found = false;
	$('script').each(function(i,e){
		if(!found && $(e).attr('src') == url) {
			found = true;
			return;
		}
	});
	if(!found) {
		$('head').append('<script src="'+url+'" type="text/javascript" charset="utf-8" onload="'+cb+'"></script>');
	} else {
		cb();
	}
};


var dialog = function(title, tplUrl, tplData, settings) {
	if(typeof settings === 'undefined' || !settings) {
		settings = {
			buttons: {
				Close: function() {
					$(this).dialog( "close" );
				}
			}
		}
	}
	if(typeof settings.title === 'undefined') settings.title = title;
	if(typeof settings.modal === 'undefined') settings.modal = true;
	if(!$('#msgbox').length) {
		$('body').append('<div id="msgbox" style="display: none;"></div>');
	}
	$('#msgbox').attr('title', title);
	tpl.parseUrl(tplUrl, tplData, function(html){
		$('#msgbox').html(html);
		$('#msgbox select').selectmenu();
		$('#msgbox .autocomplete').each(function(i,e){
			$(e).autocomplete({
				minLength: 0,
				source: emuino.preloader.getCallURL($(e).attr('data-source'), []),
			}).focus(function(){     
				//Use the below line instead of triggering keydown
				 $(this).autocomplete("search");
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
			minLength: 0,
			source: $(e).attr('data-source')
		}).focus(function(){     
            //Use the below line instead of triggering keydown
             $(this).autocomplete("search");
        });;
	});
	$('#msgbox').dialog(settings);
};








var emuino = {
	// device extensions
	exts: {},
	preloader: {
		counter: 0,
		msg: function(msg) {
			if(emuino.preloader.counter<=0) {
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
			emuino.preloader.counter++;
			
			if(typeof msg != 'undefined') {
				emuino.preloader.msg(msg);
			}
		},
		off: function() {
			emuino.preloader.msg('');
			emuino.preloader.counter--;
			if(emuino.preloader.counter < 0) {
				throw "tried to close a preloader but there is no more open..";
			}
			if(emuino.preloader.counter == 0) {
				$('#preloader').hide();
			}
		},
		get: function(url, cb, msg, nocache) {
			if(typeof nocache === 'undefined') nocache = true;
			if(typeof msg === 'undefined') msg = "Loading..";
			emuino.statmsg('loading url: '+url);
			emuino.preloader.on(msg);
			ajax.get(url+(nocache ? (url.split('?').length>1 ? '&' : '?') + 't='+Math.random() : ''), function(resp){
				emuino.statmsg(msg+' (OK)');
				emuino.preloader.off();
				if(cb) {
					cb(resp);
				} else if(resp) {
					console.error("no callback defined for response", resp);					
					alert("response: "+resp+"\n see console for more info..");
				}
			});
		},
		//getNoCache: function(url, cb, msg, nocache) {
		//	emuino.preloader.get(url, cb, msg, true);
		//},
		
		getCallURL: function(func, args) {
			if(typeof args === undefined) args = [''];
			
			var url = "emuino.php?func="+func;
			for(k in args) {
				url += "&args["+k+"]="+args[k];
			}
			
			return url;
		},
		
		getJSON: function(url, cb, msg) {
			emuino.preloader.get(url, function(resp){
				var obj;
				var ok = false;
				try {
					obj = JSON.parse(resp);
					ok = true;
				} catch (e) {
					console.error("invalid json: "+ resp, e);
				}
				if(ok) {
					cb(obj);
				}
			}, msg);
		},
		
		call: function(func, args, cb) {
			var url = emuino.preloader.getCallURL(func, args);
			
			emuino.preloader.getJSON(url, function(resp){
				if(resp.status=='E' && !cb) {
					console.error(resp);
					if(resp.message) {
						alert(resp.message + "\nmore details in console");
					}
					else {
						alert('Error: more details in console');
					}
				}
				if(cb) {
					cb(resp);
				}
			});
		},
	},
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
			$('.arduino-pin[data-pin='+pin+'] .pin-value').html(value);	
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
		$pin = $(e).closest('.arduino-pin');
		$switch = $pin.find('input[name="switch"]');
		if(!$switch.is(':checked')) {
			$pin.find('input[name="vold"]').val(parseInt($pin.find('.pin-value').html()));
		}
		emuino.send('sendPinValue', [id, $pin.attr('data-pin'), $pin.find('input[name="vset"]').val()]);
	};
	
	this.btnPinValueSetterMouseUp = function(e) {
		$pin = $(e).closest('.arduino-pin');
		$switch = $pin.find('input[name="switch"]');
		if(!$switch.is(':checked')) {
			emuino.send('sendPinValue', [id, $pin.attr('data-pin'), $pin.find('input[name="vold"]').val()]);
		}
	};
	
	loadStyle('exts/Arduino/arduino.css');
	
	$elem.html('an Arduino loading..');
	
	// event handlers
	
	this.onClose = function(cb) {
		// TODO close server side also!
		alert('TODO: close emu server side also!');
		cb();
	};
};


emuino.exts.SketchEditor = function($elem, id, args) {
	
	// todo.. I think it should be removed
	//var status = function(msg) {
	//	$elem.find('.status').html(msg);
	//};
	
	$elem.html('a Sketch Editor loading..');
	
	loadStyle('exts/SketchEditor/editor.css');
	
	var filesaved = false;
	var filename = "";
	var editor;

	
	var setFileName = function(fn) {
		filename = fn;
		$elem.find('.fname').html(fn);
	};
	
	
	var getFileName = function() {
		return filename;
	};

	var setFileSaved = function(saved) {
		filesaved = saved;		
		$elem.find('.saved').html(saved ? '' : '*');
	};
	
	
	var saveSketch = function(fn, cb) {
		if(typeof cb === 'undefined') cb = false;
		emuino.preloader.on('Save..');
		$.post('savesketch.php?f='+fn, {data: editor.getSession().getValue()}, function(resp){
			emuino.preloader.off();
			if(resp != 'OK') {
				saveAsSketch(function(){
					cb();
				});	
			}
			else {
				setFileName(fn);
				setFileSaved(true);
				if(cb) {
					cb();
				}
			}
		});
	};
	
	var saveAsSketch = function(cb) {
		var _this = this;
		dialog('Save as..', 'exts/SketchEditor/saveas.tpl', {}, {
			buttons: {
				Save: function(){
					var fn = $('input[name="sketch"]').val();
					saveSketch(fn, function(){
						cb(fn);
					});
					$(this).dialog('close');
				}
			}
		});
	};
	
	var newSketch = function() {
		setFileName("");
		setFileSaved(false);
		editor.setValue("void setup() {\n\n}\n\nvoid loop() {\n\n}\n", -1);
	};

	// ui event handlers

	this.onClose = function(cb) {		
		if(!filesaved) {
			if(confirm("File is not saved. Do you want to save it now?")) {
				saveSketch(getFileName(), function(){
					cb();
				});
			}
			else {
				cb();
			}
		}
		else {
			cb();
		}
	}
	
	// user events
	
	this.btnNewClick = function() {
		if(confirm('All change will be lost, are you sure?')) {
			newSketch();
		}
	};
	
	this.btnOpenClick = function() {
		dialog('Open..', 'exts/SketchEditor/open.tpl', {}, {
			buttons: {
				Load: function(e) {
					var _this = this;
					var sfname = $('input[name="sketch"]').val();
					emuino.preloader.call('getSketchExists', [sfname], function(resp){						
						emuino.preloader.call('getSketchContents', [sfname], function(resp){
							setFileName(sfname);
							editor.setValue(resp.message, -1);
							setFileSaved(true);
							$(_this).dialog('close');
						}, 'Loading sketch file..');
					}, 'Check sketch file..');
				}
			}
		});
	};

	
	this.btnSaveClick = function(e) {
		var fn = getFileName();
		saveSketch(fn);
	};

	this.btnSaveAsClick = function() {
		saveAsSketch();
	};
	
	this.btnRunClick = function() {
		var sketchf = getFileName();		
		if(filesaved) {
			emuino.start(sketchf);
		} else {
			if(sketchf) {
				saveSketch(sketchf, function(){
					emuino.start(sketchf);
				});
			} else {
				saveAsSketch(function(fn){
					emuino.start(fn);
				});
			}
		}
	};

	// start.. 
	
	tpl.parseUrl('exts/SketchEditor/editor.tpl', {
		'id': id,
	}, function(html){
		$elem.html(html);
		
		editor = ace.edit("editor-"+id);
		editor.$blockScrolling = Infinity
		editor.setTheme("ace/theme/monokai");
		editor.getSession().setMode("ace/mode/c_cpp");
		editor.setAutoScrollEditorIntoView(true);
		editor.getSession().on('change', function(e) {
			setFileSaved(false);
		});
		editor.commands.addCommand({
			name: 'save',
			bindKey: {win: "Ctrl-S", "mac": "Cmd-S"},
			exec: function(editor) {
				var fn = $elem.find('.fname').html();
				if(fn) {
					saveSketch(fn);
				}
				else {
					saveAsSketch();
				}
			}
		});
		
		//status('');
		
		// ace editor scroll and positioning fix
		var aceposfix = function() {
			$('.dnd-contents').each(function(){
			  $(this).css('height', $(this).parent().height() - 38);
			});			
			editor.resize();
		};
		
		$elem.closest('.dnd-box').css('width', '500px');
		$elem.closest('.dnd-box').css('height', '350px');
		$elem.closest('.dnd-box').mouseup(function(){
			aceposfix();
		});	
		aceposfix();

		// start point
		newSketch();
		
	});
	
};

emuino.exts.MessageBox = function($elem, id, args) {
	
	loadStyle('exts/MessageBox/messagebox.css');
	
	tpl.parseUrl('exts/MessageBox/messagebox.tpl', args, function(output){
		$elem.html(output);
	});
	
};

// TODO: add more extension here or load dynamically



emuino.init = function() {
	
	

	
	this.statmsg = function(msg) {
		$('.emu-stat').html(msg);
	};
	
	emuino.statmsg('Emuino client started, initialize..');
	
	emuino.preloader.on('loading..');	
	
	var ws = new WebSocket('ws://127.0.0.1:8080/');
	
	ws.onerror = function(event) {
		emuino.statmsg('websocket error');
		emuino.wsdRestart();
	};
	
	this.wsdRestart = function() {
		emuino.statmsg('WSD Restart: stop..');
		
		//emuino.preloader.get('runbat.php?f=wsdstop.bat', function(){		
		emuino.preloader.call('doWsdStop', [], function(){
			
			emuino.statmsg('WSD Restart: start..');
			
			//emuino.preloader.get('runbat.php?f=wsdstart.bat', function(){
			emuino.preloader.call('doWsdStart', [], function(){
							
			}, 'Lost connection to WebSocket server, please wait to reconnect..<br>WSD Restart: start..');
			emuino.statmsg('WSD Restart: waiting for reconnect..');
			
			msgbox('Refres...', 'Connection refresh soon.. if the page doesn\'t refresh automatically please refresh it...', {buttons:{}});
			setTimeout(function(){
				document.location.href = document.location.href;
			}, 4000);
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
	};
	
	ws.onmessage = function(event) {		
		if(event.data) {
			emuino.statmsg("received: "+event.data);
			handleWsdCmd(event.data);
		}
	};
	
	ws.onopen = function(event) {
		emuino.preloader.off();
		emuino.statmsg("wsd connected.");
		//emuino.start();
	};
	
	
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
		if(typeof id === 'undefined' || !id) id = parseInt(Math.random()*10000000);
		if(typeof args === 'undefined' || !args) args = {};
		dndCounter++;
		var dndNextID = 'dnd-'+name+'-'+id;
		if($('#'+dndNextID).length>0) {
			throw "DOM element already exists: #"+name+"-"+id;
		}
		
		// todo add close button to dnd-boxes header
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
				},
				grid: [10,10],
			});
			$('#'+dndNextID+' .dnd-title').disableSelection();
			$('#'+dndNextID).resizable({
				grid: 10,
			});
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
		});
	};
	
	this.remove = function(name, id) {
		var wait = true;
		
		// it has a close handler?
		if(typeof devices[name][id].onClose !== 'undefined') {
			// send close request
			devices[name][id].onClose(function(){
				wait = false;
			});			
		}
		else {
			wait = false;
		}
				
		var removeDOM = function() {
			$('#dnd-'+name+'-'+id).remove();
			devices[name][id] = null;
			delete devices[name][id];
		};
		
		var waiting = function() {
			if(!wait) {
				removeDOM();
			}
			else {
				setTimeout(function(){
					// is still in the dom?
					if($('#dnd-'+name+'-'+id).length>0 && wait) {
						if(confirm("Still waiting but "+name+"#"+id+" task doesn't response for close request. Force it to close?")) {
							wait = false;
						}
						waiting();
					}
				}, 30000);
			}
		};
		
		waiting();
		
		
	};
	


	
	this.start = function(sketchf) {
		if(typeof sketchf === 'undefined' || sketchf === null) sketchf = 'sketch.ino'; 
		dialog('Start', 'tpls/start.tpl', {
			'sketchf': sketchf
		}, {
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
		emuino.preloader.get('create.php?fname='+fname, null, 'Create sketch file: '+fname);
	};
	
	this.loadArduino = function(device, sketch) {
		
		emuino.preloader.call('doRunArduino', [device, sketch], function(resp){
			if(resp.status == 'E') {
				emuino.make('MessageBox', null, resp);
			}
		});
		
		// todo watch the file time... ??
	};
	
	this.close = function() {
		
		//emuino.preloader.get('runbat.php?f=stop.bat', function() {  
		emuino.preloader.call('doStopServer', [], function() {  
		
		}, 'Server shut down..');
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