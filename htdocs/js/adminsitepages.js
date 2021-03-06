/**
 * Automatically populates the WYSIWYG box on the site pages screen with the
 * content of the appropriate page
 *
 * Copyright: 2006-2008 Catalyst IT Ltd
 * This file is licensed under the same terms as Mahara itself
 */

var oldPageContent = '';
var oldPageName = 'home';
var checkOldContent = false;

function updateWYSIWYGText() {
    if (checkOldContent && oldPageContent != tinyMCE.activeEditor.getContent() && !confirm(get_string('discardpageedits', 'admin'))) {
        $('editsitepage_pagename').value = oldPageName;
        return;
    }
    if (!tinyMCE.isMSIE) {
        // Disable changed content check for IE (see below)
        checkOldContent = true;
    }
    sendjsonrequest(
        'editchangepage.json.php',
        {'pagename' :$('editsitepage_pagename').value},
        'POST',
        function(data) {
            if (!data.error) {
                tinyMCE.activeEditor.setContent(data.content);
                oldPageContent = tinyMCE.activeEditor.getContent();
                oldPageName = $('editsitepage_pagename').value;
            }
        }
    );
}

function connectElements() {
    connect('editsitepage_pagename', 'onchange', updateWYSIWYGText);
}

function contentSaved(form, data) {
    connectElements();
    if (!tinyMCE.isMSIE) {
        // Disabling changed content check for IE; Need to work out
        // why the getBody() call in getContent fails to return the
        // body element.
        oldPageContent = tinyMCE.activeEditor.getContent();
    }
    formSuccess(form, data);
}

addLoadEvent(function() {
    connectElements();
    updateWYSIWYGText();
});
