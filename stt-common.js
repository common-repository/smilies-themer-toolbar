jQuery.fn.extend({
	insertAtCaret: function(sm){

		spaceSmiley = function(smiley, pre) {
			smiley = smiley + ' ';

			var lastPreSmileyChar = pre.substr(pre.length - 1, 1);
			if (lastPreSmileyChar && lastPreSmileyChar != ' '
				&& lastPreSmileyChar.charCodeAt(0) != 10
				&& lastPreSmileyChar.charCodeAt(0) != 13) {
				smiley = ' '+smiley;
			}
			return smiley;
		}

		this.focus();
		if (document.selection && !window.opera) {
			// IE
			var sel = document.selection.createRange();
			var sel2 = sel.duplicate();
			sel2.moveToElementText(this[0]);
			sel2.setEndPoint('StartToEnd', sel);
			selEnd = this.val().length - sel2.text.length;
			sel2.setEndPoint('StartToStart', sel);
			selStart = this.val().length - sel.text.length;
			pre = this.val().substring(0, selStart);

			sm = spaceSmiley(sm, pre);
			sel.text = sm;
		}
		else if (typeof this[0].selectionStart != "undefined") {
			selStart = this[0].selectionStart;
			selEnd = this[0].selectionEnd;
			pre = this.val().substring(0, selStart);
			post = this.val().substring(selEnd);
			// if the character before the smiley is not a space, let's
			// add one before it. And add a space to the end.
			sm = spaceSmiley(sm, pre);

			var risultato = pre + sm + post;
			this.val(risultato);
			this[0].selectionEnd = this[0].selectionStart = selStart + sm.length;
		}
		else {
			sm = spaceSmiley(sm, this.val());
			this.val(this.val() + sm)
		}
	}
})
