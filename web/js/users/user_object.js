/**
 * Created by pieter on 11/08/15.
 */

var UserObject = function(){
    this.hh = new handlebar_helper();
};

/**
 * Function to parse an user object into a html template using Handlebars
 * @param user: {group_id: foo, id:bar, name:bizz}
 * @returns string
 */
UserObject.prototype.drawUser = function(user){
    return this.hh.draw_template_from_object(user, 'template-user-short');
};

/**
 * Create a user edit form for user
 * @param user
 * @returns {string}
 */
UserObject.prototype.drawEdit = function(user){
    return this.hh.draw_template_from_object(user, 'template-user-open');
};

/**
 * Create a form (after a UI-call) for new user creation
 * @param group
 */
UserObject.prototype.addNew = function(group){
    var empty = {'group_id' : group, 'id': 0, 'name': 'new user'};
    var html = this.drawEdit(empty);
    $('#individual-user').html(html);
};

/**
 * Draw a list of users
 * @param users list (array) of user objects
 * @returns {string}
 */
UserObject.prototype.drawList = function(users){
    var html = '';
    for (var i = 0; i < users.length; i++) {
        html = html + this.drawUser(users[i]);
    }
    return html;
};

UserObject.prototype.load = function(user){};

/**
 * Create a new user from data (form) in group
 * @param group
 * @param data
 */
UserObject.prototype.create = function(group, data){
    var self = this; /*https://stackoverflow.com/questions/20279484/how-to-access-the-correct-this-context-inside-a-callback*/
    $.ajax({
        'type': 'POST',
        'url'  : Routing.generate('add-new-user', {id: group}),
        'data' : data,
        'success': function(response){
            /* If the response is an array, it is a list of users that can be passed to this.drawList() */
            if (response instanceof Array) {
                var html = self.drawList(response);
                $('.user-group-list').html(html);
                $('#individual-user').html('');
                return true;
            } else {
                /* If it is an object, we have an error on our hands */
                var error = response;
                switch(error.code) {
                    case 'USER_EXISTS':
                        alert('A user with this e-mail address already exists. Please select a different address.');
                        break;
                    default:
                        alert(error.msg);
                }
                return false;
            }
        },
        'error': function(r, status, error){
            alert('The server returned the following error: ' + status + '(' + error + ')');
            return false;
        }
    });
};

UserObject.prototype.save = function(data) {};

UserObject.prototype.loadFromGroup = function(group){};


/*
COMPATIBILITY FUNCTIONS
 */
function addNewUser(group){
    var uo = new UserObject();
    uo.addNew(group);
}
function drawUser(user){
    var uo = new UserObject();
    return uo.drawUser(user);
}

function drawUsers(users){
    var uo = new UserObject();
    return uo.drawList(users);
}

function drawEditUser(user){
    var uo = new UserObject();
    return uo.drawEdit(user);
}

function saveNewUser(group, data){
    var uo = new UserObject();
    uo.create(group, data);
}

function saveUser(data){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('update-user'),
        'data' : data,
        'success': function(users){
            var html = drawUsers(users);
            $('.user-group-list').html(html);
            $('#individual-user').html('');
        },
        'error': function(){
            alert("An error has occurred. Please try again...");
        }
    });
}

function loadUsersForGroup(group){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('trip-load-users-for-group', {id: group}),
        'success': function(users){
            var html = drawUsers(users);
            $('.user-group-list').html(html);

        },
        'error': function(){
            alert("An error has occurred. Please try again...");
        }
    })
}


function loadUser(user){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('trip-load-user', {id: user}),
        'success': function(user){
            var html = drawEditUser(user);

            $('#individual-user').html(html);

        },
        'error': function(){
            alert("An error has occurred. Please try again...");
        }
    })
}