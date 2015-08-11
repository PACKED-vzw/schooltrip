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
