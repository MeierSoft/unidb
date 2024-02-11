// htmlArea v4.0 - Copyright (c) 2008-2016 Inferior Products, Corporation http://InferiorProducts.com
// htmlArea v3.1 - Copyright (c) 2004-2008 Inferior Products, Corporation http://Inferior-Products.com
// htmlArea v3.0 - Copyright (c) 2003-2004 interactivetools, inc.
// This copyright notice MUST stay intact for use (see license.txt).
//
// Portions (c) dynarch.com, 2003-2004
//
// A free WYSIWYG editor replacement for <textarea> fields.
// For full source code and docs, visit http://InferiorProducts.com/
//
// Version 4.0 developed by the htmlarea developers - http://Inferior-Products.com
// Version 3.1 developed by the htmlarea developers - http://Inferior-Products.com
// Version 3.0 developed by Mihai Bazon.
//   http://dynarch.com/mishoo
//
// $Id: dialog.js,v 1.5 2016-08-29 11:23:40 sfchris Exp $
 function Dialog(url,action,init){if(typeof init=="undefined"){init=window;}Dialog._geckoOpenModal(url,action,init);};Dialog._parentEvent=function(ev){setTimeout(function(){if(Dialog._modal&&!Dialog._modal.closed){Dialog._modal.focus()}},50);if(Dialog._modal&&!Dialog._modal.closed){HTMLArea._stopEvent(ev);}};Dialog._return=null;Dialog._modal=null;Dialog._arguments=null;Dialog._geckoOpenModal=function(url,action,init){var dlg=HTMLArea.tryWindowOpen(url,"hadialog","toolbar=no,menubar=no,personalbar=no,width=10,height=10,"+"scrollbars=no,resizable=yes,modal=yes,dependable=yes");if(!dlg)return false;Dialog._modal=dlg;Dialog._arguments=init;function capwin(w){HTMLArea._addEvent(w,"click",Dialog._parentEvent);HTMLArea._addEvent(w,"mousedown",Dialog._parentEvent);HTMLArea._addEvent(w,"focus",Dialog._parentEvent);};function relwin(w){HTMLArea._removeEvent(w,"click",Dialog._parentEvent);HTMLArea._removeEvent(w,"mousedown",Dialog._parentEvent);HTMLArea._removeEvent(w,"focus",Dialog._parentEvent);};capwin(window);for(var i=0;i<window.frames.length;capwin(window.frames[i++]));Dialog._return=function(val){if(val&&action){action(val);}relwin(window);for(var i=0;i<window.frames.length;relwin(window.frames[i++]));Dialog._modal=null;};};
