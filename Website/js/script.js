/****************************************/
/********* Globale variabelen ***********/
/****************************************/

var page_container = document.getElementsByTagName("MAIN")[0];

/*******************************************/
/********* Laad de pagina inhoud ***********/
/*******************************************/

// Loading animatie
function loader(container, x) {
	// Loader element
	var loader = container.getElementsByClassName('loader_container')[0];

	if (x == true){
		loader.style.cssText = 'opacity:1;display:block;pointer-events:all;';
	} else {
		loader.style.cssText = 'opacity:0;pointer-events:none;';
		setTimeout(function(){ 
			loader.style.display = 'none';
		}, 500);
	};
};

// Laad de pagina inhoud afhankelijk van de PHP sessie.
function loadContent() {

	// Controleert de actieve PHP sessies.
	ajax.get('php/session_check', false, function(xhr) {
	    if (xhr.responseText == '1') {

	    	// Redirect naar de product pagina.
			ajax.get('pages/products', true, function(xhr) {
				page_container.innerHTML = xhr.responseText;
				sort('sortProduct');
			});
	    } else {

			// Redirect naar de home pagina.
			ajax.get('pages/form', true, function(xhr) {
				page_container.innerHTML = xhr.responseText;
			});
	    };
	});
};

// Logout en redirect.
function logout(){

	// Vernietigt de huidige PHP sessie.
	ajax.get('php/logout', false, function(xhr) {});

	// Redirect naar de home pagina.
	ajax.get('pages/form', true, function(xhr) {
		page_container.innerHTML = xhr.responseText;
	});
};

/*********************************************/
/********* Interface functionaliteit *********/
/*********************************************/

// Tab functionaliteit
function activeTab(tab){

	// Haalt de tab buttons op.
	var tabMenu = document.getElementsByClassName("tab");

	// Haalt de container op van elk HTML formulier.
	var forms = document.getElementsByTagName('FORM');

	// Activeert de tab waar op gedrukt is.
	document.getElementById(tab.name).classList.add('showTab');

	// Verandert de kleur van de tab afhankelijk van de tab waarop gedrukt is.
	for (i = 0; i < tabMenu.length; i++) {
		if (tab == tabMenu[i]) {
			tabMenu[i].parentNode.classList.add('active');
		} else {
			tabMenu[i].parentNode.classList.remove('active');
		};
	};

	// Verandert de inhoud van de tab afhankelijk van de tab waarop gedrukt is.
	for (i = 0; i < forms.length; i++) {
		if (tab.name == forms[i].parentNode.id) {
			forms[i].parentNode.classList.add('showTab');
		} else {
			forms[i].parentNode.classList.remove('showTab');
		};
	};
};

// Sorteert op product / price / stock.
function sort(id){

	// Haalt de inhoud op die gesorteerd moet worden.
	var content = document.querySelector('#product_container ul');
	var els = Array.prototype.slice.call(document.querySelectorAll('#product_container ul .list_item'));
	var cls;
	var desc;

	// Haalt het id op van de button waarop gedrukt is
	var element = document.getElementById(id);

	// Haalt alle buttons op.
	var otherElements = element.parentNode.parentNode.getElementsByTagName("BUTTON");

	// Verandert het pijl incoontje van de buttons.
	for (i = 0; i < otherElements.length; i++) {
		if (otherElements[i] == element) {
			// Toggle asc / desc class
			if (element.classList.contains('asc')){
				element.classList.add('desc');
				element.classList.remove('asc');
				desc = true;
			} else {
				element.classList.remove('desc');
				element.classList.add('asc');
				desc = false;
			};
		} else {
			otherElements[i].classList.remove('asc');
			otherElements[i].classList.remove('desc');
		};
	};

	// Sorteert de producten afhankelijk van de button waarop gedrukt is.
	els.sort(function(a, b){
		if (id === 'sortPrice'){
			cls = 'price';
			na = parseInt(a.querySelector('.' + cls).innerHTML.replace(/[^0-9\.]/g, ''));
			nb = parseInt(b.querySelector('.' + cls).innerHTML.replace(/[^0-9\.]/g, ''));
			return desc ? (nb - na) : (na - nb);
		} else if (id === 'sortStock'){
			cls = 'stock';
			na = parseInt(a.querySelector('.' + cls).options[a.querySelector('.' + cls).options.length - 1].value);
			nb = parseInt(b.querySelector('.' + cls).options[b.querySelector('.' + cls).options.length - 1].value);
			return desc ? (nb - na) : (na - nb);
		} else {
			cls = 'product';
			na = a.querySelector('.' + cls).innerHTML;
			nb = b.querySelector('.' + cls).innerHTML;	
			return desc ? (nb > na) : (na > nb);
		}
	});

	// Bouwt de producten in de nieuwe volgorde weer op.
	els.forEach(function(el){
		content.appendChild(el);
	});
}

