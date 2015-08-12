// todo put script in correct location
Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
    switch (operator) {
        case '==':
            return (v1 == v2) ? options.fn(this) : options.inverse(this);
        case '===':
            return (v1 === v2) ? options.fn(this) : options.inverse(this);
        case '<':
            return (v1 < v2) ? options.fn(this) : options.inverse(this);
        case '<=':
            return (v1 <= v2) ? options.fn(this) : options.inverse(this);
        case '>':
            return (v1 > v2) ? options.fn(this) : options.inverse(this);
        case '>=':
            return (v1 >= v2) ? options.fn(this) : options.inverse(this);
        case '&&':
            return (v1 && v2) ? options.fn(this) : options.inverse(this);
        case '||':
            return (v1 || v2) ? options.fn(this) : options.inverse(this);
        default:
            return options.inverse(this);
    }
});

function getSettings(selector){
    var settings = {};
    $(selector).each(function(){
        if($(this).hasClass('activated')){
            settings[$(this).attr('id')] = true;
        }
        else {
            settings[$(this).attr('id')] = false;
        }
    });
    return settings;
}

function updateNotificationSettings(){
    var settings = getSettings('.settingNotif');

    $.ajax({
        'type' : 'POST',
        'data' : {settings: settings},
        'url'  : Routing.generate('update_notification_settings'),
        'success': function(resp){
            if(resp === "ok"){
                alert("Settings updated!");
            }
            else {
                alert("Something went wrong");
            }
        },
        'error': function(){
            alert("Server error, try again");
        }
    });

}

function loadNotificationSettings(){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('load_notification_settings'),
        'success': function(settings){
            for (var setting in settings) {
                if (settings.hasOwnProperty(setting)) {
                    if (settings[setting] == 'true' ){
                        $('#' + setting).addClass('activated');
                    }

                }
            }
        }
    });
}

function loadNotifications(){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('show-notifications'),
        'success': function(html){
            $('#notification-list').html(html);
        }
    });
}

function drawGroup(group){
    var source   = $("#template-group").html();
    var template = Handlebars.compile(source);

    return template(group);
}

function drawGroups(groups){
    var html = '';
    for(var i = 0; i < groups.length; i++){
        html += drawGroup(groups[i]);
    }
    return html;
}


function updateGroup(group_id, group_name){
    $.ajax({
        'type' : 'POST',
        'data' : {
            'name' : group_name
        },
        'url'  : Routing.generate('update-group', {id: group_id}),
        'success': function(groups){
            $('#open-edit-group').html('');
            var html = drawGroups(groups);
            $('.group-list').html(html);
        }
    });
}

function openEditGroup(groupId){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('trip-load-group', {id: groupId}),
        'success': function(group){

            var html = drawEditGroup(group);
            $('#open-edit-group').html(html);
        }
    });
}

function saveGroup(){
    $('#open-edit-group').html('');
}

function addNewGroup(){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('add-new-group'),
        'success': function(groups){
            var html = drawGroups(groups);
            $('.group-list').html(html);
        },
        'error': function(){
            alert("An error has occurred. Please try again...");
        }
    })
}

function updateProfile(){
    var user = {};

    user.name               = $('#profile-name').val();
    user.email              = $('#profile-email').val();
    user.password           = $('#profile-old-pass').val();
    user.newPassword        = $('#profile-new-pass').val();
    user.newPasswordRepeat  = $('#profile-new-pass-repeat').val();
    console.log(user);

    $.ajax({
        'type' : 'POST',
        'data' : user,
        'url'  : Routing.generate('update-profile'),
        'success' : function(resp){
            if(resp.status=="OK"){
                alert("Profile was updated successfully!");
            }
            else {
                alert(resp.error);
            }
        },
        'error' : function(){
            alert("Something went wrong.");
        }
    });

}



$('.settingNotif').on('click', function(){
    $(this).toggleClass('activated');
});



$('#save-notifications').on('click', function(){
    updateNotificationSettings();
});


loadNotifications();
loadNotificationSettings();


$('.new-group').on('click', function(){
    addNewGroup();
});

$('#update-profile').on('click', function(){
    updateProfile();
});

$(document).on('click', '.trip-group', function(){
    $('.group-list').css('display', 'none');

    openEditGroup($(this).attr('data-id'));
    loadUsersForGroup($(this).attr('data-id'));
});

$(document).on('click', '#add-new-user', function(){
    addNewUser($(this).attr('data-id'));
});


$(document).on('click', '#user-save-user', function(){
    var user_id = $(this).attr('data-id');
    var group_id = $(this).attr('data-group-id');
    var data = {
        'email'    : $('#user-email').val(),
        'username' : $('#user-username').val(),
        'pass' : $('#user-pass').val(),
        'confirm_pass' : $('#user-confirm-pass').val(),
        'user_id'  : user_id,
        'group_id' : group_id

    }

    if (user_id == 0){
        saveNewUser(group_id, data);
    }
    else {
        saveUser(data);
    }

});

$(document).on('click', '#load-group-modal', function(){
    loadBaseGroups();
});

$(document).on('click', '#save-group-config', function(){
    var groupName = $('#group-name').val();
    var groupId = $(this).attr('data-id');
    updateGroup(groupId, groupName);
    $('.group-list').css('display', 'block');
});

$(document).on('click', '.edit-user', function(){
    loadUser($(this).attr('data-id'));
});

$(document).on('click', '.delete-group', function(){
    var confirm = window.confirm("Are you sure you want to delete this group? All users belonging to this group will be deleted as well with this action!");
    var id = $(this).attr('data-id');
    if(confirm){
        deleteGroup(id);
        $('.group-list').css('display', 'block');
        $('#open-edit-group').css('display', 'none');
    }
});

// reload page after editing groups
$('#groupModal').on('hidden.bs.modal', function () {
    location.reload();
});