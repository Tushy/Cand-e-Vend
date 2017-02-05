/*****************************************************/
/*************** AJAX functionaliteit ****************/
/*****************************************************/

var ajax = {
	post : function(queryString, file, load, callback) {
		// Als een laadscherm moet worden laten zien.
		if (load == true){
			loader(document.body, true);
		};

		var xhr;

		if(typeof XMLHttpRequest !== 'undefined') {
			xhr = new XMLHttpRequest();
		} else {
			var versions = ["MSXML2.XmlHttp.5.0", 
							"MSXML2.XmlHttp.4.0",
							"MSXML2.XmlHttp.3.0", 
							"MSXML2.XmlHttp.2.0",
							"Microsoft.XmlHttp"]

			 for(var i = 0, len = versions.length; i < len; i++) {
				try {
					xhr = new ActiveXObject(versions[i]);
					break;
				} catch(e){};
			 };
		};
		 
		xhr.onreadystatechange = ensureReadiness;

		function ensureReadiness() {
			if(xhr.readyState < 4) {
				return;
			};
			 
			if(xhr.status !== 200) {
				return;
			};

			// Als alles is goedgegaan.
			if(xhr.readyState === 4) {
				callback(xhr);
				if (load == true){
					loader(document.body, false);
				};
			};
		};

		// Haalt het PHP bestand op met de query string informatie.
		xhr.open("POST", file + ".php", true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(encodeURI(queryString));
	},
	get : function(file, load, callback) {
		// Als een laadscherm moet worden laten zien.
		if (load == true){
			loader(document.body, true);
		};

		var xhr;

		if(typeof XMLHttpRequest !== 'undefined') {
			xhr = new XMLHttpRequest();
		} else {
			var versions = ["MSXML2.XmlHttp.5.0", 
							"MSXML2.XmlHttp.4.0",
							"MSXML2.XmlHttp.3.0", 
							"MSXML2.XmlHttp.2.0",
							"Microsoft.XmlHttp"]

			 for(var i = 0, len = versions.length; i < len; i++) {
				try {
					xhr = new ActiveXObject(versions[i]);
					break;
				} catch(e){};
			 };
		};
		 
		xhr.onreadystatechange = ensureReadiness;
		 
		function ensureReadiness() {
			if(xhr.readyState < 4) {
				return;
			};
			 
			if(xhr.status !== 200) {
				return;
			};

			// Als alles is goedgegaan.
			if(xhr.readyState === 4) {
				callback(xhr);
				if (load == true){
					loader(document.body, false);
				};
			};
		};

		// Voert een het PHP bestand uit.
		xhr.open("GET", file + ".php", true);
		xhr.send();
	}
};