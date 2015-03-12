var App = {};

App.getState = function() {
    var dataObject             = new Object();
    dataObject.id              = $('#save-section').attr('data-id');
    dataObject.tripid          = $('#save-section').attr('data-parent-id');
    dataObject.title           = $('#title').val();
    dataObject.description     = $('#description').val();
    dataObject.parameters      = App.getParameters();

    return dataObject;
}

App.loadSection = function(section_id) {
    $.ajax({
        url : Routing.generate( 'section_ajax_load' ),
        data : {id : section_id},
        type: "POST",
        success: function(data){
            App.doMapping(data);
        },
        error: function(){
            alert("Something went wrong...")
        }
    })

}

App.deleteSection = function() {
    var confirmDelete = window.confirm("Are you sure?");
    if(confirmDelete){
        $.ajax({
            type: 'post',
            url: Routing.generate( 'section_delete', { id: $('#save-section').attr('data-id') }),
            success: function(data){
                if(data=="ok"){
                    window.location = Routing.generate('section');
                }
            }

        });
    }
}

App.saveSection = function(){
    $.notify("Saving ... ", { className: "info", globalPosition: 'top center' } );
    $.ajax({
        url : Routing.generate('section_ajax_save'),
        data : App.getState(),
        type: 'POST',
        success: function(data){
            App.doMapping(data);
            $.notify("Section saved", { className: "success", globalPosition: 'top center' } );
            App.reloadSections($('#save-section').attr('data-parent-id'));
        },
        error: function(){
            $.notify("Something went wrong", { className: "error", globalPosition: 'top center' } );
        }
    })
}

App.reloadSections = function(trip_id){
    $.ajax({
        url: Routing.generate('trip_load_sections'),
        data: {id : trip_id},
        type: 'POST',
        success: function(data){
            $('#sections-trip').html(data);
        },
        error: function(){
            $.notify("Something went wrong", { className: "error", globalPosition: 'top center' } );
        }
    })
}

App.setValue = function(dom_id, object_property, entity_object, input_type ){

    //error handling
    if($(dom_id).length==0){
        console.log("Element "+ dom_id + " does not exist...");
        return false;
    }
    if(!entity_object.hasOwnProperty(object_property)){
        console.log("Entity does not have property " + object_property);
        return false;
    }
    // set values
    if(input_type=="text"){
        $(dom_id).val(entity_object[object_property]);
    }
    if(input_type=="textarea"){
        $(dom_id).val(entity_object[object_property]);
    }
    if(input_type=="parameter-block"){
        $(dom_id).val(entity_object[object_property]);
    }

}

App.getParameters = function() {
    var parameters;
    $('.parameter-row').each(function(){
        var parameter_id = App.getParameterId($(this).attr('id'));
        var value = $('#val_'+ parameter_id).val();
        var type = $("#type_" + parameter_id).val();
        var challenge = $("#challenge_" + parameter_id).val();
        var resource;
        if (type == 'media'){
            resource = App.getMedia("#med_" + parameter_id + " img");
        }
        if (type == 'url') {
            resource = $("#res_" + parameter_id).val();
        }

        var row = {value: value, resource: resource, type: type, challenge: challenge}
        if(parameters instanceof Array){
            parameters.push(row);
        }
        else{
            parameters = new Array();
            parameters.push(row);
        }

    });
    return parameters;
}

App.setParameters = function(parameters){
    $('#parameters-body').html("");
    for (parameter in parameters){
        App.addParameterRow(parameters[parameter]['value'], parameters[parameter]['resource'], parameters[parameter]['type'], parameters[parameter]['challenge']);
    }

}

App.setMedia = function(media){
    $('.media-file.active').each(function(){
        $(this).removeClass('active');
    })
    for(file in media){
        $('[data-id="'+ media[file].id +'"]').addClass('active');
    }
}

App.setId = function(id){
    $('#save-section').attr('data-id', id);
}


App.getMedia = function(selector){
    if ( selector == undefined ) { selector = ".media-file.active"};

    var media = new Array();
    $(selector).each(function(){
        var item;
        item = {id: $(this).attr('data-id'), src: $(this).attr('src')};
        media.push(item);
    })
    return media;
}


App.doMapping = function(entity_object){
    App.setValue('#title', 'title', entity_object, 'text');
    App.setValue('#description', 'description', entity_object, 'textarea');

    App.setParameters(entity_object.parameters);
    //App.setMedia(entity_object.media);
    App.setId(entity_object.id);
}

App.removeParameterRow = function(parameter_id)
{
    $('#parameter_' + parameter_id).remove();
}

App.getParameterId = function(id_string)
{
    return  id_string.split("_").pop();
}


