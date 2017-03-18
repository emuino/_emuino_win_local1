var	tpl = {
	cfg: {
		prefix: '{{\\s*',
		suffix: '\\s*}}',
	},
	replace: function (str, from, to) {
		return str.split(from).join(to);	
	},
	parseStr: function (str, data) {	

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

		str = eval('(function(){'+vardefs+' var __tpl_output = "'+str+'"; return __tpl_output;})');
		
		return str;
	},
	cacheUrl: {},
	parseUrl: function(url, data, callback) {
		if(typeof this.cacheUrl[url] == 'undefined') {
			var _this = this;
			var _url = url;
			var _data = data;
			var _callback = callback;
			$.get(url, function(resp){
				_this.cacheUrl[_url] = resp;
				_this.parseUrl(_url, _data, function(results){
					_callback(results)
				});
			});
		} else {
			callback(this.parseStr(this.cacheUrl[url], data));
		}
	}
};


var dialog = function(title, tplUrl, tplData, settings) {
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
		$('#preloader').fadeIn();
		preloader.counter++;
		
		if(typeof msg != 'undefined') {
			preloader.msg(msg);
		}
	},
	off: function() {
		preloader.counter--;
		if(preloader.counter < 0) {
			throw "tried to close a preloader but there is no more open..";
		}
		if(preloader.counter == 0) {
			$('#preloader').hide();
		}
	},
	get: function(url, cb) {
		preloader.on();
		$.get(url, function(resp){
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

emuino.exts.Arduino = function($elem, guid, args) {
	
	var pinModes = [];
	var pins = [];
	
	
	this.refresh = function() {
		//var html = '';
		tpl.parseUrl('exts/Arduino/pins.tpl', {
			'values': pins,
			'modes': pinModes
		}, function(html){			
			// for(var k in pins) {
				// html+= '<div class="arduino-pin"><span class="pin-no">'+k+'</span><span class="pin-mode">'+pinModes[k]+'</span><span class="pin-value">'+pins[k]+'</span></div>';
			// }
			$elem.html(html);
		});
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
	this.statmsg = function(msg) {
		$('.emu-stat').html(msg);
	};
	
	emuino.statmsg('Emuino client started, initialize..');
	
	preloader.on('initialize..');	
	
	var ws = new WebSocket('ws://127.0.0.1:8080/');
	
	ws.onerror = function(event) {
		emuino.statmsg('websocket error');
		alert('Connection to WebSocekt server faild, please run wsd.bat');
		document.location.href = document.location.href;
	};
	
	ws.onmessage = function(event) {
		if(event.data) {
			emuino.statmsg("received: "+event.data);
			eval(event.data);
		}
	};
	
	ws.onopen = function(event) {
		preloader.off();
		emuino.statmsg("wsd connected.");
	}
	
	

	
	
	var devices = {};
	this.getDevices = function(){
		return devices;
	};
	
	var dndCounter = 0;
	var dndZIndexMax = 0;
	this.make = function(name, guid, args) {
		if(!args) args = {};
		dndCounter++;
		var dndNextID = 'dnd-'+name+'-'+guid;
		if($('#'+dndNextID).length>0) {
			throw "DOM element already exists: #"+name+"-"+guid;
		}
		
		tpl.parseUrl('tpls/dnd-box.tpl', {
			'dndNextID' : dndNextID,
			'name': name,
			'guid': guid,
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
			var device = new emuino.exts[name]($('#'+dndNextID+'-contents'), guid, args);
			if(!devices[name]) {
				devices[name] = [];
			}
			if(devices[name][guid]) {
				throw "device already exists: "+name+" giud="+guid;
			}
			devices[name][guid] = device;
			console.log(devices);
		});
	};
	
	this.remove = function(name, guid) {
		$('#dnd-'+name+'-'+guid).remove();
		devices[name][guid] = null;
		delete devices[name][guid];
		console.log(devices);
	};
	


	
	this.start = function() {
		dialog('Start', 'tpls/start.tpl', {}, {
			modal: true,
			buttons: {
				'Load': function() {
					emuino.loadArduino($('[name=device]').val(), $('[name=skatch]').val());
					$(this).dialog( "close" );
				},
				'Cancel': function() {
					$(this).dialog( "close" );
				}
			}
		});
	};
	
	this.createSkatch = function(fname) {
		if(fname.substr(-4)!='.ino') {
			alert('File extension should be a ".ino"!');
			return;
		}
		preloader.get('createSkatch.php?fname='+fname);
	};
	
	this.loadArduino = function(deviceType, skatchFileName) {
		preloader.get('rebuildSkatch.php?device='+deviceType+'&fname='+skatchFileName);
		alert('TODO: load Arduino (rebuild cpp source with skatch.ino) and run: '+deviceType+', '+skatchFileName);
	};
	
	

	function send()
	{
		// todo...
		ws.send(document.getElementById('outMsg').value);
	}	
};

$(function(){	
	emuino.init();
});