
		console.log('Emuino client started');
		
		var ws = new WebSocket('ws://127.0.0.1:8080/');

		ws.onmessage = function(event) {
			console.log(event);
			document.getElementById('msgBox').innerHTML = event.data;
			document.getElementById('outMsg').value='';
		}

		function send()
		{
			ws.send(document.getElementById('outMsg').value);
		}		
