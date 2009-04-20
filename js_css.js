var css_tag = document.createElement('style');
css_tag.type = 'text/css';
var text = document.createTextNode(".invisible_if_js { display: none; }");
css_tag.appendChild(text);
document.getElementsByTagName('head')[0].appendChild(css_tag);
