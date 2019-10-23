
$(document).ready(function () {
    bindHandlers();
    initToggles();
})


function timeOut(){
    //watch page for session timeout

    // if page times out, ajax a call
    // that will redirect
}

/**
 * Sweep the page for bindings indicated by HTML attribute hooks
 *
 * Class any DOM element with event handlers.
 * Place a 'bind' attribute in the element in need of binding.
 * bind="focus.revealPic blur.hidePic" would bind two methods
 * to the object; the method named revealPic would be the focus handler
 * and hidePic would be the blur handler. All bound handlers
 * receive the event object as an argument
 *
 * Version 2
 *
 * @param {string} target a selector to limit the scope of action
 * @returns The specified elements will be bound to handlers
 */
function bindHandlers(target) {
    if (typeof (target) == 'undefined') {
        var targets = $('*[bind*="."]');
    } else {
        var targets = $(target).find('*[bind*="."]')
    }
    targets.each(function () {
        var bindings = $(this).attr('bind').split(' ');
        for (i = 0; i < bindings.length; i++) {
            var handler = bindings[i].split('.');
            if (typeof (window[handler[1]]) === 'function') {
                // handler[0] is the event type
                // handler[1] is the handler name
                $(this).off(handler[0]).on(handler[0], window[handler[1]]);
            }
        }
    });
}

/**
 * Set up the click on a node to control the display-toggle of another node
 *
 * Any <item class=toggle id=unique_name> will toggle <item class=unique_name> on click
 */
function initToggles(target) {
    if (typeof (target) == 'undefined') {
        var targets = $('*.toggle');
    } else {
        var targets = $(target).find('*.toggle')
    }
    targets.unbind('click').bind('click', function(e) {
		var id = e.currentTarget.id;
        $('.' + $(this).attr('id')).toggle(50, function() {
            // animation complete.
			if (typeof(statusMemory) == 'function') {
				statusMemory(id, e);
			}
			e.currentTarget.focus();
        });
    })
}

function jxEdit(e) {
    var alias = $(e.currentTarget).attr('alias');
    var target = $(e.currentTarget);
    var originalValue = $(e.currentTarget).attr('originalValue');
    var prepData = {};
    prepData[alias] = {};
    prepData[alias]['id'] = $(e.currentTarget).attr('recordId');
    prepData[alias][$(e.currentTarget).attr('fieldName')] = $(e.currentTarget).val();

    $.ajax({
        type: "POST",
        dataType: "JSON",
        data: prepData,
        url: webroot + controller + "jxEdit",
        success: function (data) {
            if (data.result) {
            } else {
                target.val(originalValue);
            }
            target.before(data.flash);
        },
        error: function (data) {
            alert('The call failed, please try again.');
        }
    })
}


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


