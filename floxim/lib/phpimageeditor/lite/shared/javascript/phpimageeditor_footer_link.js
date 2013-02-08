function phpimageeditor_footer_link()
{
	if (parent.frames.length == 0) 
	{
		//Not in iframes.
		var a = document.createElement('a');
		a.setAttribute('href', 'http://www.phpimageeditor.se');
		a.setAttribute('target', '_blank');
		a.setAttribute('style', 'color: #4d4d4d; text-decoration: none; font-size: 10px; display: block; text-align: center; margin: 10px;');
		a.setAttribute('id', 'pie-footer-link');
		a.innerHTML = 'We´re using PHP Image Editor';

		document.body.appendChild(a);
	}
}

window.onload = function() 
{ 
	phpimageeditor_footer_link(); 
};