// Extra product informatie.
function description(expand_item){
	// Voegt of verwijdert de class 'expand' toe aan het element om de extra informatie weer te geven of te verstoppen.
	expand_item.parentNode.classList.toggle('expand');
};

// Laat de sectie zien afhankelijk van de button waarop gedrukt is in het menu.
function classToggle(ele, get, refresh){
	var eleContainer = document.getElementById(ele);

	if (get == true) {
		var content = eleContainer.getElementsByClassName('content')[0];

		// Controleert of de content opnieuw moet worden geladen.
		if(refresh){
			loader(eleContainer, true);

			// Haalt de inhoud op.
			ajax.get('pages/' + ele, false, function(xhr) {
				content.innerHTML = xhr.responseText;
				loader(eleContainer, false);
			});
		} else {
			if (content.innerHTML.length == 0) {
				loader(eleContainer, true);

				// Haalt de inhoud op als het element leeg is.
				ajax.get('pages/' + ele, false, function(xhr) {
					content.innerHTML = xhr.responseText;
					loader(eleContainer, false);
				});
			};
		};
	};

	// Slide in / out class toggle
	if (!eleContainer.classList.contains('reveal')) {
		eleContainer.classList.add('reveal');
	} else {
		eleContainer.classList.remove('reveal');
	}
};

/**********************************************************/
/********* Login / Create account functionaliteit *********/
/**********************************************************/

// Valideert de velden met javascript om feedback te kunnen geven zonder connectie te maken met de server.
function validateField(input){
	var re;
	var message;
	var parent_element = input;
	var correctField = false;

	// Zoekt het element waarin het error bericht geplaatst zal worden.
	while (parent_element.tagName != 'DIV') {
		parent_element = parent_element.parentNode;
		if (parent_element.tagName == 'DIV') {
			error_element = parent_element.getElementsByTagName("SPAN")[0];
		};
	};

	// Als de input niet leeg is.
	if(input.value != ''){
		// Als het input type 'x' is.
		switch(input.type) {
			case "text":
				if(input.name == 'achternaam'){
					//Voorbeeld: Example'd
					re = /(?:[A-Za-z'-]+\s*){2,3}$/;
					message = 'Dit is geen achternaam.';
				} else {
					//Voorbeeld: Example
					re = /^[a-zA-Z]{2,20}$/;
					message = 'Alleen letters zijn toegestaan.';
				};
			break;
			case "email":
				//Voorbeeld: Example7@idp.com
				re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,30}))$/;
				message = 'Dit is geen email adres.';
			break;
			case "password":
				//Voorbeeld: Example7!
				re = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{4,20}$/;
				message = 'Wachtwoord moet een hoofdletter, kleine letter, nummer en symbool bevatten.';
			break;
		};

		// Als veld correct is.
		var correctField = re.test(input.value);

		// Voegt toe / verwijdert styles en messages afhankelijk van correctField.
		if(correctField) {
			parent_element.classList.add('good')
			parent_element.classList.remove('error')
			error_element.innerHTML = '';
		} else {
			parent_element.classList.add('error')
			parent_element.classList.remove('good')
			error_element.innerHTML = message;
		};

	// Verwijdert de styles en messages als het veld leeg is.
	} else {
		parent_element.classList.remove('good');
		parent_element.classList.remove('error');
		error_element.innerHTML = '';
	};

	if((input.name == 'wachtwoord') || (input.name == 'wachtwoord_bevestiging')) {
		var parent = input.parentNode.parentNode;
		var parent_children = parent.children;
		parent.getElementsByTagName('SPAN')[2].innerHTML = '';
	};
};

