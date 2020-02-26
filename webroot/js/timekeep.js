$(document).ready(function () {
    updateTableClassing();
})
function OutNow(e) {
    e.preventDefault();
    var d = new Date();
    var monthstring = (parseInt(d.getMonth()) + 1).valueOf();
    var month = ('0' + monthstring).substr(-2);
    var day = ('0' + d.getDate()).substr(-2);
    var dstring = d.getFullYear() + '-' + month + '-' + day + ' ' + d.toLocaleTimeString();
    alert(d);
    $(this).parents('form').find('#TimeTimeOut').attr('value', dstring);
}

function AdjustSelect(e) {
    //Get curr datetime
    var d = new Date();
    //set variable for users adjustment selection
    var adj = parseInt($(this).val());
    //Adjust per parameters
    var dadj = d.setMinutes(d.getMinutes() + adj);
    //Set new value to d

    //Reformat for output
    var monthstring = (parseInt(d.getMonth()) + 1).valueOf();
    var month = ('0' + monthstring).substr(-2);
    var day = ('0' + d.getDate()).substr(-2);
    var dstring = d.getFullYear() + '-' + month + '-' + day + ' ' + d.toLocaleTimeString();
//            alert(dadj);
    $(this).parents('form').find('#TimeTimeOut').attr('value', dstring);
}

function timeChange(e, action) {
    var id = $(e.currentTarget).attr('index');
    $('#row_' + id).removeClass('alt open paused closed').addClass('ajax');
    $.ajax({
        type: "GET",
        url: OSTime.webroot + OSTime.controller + action + "/" + id,
        dataType: "JSON",
        success: function (data) {
            if (data.html.match(/<tr/) != null) {
                replaceRow(data.html, id);
            } else {
                $('#row_' + id).before('<tr><td colspan="5" class="flashmessage"' + data.html + '</td></tr>');
            }
        },
        error: function () {
            handleError(data);
            alert('Error adding the time row.')
        }
    });
}

function timeStop(e) {
    e.preventDefault();
    timeChange(e, 'timeStop');
}

function timePause(e) {
    e.preventDefault();
    timeChange(e, 'timePause');
}

function timeRestart(e) {
    e.preventDefault();
    timeChange(e, 'timeRestart');
}

function timeReopen(e) {
    e.preventDefault();
    timeChange(e, 'timeRestart');
}

function timeDelete(e) {
    e.preventDefault();
    var c = confirm('Are you sure you want to delete this time record?');
    if (!c) {
        return;
    }
    var id = $('#' + $(this).attr('index') + 'TimeId').val();
    $('#row_' + id).removeClass('alt open paused closed').addClass('ajax');
    $.ajax({
        type: "POST",
        url: OSTime.webroot + OSTime.controller + "deleteRow/" + id,
        dataType: "JSON",
        success: function (data) {
            if (data.result) {
                $('#' + id + 'TimeId').parents('tr').remove();
            } else {
                alert('The deletion failed, please try again.');
            }
        },
        error: function () {
            handleError(data);
            alert('Error deleting the time row.')
        }
    });
}

function newTime(e) {
    e.preventDefault();

}

function timeInfo(e) {
    e.preventDefault();
    var target = e.currentTarget;
    var id = $(target).attr('index');
    $.ajax({
        type: "GET",
        dataType: "HTML",
        url: OSTime.webroot + OSTime.controller + 'edit/' + id + '/true',
        success: function (data) {
            $('div.times.form').remove();
            $(target).parents('td').prepend(data);
            bindHandlers('div.times.form');
            $('div.times.form').draggable();
        },
        error: function (data) {
            handleError(data);
            alert('There was an error on the server. Please try again');
        }
    })
}

function cancelTimeEdit(e) {
    e.stopPropagation();
    $('div.times.form').remove();
}

function saveTimeEdit(e) {
    e.preventDefault();
    e.stopPropagation();
    var id = $(e.currentTarget).attr('index');
//	var formData = $('form#TimeEditForm').serialize();
    $.ajax({
        type: "PUT",
        dataType: "HTML",
        data: $('form#TimeEditForm').serialize(),
        url: $('form#TimeEditForm').attr('action'),
        success: function (data) {
            if (data.match(/<tr/) != null) {
                replaceRow(data, id);
            } else {
                $('div.times form').prepend(data);
            }
        },
        error: function (data) {
            handleError(data);
            alert('failure');
        }
    })
}

/**
 * Replace the row contents with returned row
 */
function replaceRow(data, id) {
    $('#row_' + id).replaceWith(data);
    bindHandlers('#row_' + id);
    initToggles('#row_' + id);
    updateTableClassing();
    updateTableSortability();
}

/**
 * Set the default project for new time entries
 */
