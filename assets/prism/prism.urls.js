// linker to docs for prism.js update
// created 7/01/2019 - updated 20/01/2019
// script by joedf
////////////////////////////////////

var redirectQURL = 'https://www.autohotkey.com/docs/redirect.php?topic=';

// get AHK codeboxes
var cboxes = document.querySelectorAll('code.language-autohotkey');

for(var i=0;i<cboxes.length;i++) {
	ctokens = cboxes[i].getElementsByClassName('token');
	for(var j=0;j<ctokens.length;j++) {
		type = ctokens[j].className.replace('token','').trim().toLowerCase();
		
		// supported types: selector, keyword, constant, operator, and variable
		if (['selector','keyword','constant','operator'].indexOf(type)>-1) {
			ctokens[j].outerHTML = '<a href="'+redirectQURL+ctokens[j].innerHTML+'">'+ctokens[j].outerHTML+'</a>';
		} else if (['builtin','function'].indexOf(type)>-1) {
			ctokens[j].outerHTML = '<a href="'+redirectQURL+ctokens[j].innerHTML+'()">'+ctokens[j].outerHTML+'</a>';
		} else if (type.indexOf('variable')>-1) {
			// same as bove normal rediretor usage but remove the % symbols...
			ctokens[j].outerHTML = '<a href="'+redirectQURL+ctokens[j].innerHTML.replace(/%/gi,'')+'">'+ctokens[j].outerHTML+'</a>';
		} else if (type.indexOf('important')>-1) {
			// same as bove normal rediretor usage but remove the # symbols...
			ctokens[j].outerHTML = '<a href="'+redirectQURL+ctokens[j].innerHTML.replace(/#/gi,'')+'">'+ctokens[j].outerHTML+'</a>';
		} else {
			// do nothing? leave as is...
		}
	}
}

/* force the underlined link style */
codelinkstyle = document.createElement('style');
codelinkstyle .innerHTML = "code a:hover{text-decoration:underline !important;}";
document.head.appendChild(codelinkstyle);
console.log("prism.urls.js executed.");

