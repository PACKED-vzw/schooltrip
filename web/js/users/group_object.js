/**
 * Created by pieter on 11/08/15.
 */

var GroupObject = function(){
    this.hh = new handlebar_helper();
};

/**
 * Draw an edit form for groups
 * @param group
 * @returns {string}
 */
GroupObject.prototype.drawEdit = function(group){
    return this.hh.draw_template_from_object(group, 'template-group-edit');
};

/* Compatibility functions */
function drawEditGroup(group){
    var go = new GroupObject();
    return go.drawEdit(group);
}


function getGroups(){
    var groups = [];
    $('.trip-group').each(function(){
        var group = {};
        group.id   = $(this).attr('data-id');
        group.name = $(this).find('.groupDescription').html();
        groups.push(group);
    });
    return groups;
}

$('.modaleditbutton').on('click', function(){

});

function loadBaseGroups(){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('load-groups'),
        'success': function(groups){
            var html = drawGroups(groups);
            $('.group-list').html(html);
        },
        'error': function(){
            alert("An error has occurred. Please try again...");
        }
    });
}

function deleteGroup(id){
    $.ajax({
        'type' : 'POST',
        'url'  : Routing.generate('delete-group', {id: id}),
        'success': function(groups){
            alert('Group deleted');
            var html = drawGroups(groups);
            $('.group-list').html(html);
        },
        'error': function(){
            alert("An error has occurred. Please try again...");
        }
    });
}