App.addParameterRow = function(parameter_value, parameter_resource, parameter_type, parameter_challenge)
{
    if (parameter_value == undefined) { parameter_value = ""};
    if (parameter_resource == undefined) { parameter_resource = ""};
    if (parameter_type == undefined) { parameter_type = "url"};
    if (parameter_challenge == undefined) { parameter_challenge = ""};



    var random_number = Math.floor(Math.random()*10001)
    var parameter_id = $.now() + random_number;

    var url_selected, url_class, media_selected, media_class = " ";
    if(parameter_type=="url"){
        url_selected = "selected";
        media_class = "hidden";
    }
    else {
        media_selected = "selected";
        url_class = "hidden";
    }
    var newRow = "";
    
    newRow += "            <tr class=\"parameter-row\" id=\"parameter_"+ parameter_id +"\">";
    newRow += "                <td>";
    newRow += "                    <label for=\"val_"+ parameter_id +"\">Label</label>";
    newRow += "                    <input type=\"text\" id=\"val_"+ parameter_id +"\" class=\"form-control parameter-key\"  value=\""+ parameter_value +"\">";
    newRow += "                    <label for=\"challenge_"+ parameter_id +"\">Challenge</label>";
    newRow += "                    <input type=\"text\" id=\"challenge_"+ parameter_id +"\" class=\"form-control parameter-challenge\"  value=\""+ parameter_challenge +"\">";
    newRow += "                <\/td>";
    newRow += "                <td>";
    newRow += "                    <select id=\"type_"+ parameter_id +"\" class=\"form-control parameter-type\">";
    newRow += "                       <option value=\"media\" "+ media_selected +">Media</option>";
    newRow += "                       <option value=\"url\" "+ url_selected +">Url</option>";
    newRow += "                    </select>";
    newRow += "                <\/td>";

    newRow += "                <td>";
    newRow += "                    <input type=\"text\" id=\"res_"+ parameter_id +"\" class=\"form-control parameter-resource " + url_class + "\"  value=\""+ parameter_resource +"\" placeholder=\"http://\" data-type=\""+ parameter_resource +"\">";
    newRow += "                    <div id=\"med_"+ parameter_id +"\" class=\"" + media_class + " \">";
    newRow += "                    <div class=\"selected-media\"><\/div>";
    newRow += "                    <a title=\""+ parameter_id +"\" class=\"btn btn-sm btn-theme btn-select-media\">Select media<\/a>";
    newRow += "                    <\/div>";
    newRow += "                <\/td>";

    newRow += "                <td>";
    newRow += "                    <button class=\"btn btn-primary btn-embossed btn-danger btn-sm btn-delete-parameter\" id=\"delete_parameter_"+ parameter_id +"\">";
    newRow += "                        X";
    newRow += "                    <\/button>";
    newRow += "                <\/td>";
    newRow += "            <\/tr>";

    $('#parameters-body').append(newRow);

    // set resource
    if(parameter_type=="media"){
        $("#res_"+ parameter_id ).val("");
        App.createSelectedMediaBlock(parameter_id, parameter_resource);
    }
}

App.switchTabs = function(tab_id){

    // reset
    $('.nav-tabs li').each(function(){
        $(this).removeClass('active');
    });
    $('.tab-content .tab-pane').each(function(){
        $(this).removeClass('active');
    });

    // set tab

    $('a[href="#'+ tab_id + '"]').parent().addClass('active');
    $('#' + tab_id).addClass('active');

};

App.setCurrentParameter = function(parameter_id){
    App.current_parameter = parameter_id;
}

App.createSelectedMediaBlock = function(parameter, files) {
    $('#med_'+ parameter +' .selected-media').html("");
    for (file in files) {
        $('#med_'+ parameter +' .selected-media').append('<img data-id="'+ files[file].id +'" class="img-thumbnail" id="'+ files[file].id +'" src="'+ files[file].src +'"/>');
    }
}




$(document).ready(function(){
    // events
    $('#new-row').on('click', function(){
        App.addParameterRow();
    });

    $('#edit-section').on('click', '.btn-delete-parameter', function(){
        App.removeParameterRow(App.getParameterId($(this).attr('id')));
    });

    $('#save-section').on('click', function(){
        App.saveSection();
    });

    $('.btn-delete-section').on('click', function(){
        App.deleteSection();
    });

    $('.media-file').on('click', function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
        }
        else {
            $(this).addClass('active');
        }
        App.current_parameter_files = App.getMedia('.media-file.active');
    });

    $('#sections-container').on('click', '.btn-edit-section', function(){
        App.loadSection($(this).attr('title'));
        App.switchTabs('edit_section');
        $('#edit-section').modal();
    });

    $('.btn-add-section').on('click', function(){
        App.switchTabs('edit_section');
        App.setId(0);
    });

    $('#parameters').on('click', '.btn-select-media', function(){
        var parameter_id = $(this).attr('title');
        App.setCurrentParameter(parameter_id);
        App.setMedia(App.getMedia("#med_" + parameter_id + " img"));
        App.switchTabs('media');
    });

    $('#parameters').on('change', '.parameter-type', function(){
        var id = App.getParameterId($(this).attr('id'));

        if($(this).val() == 'media') {
            $('#res_' + id).addClass('hidden');
            $('#med_' + id).removeClass('hidden');
        }
        if($(this).val() == 'url') {
            $('#res_' + id).removeClass('hidden');
            $('#med_' + id).addClass('hidden');
        }
    });

    $('.btn-back-to-edit').on('click', function(){
        App.switchTabs('edit_section');
        App.createSelectedMediaBlock(App.current_parameter, App.current_parameter_files);
    });

});






