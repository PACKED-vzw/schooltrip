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

/**
 * Create an alert with the .msg property of an error object returned by the server API.
 * @param error
 */
UserObject.prototype.serverErrorHandler = function(error){
    alert(error.msg);
};

/**
 * Function to load a single user identified by id
 * @param id
 */
UserObject.prototype.load = function(id){
    var self = this;
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('trip-load-user', {id: id}),
        'success': function(response){
            var html = self.drawEdit(response);
            $('#individual-user').html(html);
        },
        'error': function(r, status, error){
            alert('The server returned the following error: ' + status + '(' + error + ')');
        }
    })
};

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
            } else {
                /* If it is an object, we have an error on our hands */
                self.serverErrorHandler(response);
            }
        },
        'error': function(r, status, error){
            alert('The server returned the following error: ' + status + '(' + error + ')');
        }
    });
};

/**
 * Save an updated user.
 * @param data
 */
UserObject.prototype.update = function(data) {
    var self = this;
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('update-user'),
        'data' : data,
        'success': function(response){
            /* If the response is an array, it is a list of users that can be passed to this.drawList() */
            if (response instanceof Array) {
                var html = self.drawList(response);
                $('.user-group-list').html(html);
                $('#individual-user').html('');
            } else {
                /* If it is an object, we have an error on our hands */
                self.serverErrorHandler(response);
            }
        },
        'error': function(r, status, error){
            alert('The server returned the following error: ' + status + '(' + error + ')');
        }
    });
};

/**
 * Load all users for a given group identified by id
 * @param id
 */
UserObject.prototype.loadFromGroup = function(id){
    var self = this;
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('trip-load-users-for-group', {id: id}),
        'success': function(response){
            var html = self.drawList(response);
            $('.user-group-list').html(html);
        },
        'error': function(r, status, error){
            alert('The server returned the following error: ' + status + '(' + error + ')');
        }
    })
};


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
    var uo = new UserObject();
    uo.update(data);
}

function loadUsersForGroup(group){
    var uo = new UserObject();
    uo.loadFromGroup(group);
}

function loadUser(user){
    var uo = new UserObject();
    uo.load(user);
}