// Error berichten die van de server af komen. (Komt voor in de functie: ValidateForm).
function error_message(keys, form){
	var message = '';

	// Voor elk element in 'keys'.
	for(var i=0; i<Object.keys(keys).length; i++) {

		// Als het element gelijk is aan 0 of 2 (foutmeldingen).
		if ((keys[Object.keys(keys)[i]] == '0') || (keys[Object.keys(keys)[i]] == '2')){

			// Feedback afhankelijk van de key met de error.
			switch (Object.keys(keys)[i]) {
				case "login":
				 	if (keys['email_login'] != '2') {
						message = 'Email en wachtwoord niet gelijk.';
					};
				break;			
				case "email_login":
				 	if (keys[Object.keys(keys)[i]] == '0') {
						message = 'Dit is geen email adres.';
					} else if (keys[Object.keys(keys)[i]] == '2') {
						message = 'Email bestaat niet.';
					};
				break;
				case "wachtwoord_login":
					message = 'Wachtwoord moet een hoofdletter, kleine letter, nummer en symbool bevatten.';
				break;
				case "voornaam":
					message = 'Alleen letters zijn toegestaan.';
				break;
				case "achternaam":
					message = 'Alleen letters zijn toegestaan.';
				break;
				case "email_adres":
				 	if (keys[Object.keys(keys)[i]] == '0') {
						message = 'Dit is geen email adres.';
					} else if (keys[Object.keys(keys)[i]] == '2') {
						message = 'Email is al in gebruik.';
					};
				break;
				case "wachtwoord":
					message = 'Wachtwoord moet een hoofdletter, kleine letter, nummer en symbool bevatten.';
				break;
				case "wachtwoord_bevestiging":
					message = 'Wachtwoord moet een hoofdletter, kleine letter, nummer en symbool bevatten.';
				break;
				case "wachtwoorden_gelijk":
					message = 'Wachtwoorden zijn niet gelijk.';
				break;
				case "password_recovery_email":
				 	if (keys[Object.keys(keys)[i]] == '0') {
						message = 'Dit is geen email adres.';
					} else if (keys[Object.keys(keys)[i]] == '2') {
						message = 'Email bestaat niet.';
					};
				break;
			};

			// Laat de feedback zien & extra regels voor keys waar dat nodig is (Wachtwoord_gelijk & Login).
			if ((Object.keys(keys)[i] != 'wachtwoorden_gelijk') && (Object.keys(keys)[i] != 'login')) {
				var parent = form.elements[Object.keys(keys)[i]].parentNode;
						
				parent.classList.add('error');
				parent.classList.remove('good');
				parent.getElementsByTagName('SPAN')[0].innerHTML = message;

			} else if (Object.keys(keys)[i] == 'wachtwoorden_gelijk') {
				var parent = form.elements['wachtwoord'].parentNode.parentNode;
				var parent_children = parent.children;

				// Error class op beide wachtwoord inputs.
				for(var c=0; c < parent_children.length; c++) {
					if (parent_children[c].tagName == 'DIV') {
						parent_children[c].classList.add('error');
						parent_children[c].classList.remove('good');
					};
				};
				parent.getElementsByTagName('SPAN')[2].innerHTML = message;

			} else if (Object.keys(keys)[i] == 'login') {
				if (keys['email_login'] != '2') {
					var parent = form.elements['wachtwoord_login'].parentNode;
				} else {
					var parent = form.elements['email_login'].parentNode;
				}

				parent.classList.add('error');
				parent.classList.remove('good');
				parent.getElementsByTagName('SPAN')[0].innerHTML = message;
			};
		} else {
			message = '';
		};
	};
};

