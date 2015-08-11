/**
 * Created by pieter on 11/08/15.
 */
/**
 * Handlebar helper functions
 * hh.draw_template_from_object(template, object)
  */
var handlebar_helper = function(){};

/**
 * Function to parse an object into a html template using Handlebars
 * @param obj: the object to parse as parameter to the Handlebars template()-function
 * @param template: the id of the Handlebars template (without the "#")
 * @returns {string}
 */
handlebar_helper.prototype.draw_template_from_object = function(obj, template){
    var source = $("#" + template).html();
    var hb_template = Handlebars.compile(source);
    return hb_template(obj);
};