function setDefaultProject() {
    e.preventDefault();
    alert('Set default project');
}

/**
 * Create a new row for time keeping
 */
function newTimeRow(e) {
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: OSTime.webroot + OSTime.controller + "newTimeRow",
        dataType: "JSON",
        success: function (data) {
            $('#TimeTrackForm tbody').append(data.html);
            OSTime.projects = data.projects;
            OSTime.taskGroups = data.taskGroups;
            updateTableClassing();
            updateTableSortability();
            bindHandlers('table.sortable tr.last');
            initToggles();
        },
        error: function (data) {
            handleError(data);
            alert('Error adding the time row.')
        }
    });
}

function handleError(data) {
    if (data.responseText.search('cake-error')) {
        $('#content').prepend(data.responseText);
        document.location = 'http://localhost/time3x/users/who';
        return false;
    }
    return true;
}

/**
 * Update table classing for kickstart after AJAX insertion
 */
function updateTableClassing() {
    var rows = $('table.sortable').find('tbody tr');
    rows.removeClass('alt first last');
    var table = $('table.sortable');
    table.find('tr:even').addClass('alt');
    table.find('tr:first').addClass('first');
    table.find('tr:last').addClass('last');

}

/**
 * Update sortability on AJAX inserted row
 */
function updateTableSortability() {
    $(this).find('table.sortable tr.last th,td').each(function () {
        $(this).attr('value', $(this).text());
    });
}

/**
 * Save a single field change on the timekeeping form
 */
function saveField(e) {
    e.stopPropagation();
    var id = $(e.currentTarget).attr('index');
    var fieldName = $(e.currentTarget).attr('fieldName');
    var value = $(e.currentTarget).val();
    var postData = {'id': id, 'fieldName': fieldName, 'value': value};
    $('#row_' + id).removeClass('alt open paused closed').addClass('ajax');
    $.ajax({
        type: "POST",
        url: OSTime.webroot + OSTime.controller + "saveField",
        data: postData,
        dataType: "JSON",
        success: function (data) {
            $('tr#row_' + data.id).replaceWith(data.html);
            OSTime.projects = data.projects;
            OSTime.taskGroups = data.taskGroups;
            updateTableClassing();
            updateTableSortability();
            bindHandlers('tr#row_' + data.id);
            initToggles();
        },
        error: function () {
            handleError(data);
            alert('Error adding the time row.')
        }
    });

}
/**
 * Hide the duration input on blur
 */
function hideDurationInput(e) {
    e.stopPropagation();
    var id = $(e.currentTarget).attr('index');
    if($(e.currentTarget).css('display') != 'none'){
        $('#' + id + 'duration').trigger('click');
    }

}

	/**
	 * Handle request to create a new task for a project
	 *
	 * When the task "New task" is selected, prompt the
	 * ust for the new task, then save and set it.
	 *
	 * @param {event} e
	 */
	function taskChoice(e) {
		if ($(e.currentTarget).val() === 'newtask') {
			var task = window.prompt('Enter the new task.');
			if (task != null) {
				var proj = $(e.currentTarget).attr('project_id');
				if (proj == '' || typeof(proj) == 'undefined') {
					alert('You can\'t make new tasks until you specify a project.');
				} else {
				$.ajax({
					type: "POST",
					dataType: "HTML",
					data: {Task : {project_id: proj, name: task}},
					url: OSTime.webroot+'tasks/add',
					success: function (data) {
						if(data.match(/could not/) != 'could not') {
							location.replace(location.href);
						} else {
							alert('The new task was not saved.');
						}
					},
					error: function (data) {
						alert('ajax failure');
					}
				})

				}
			}
		} else {
			saveField(e);
		}
	}

	function plus(e) {
		var delta = 20;
		changeSize(e, delta);
	}

	function minus(e) {
		var delta = -20
		changeSize(e, delta);
	}

	function changeSize(e, delta) {
		var targets = $('textarea[id*="TimeActivity"]');
		var axis = $(e.currentTarget).hasClass('height') ? 'height' : 'width';
		var start_size = parseInt($(targets[0]).css(axis));
		$(targets).css(axis, start_size + delta);
	}

	function timeDuplicate(e) {
    e.preventDefault();
    $.ajax({
        type: "GET",
        url: OSTime.webroot + OSTime.controller + "duplicateTimeRow/" + $(e.currentTarget).attr('index'),
        dataType: "JSON",
        success: function (data) {
            $('#TimeTrackForm tbody').append(data.html);
            updateTableClassing();
            updateTableSortability();
            bindHandlers('table.sortable tr.last');
            initToggles();
        },
        error: function () {
            handleError(data);
            alert('Error adding the time row.')
        }
    });
	}
