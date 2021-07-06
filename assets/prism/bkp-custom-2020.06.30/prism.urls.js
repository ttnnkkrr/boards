// NEW VERSION
// Using global events for target case-by-case detection

// linker to docs for prism.js update
// created 7/01/2019 - updated 09/08/2019
// script by joedf
var redirectQURL = '/docs/redirect.php?topic=';

// add CSS 'link' styling
codelinkstyle = document.createElement('style');
codelinkstyle .innerHTML = "code .selector:hover, code .constant:hover, code .keyword:hover, code .symbol:hover, \
							code .tag:hover, code .builtin:hover, code .x_function:hover, code .important:hover, \
							code .variable:hover { \
								text-decoration:underline !important; \
								cursor: pointer; \
							}";
document.head.appendChild(codelinkstyle);

// add events for each codebox
var cboxes = document.querySelectorAll('code.language-autohotkey');

for(var i=0;i<cboxes.length;i++) {
	cboxes[i].addEventListener('click', function(e){
		
		var citem = e.target;
		if (citem.className.indexOf('token') >= 0) {

			var type = citem.className.replace('token','').trim().toLowerCase();
			var cURL = null;

			// supported types: selector, constant, operator, and variable
			if (['selector','constant','keyword','symbol','tag'].indexOf(type)>=0) {
				cURL = redirectQURL+citem.innerText;
			} else if (['builtin','x_function'].indexOf(type)>-1) {
				cURL = redirectQURL+citem.innerText+'()';
			} else if (type.indexOf('variable')>-1) {
				// same as bove normal rediretor usage but remove the % symbols...
				cURL = redirectQURL+citem.innerText.replace(/%/gi,'');
			} else if (type.indexOf('important')>-1) {
				// same as bove normal rediretor usage but remove the # symbols...
				cURL = redirectQURL+citem.innerText.replace(/#/gi,'');
			} else {
				// do nothing? leave as is...
				return false;
			}

			var w = window.open(cURL, '_blank');
  			w.focus();
			return true;
		}

	});
}

console.log("prism.urls.js executed.");