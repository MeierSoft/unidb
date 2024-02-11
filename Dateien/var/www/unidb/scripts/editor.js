const editor = SUNEDITOR.create('editor', {
	display: 'block',
	width: '100%',
	height: 'auto',
	popupDisplay: 'full',
	lang: SUNEDITOR_LANG['de'],
	charCounter: true,
	charCounterLabel: 'Characters :',
	buttonList: [
		['undo', 'redo'],
		['font', 'fontSize', 'formatBlock'],
		['paragraphStyle', 'blockquote'],
		['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
		['fontColor', 'hiliteColor', 'textStyle'],
		['removeFormat'],
		['outdent', 'indent'],
		['align', 'horizontalRule', 'list', 'lineHeight'],
		['table', 'link', 'image'],
		['fullScreen', 'showBlocks', 'codeView'],
	]
});