// Valideert het hele formulier.
function validateForm(form){

	// Haalt alle input velden op.
	while (form.tagName != 'FORM') {
		form = form.parentNode;
		if (form.tagName == 'FORM') {
			var input_fields = form.getElementsByTagName('INPUT');
		};
	};

	// Maakt de query string aan om de informatie later naar de server te sturen.
	var queryString = '';

	for(var i=0; i<input_fields.length; i++) {
		if (i != (input_fields.length - 1)) {
			queryString += input_fields[i].name + '=' + input_fields[i].value + '&';
		} else {
			queryString += input_fields[i].name + '=' + input_fields[i].value;
		};
	};

	// Als het formulier id 'x' is.
	switch(form.id) {
		case "login_form":

			// Stuurt de login gegevens naar de server.
			ajax.post(queryString, 'php/login', false, function(xhr) {
				var response = JSON.parse(xhr.responseText);

				// Als de validatie correct is.
				if (response['login'] == '1'){
					ajax.get('pages/products', true, function(xhr) {
						page_container.innerHTML = xhr.responseText;
					});
				} else {
					if(response['leeg'] != '0'){
						error_message(response, form);
					};
				};
			});
		break;
		case "create_account_form":

			// Stuurt de registratie gegevens naar de server.
			ajax.post(queryString, 'php/registration', false, function(xhr) {
				var response = JSON.parse(xhr.responseText);

				// Als de validatie correct terug geeft.
				if (response['correct'] == '1'){
					// Haalt de login tab op.
					var loginTab = document.getElementsByClassName("tab-group")[0].getElementsByTagName("button")[0];

					// Insert tekst in de login tab inhoud.
					document.getElementById("global_message").innerHTML = 'Uw account is aangemaakt.';

					// Haalt de registratie inputs leeg.
					for (i = 0; i < input_fields.length; i++) {
						input_fields[i].value = '';
						input_fields[i].parentNode.classList.remove('good');
					};

					// Zet de login tab op actief.
					activeTab(loginTab);
				} else {
					if(response['leeg'] != '0'){
						error_message(response, form);
					}
				};
			});
		break;
		case "password_recovery":

			// Stuurt de recovery email gegevens naar de server.
			ajax.post(queryString, 'php/passrecEmail', false, function(xhr) {
				var response = JSON.parse(xhr.responseText);

				// Als de validatie correct terug geeft.
				if (response['correct'] == '1'){
					// Haalt de login tab op.
					var loginTab = document.getElementsByClassName("tab-group")[0].getElementsByTagName("button")[0];

					// Insert tekst in de login tab inhoud.
					document.getElementById("global_message").innerHTML = 'Email met uw wachtwoord is verstuurd.';

					// Haalt de registratie inputs leeg.
					for (i = 0; i < input_fields.length; i++) {
						input_fields[i].value = '';
						input_fields[i].parentNode.classList.remove('good');
					};

					// Zet de login tab op actief.
					activeTab(loginTab);
				} else {
					if(response['leeg'] != '0'){
						error_message(response, form);
					};
				};
			});
		break;
	};
};

/**********************************************/
/********* Winkelmand functionaliteit *********/
/**********************************************/

