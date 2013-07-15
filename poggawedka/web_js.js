var avatarBig=document.getElementById('avatarBig');

function findPos(obj) {
	var curleft = curtop = 0;
		if (obj.offsetParent) {
			while (obj.offsetParent) {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
				obj = obj.offsetParent;
			}
		}
	return [curleft,curtop];
}

function onMouseOut(self)
{
	avatarBig.setAttribute('style','display:none;');
	
}
	
function onMouseOver(self)
{
	var pos=findPos(self);
	avatarBig.setAttribute('style','position:absolute; left:'+pos[0]+';top:'+(pos[1]+16)+';');
	avatarBig.setAttribute('src',self.getAttribute('src'));
}