// Product button functionaliteit. 
function cartAction(product, action, product_code){
	// Haalt de benodigde elementen op.
	var eleContainer = document.getElementById('checkout');
	var content = eleContainer.getElementsByClassName('content')[0];
	var cart_message = document.getElementById("cart_message");

	// Als de actie van de knop 'add' is.
	if (action == 'add'){
		// Haalt de product container op.
		while (product.tagName != 'LI') {
			product = product.parentNode;
			if (product.tagName == 'LI') {
				// Haalt de stock van het product op.
				var stock = product.getElementsByTagName('SELECT')[0];
				var stock = stock.options[ stock.selectedIndex ].value;
			};
		};
	};

	// Maakt de query string aan en laat een feedback bericht voor 2 seconden zien.
	var queryString;
	if(action != "") {
		switch(action) {
			case "add":
				queryString = 'action='+action+'&product_code='+ product_code+'&kwantiteit='+stock;

				if(stock > 0) {
					cart_message.innerHTML = 'Product toegevoegd';
					cart_message.classList.add('show');

					setTimeout(function() {
						cart_message.classList.remove('show');
					}, 2000);
				};
			break;
			case "remove":
				queryString = 'action='+action+'&product_code='+ product_code;
				cart_message.innerHTML = 'Product verwijderd';
				cart_message.classList.add('show');

				setTimeout(function() {
					cart_message.classList.remove('show');
				}, 2000);
			break;
			case "empty":
				queryString = 'action='+action;
				cart_message.innerHTML = 'Winkelmand geleegt.';
				cart_message.classList.add('show');

				setTimeout(function() {
					cart_message.classList.remove('show');
				}, 2000);
			break;
		}; 
	};

	// Stuurt de query string op naar cart.php.
	ajax.post(queryString, 'pages/cart', false, function(xhr) {
		content.innerHTML = xhr.responseText;
	});
};

// Verstuurt de email met de QR-code.
function order() {
	// Haalt de container op van het bericht.
	var order_container = document.getElementById("order_message");
	var container = order_container;

	// Haalt de container op van het winkelmandje.
	while (container.tagName != 'SECTION') {
		container = container.parentNode;
	};

	loader(container, true);

	// Voert de bestelling uit.
	ajax.get('php/sendQR', false, function(xhr) {
		order_container.innerHTML = xhr.responseText;
		loader(container, false);
	});
};

// Voegt credits toe aan het account.
function addCredits(ele) {
    ele.setAttribute('disabled', true);
    var container = ele;

	// Zoekt de container met het id credits.
	while (container.id != 'credits') {
		container = container.parentNode;
		if (container.id == 'credits') {
			container = container;
		};
	};

	// Haalt de geselecteerde input op.
	var select_element = container.getElementsByTagName('SELECT')[0];
	queryString = 'credits='+select_element.options[select_element.selectedIndex].value;

	// Haalt het span element met de credits van de klant op.
	var credit_span = container.getElementsByTagName('SPAN')[0];

	// Haalt de nieuwe credit resultaten op.
	ajax.post(queryString, 'php/addCredits', false, function(xhr) {
		credit_span.innerHTML = xhr.responseText;
	});

	// Button wordt opnieuw geactiveert na 5 seconden.
	setTimeout(function(){
	    ele.removeAttribute('disabled');
	}, 5000);
};

// Verwijdert de bestelde producten.
function removeOrder(ele, bestel_nummer, product_ID) {

	// Zoekt de container met de class content.
	while (!ele.classList.contains('content')) {
		ele = ele.parentNode;
		if (ele.classList.contains('content')) {
			container = ele;
		};
	};

	// Maakt de query string aan met de juiste gegevens.
	queryString = 'bestel_nummer='+bestel_nummer+'&product_ID='+ product_ID;

	// Verstuurt de query string om het element uit de database te verwijderen.
	ajax.post(queryString, 'pages/bestellingen', false, function(xhr) {
		container.innerHTML = xhr.responseText;
	});
};

// Stuurt de QR-code van de bestelling opnieuw op.
function resendQR(button) {
    button.setAttribute('disabled', true);

	// Haalt de QR-code container op.
	var container = button
	var message = document.getElementById('resend_message');

	// Haalt de product container op.
	while (container.tagName != 'SECTION') {
		container = container.parentNode;
	};

	loader(container, true);

	// Stuurt de QR-code opnieuw op.
	ajax.get('php/resendQR', false, function(xhr) {
		message.innerHTML = xhr.responseText;
		loader(container, false);
	});

	// Wacht 10 minuten voordat de klant opnieuw de QR-code kan aanvragen.
    setTimeout(function(){
        button.removeAttribute('disabled');
    }, 60000);
};