var App = new Object();

App.resetCloud = function(){
    var bw = $("#content-cloud").width();
    var bh = $("#content-cloud").height();
    $(".section-cloud").each(function(i){
        var posx = Math.round(Math.random() * bw)-10;
        var posy = Math.round(Math.random() * bh)-10;
        $(this).css("top", posy + "px").css("left", posx + "px");
    });
}

App.loadSection = function(section_id) {
    $.ajax({
        url : Routing.generate( 'contents_section_ajax_load' ),
        data : {id : section_id},
        type: "POST",
        success: function(data){
            App.setSection(data);
        },
        error: function(){
            alert("Something went wrong...")
        }
    })
}

App.setSection = function(data) {
    $('#section-meta-information').html(data);
}

App.editRecord = function(id) {
    App.current_record = id;
    if(id!=0){
        // todo hier entry opvragen ...
        $.ajax({
            url : Routing.generate( 'load_record_ajax' ),
            data : {id : id},
            type: "POST",
            success: function(data){
                App.doMapping(data);
            },
            error: function(){
                alert("Something went wrong...")
            }
        })
    }
    App.showEditScreen();
}

App.doMapping = function(entity_object){
    App.setValue('#entry-title', 'title', entity_object, 'text');
    App.setValue('#entry-description', 'text', entity_object, 'textarea');
    App.setItems(entity_object.items);
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
    if(input_type=="checkbox"){
        if(entity_object[object_property]){
            $(dom_id).prop('checked', true);
        }
        else {
            $(dom_id).prop('checked', false);
        }
    }
}



App.saveRecord = function() {
    $.ajax({
        url : Routing.generate( 'save_record_ajax' ),
        data : {
            id: App.current_record,
            section_id : App.current_section,
            description: $('#entry-description').val(),
            title: $('#entry-title').val(),
            items: App.getItems()
        },
        type: "POST",
        success: function(data){
            $.notify("Saved!", { className: "info", globalPosition: 'top center' } );
            App.getRecordList(App.current_section);
        },
        error: function(){
            alert("Something went wrong...")
        }
    })

    App.hideEditScreen();
}

App.getRecordList = function(section_id) {
    $.ajax({
        url : Routing.generate( 'contents_records_ajax_load' ),
        data : {id : section_id},
        type: "POST",
        success: function(data){
            $('#contents-section-records').html(data);
        },
        error: function(){
            alert("Something went wrong...")
        }
    })

}


App.getItems = function() {
    var items;
    $('.item-row').each(function(){
        var item_id = App.getItemId($(this).attr('id'));
        var value = $('#val_'+ item_id).val();
        var type = $("#type_" + item_id).val();
        var resource;
        if (type == 'media'){
            resource = App.getMedia("#med_" + item_id + " img");
        }
        if (type == 'url') {
            resource = $("#res_" + item_id).val();
        }

        var row = {value: value, resource: resource, type: type}
        if(items instanceof Array){
            items.push(row);
        }
        else{
            items = new Array();
            items.push(row);
        }

    });
    return items;
}


App.setCurrentItem = function(item_id){
    App.current_item = item_id;
}

// todo josn: refactor in one function
App.showEditScreen = function(){
    $('#edit-entry').removeClass('hidden');
    $('#all-records').addClass('hidden');
    $('#media-entry').addClass('hidden');
    $('#modal-title').html('Edit record');


}

App.hideEditScreen = function(){
    $('#edit-entry').addClass('hidden');
    $('#all-records').removeClass('hidden');
    $('#media-entry').addClass('hidden');
    $('#modal-title').html('Complete section');
}


App.showMediaScreen = function(){
    $('#media-entry').removeClass('hidden');
    $('#all-records').addClass('hidden');
    $('#edit-entry').addClass('hidden');
    $('#modal-title').html('Add media to record');

}

App.hideMediaScreen = function(){
    $('#media-entry').addClass('hidden');
    $('#edit-entry').removeClass('hidden');
    $('#all-records').removeClass('hidden');
    $('#modal-title').html('Edit record');
}

App.setMedia = function(media){
    $('.media-file.active').each(function(){
        $(this).removeClass('active');
    })
    for(file in media){
        $('[data-id="'+ media[file].id +'"]').addClass('active');
    }
}


App.getMedia = function(selector){
    if ( selector == undefined ) { selector = ".media-file.active"};

    var media = new Array();
    $(selector).each(function(){
        var item;
        item = {id: $(this).attr('data-id'), src: $(this).attr('src')}
        media.push(item);
    })
    return media;
}



App.addItemRow = function(item_value, item_resource, item_type)
{
    if (item_value == undefined) { item_value = ""};
    if (item_resource == undefined) { item_resource = ""};
    if (item_type == undefined) { item_type = "url"};


    var random_number = Math.floor(Math.random()*10001)
    var item_id = $.now() + random_number;

    var url_selected, url_class, media_selected, media_class = " ";
    if(item_type=="url"){
        url_selected = "selected";
        media_class = "hidden";
    }
    else {
        media_selected = "selected";
        url_class = "hidden";
    }
    var newRow = "";

    newRow += "            <tr class=\"item-row\" id=\"item_"+ item_id +"\">";
    newRow += "                <td>";
    newRow += "                    <input type=\"text\" id=\"val_"+ item_id +"\" class=\"form-control item-key\"  value=\""+ item_value +"\">";
    newRow += "                <\/td>";
    newRow += "                <td>";
    newRow += "                    <select id=\"type_"+ item_id +"\" class=\"form-control item-type\">";
    newRow += "                       <option value=\"media\" "+ media_selected +">Media</option>";
    newRow += "                       <option value=\"url\" "+ url_selected +">Url</option>";
    newRow += "                    </select>";
    newRow += "                <\/td>";

    newRow += "                <td>";
    newRow += "                    <input type=\"text\" id=\"res_"+ item_id +"\" class=\"form-control item-resource " + url_class + "\"  value=\""+ item_resource +"\" placeholder=\"http://\" data-type=\""+ item_resource +"\">";
    newRow += "                    <div id=\"med_"+ item_id +"\" class=\"" + media_class + " \">";
    newRow += "                    <div class=\"selected-media\"><\/div>";
    newRow += "                    <a title=\""+ item_id +"\" class=\"btn btn-sm btn-theme btn-select-media\">Select media<\/a>";
    newRow += "                    <\/div>";
    newRow += "                <\/td>";

    newRow += "                <td>";
    newRow += "                    <button class=\"btn btn-primary btn-embossed btn-danger btn-sm btn-delete-item\" id=\"delete_item_"+ item_id +"\">";
    newRow += "                        X";
    newRow += "                    <\/button>";
    newRow += "                <\/td>";
    newRow += "            <\/tr>";

    $('#items-body').append(newRow);

    // set resource
    if(item_type=="media"){
        $("#res_"+ item_id ).val("");
        App.createSelectedMediaBlock(item_id, item_resource);
    }


}


App.removeItemRow = function(item_id)
{
    $('#item_' + item_id).remove();
}

App.getItemId = function(id_string)
{
    return  id_string.split("_").pop();
}

App.setItems = function(items){
    $('#items-body').html("");
    for (item in items){
        App.addItemRow(items[item]['value'], items[item]['resource'], items[item]['type']);
    }

}

App.createSelectedMediaBlock = function(item, files) {
    $('#med_'+ item +' .selected-media').html("");
    for (file in files) {
        $('#med_'+ item +' .selected-media').append('<img data-id="'+ files[file].id +'" class="img-thumbnail" id="'+ files[file].id +'" src="'+ files[file].src +'"/>');
    }
}

App.changeState = function(){
    $.notify("Saving ... ", { className: "info", globalPosition: 'top center' } );
    $.ajax({
        url : Routing.generate('change_state_ajax'),
        data : {id:App.current_section, state: $('#section-ready').prop('checked') },
        type: 'POST',
        success: function(data){
            App.doMapping(data);
            $.notify("Section saved", { className: "success", globalPosition: 'top center' } );

            if($('#section-ready').prop('checked')){
                $('.section-cloud[title='+ App.current_section +']').addClass('section-ready ');
            }
            else {
                $('.section-cloud[title='+ App.current_section +']').removeClass('section-ready ');
            }

            //App.reloadSections($('#save-section').attr('data-parent-id'));
        },
        error: function(){
            $.notify("Something went wrong", { className: "error", globalPosition: 'top center' } );
        }
    })
}

App.setState = function(){
    $.ajax({
        url : Routing.generate('get_state_ajax'),
        data : { id: App.current_section },
        type: 'POST',
        success: function(data){
            if(data=="true"){
                $('#section-ready').prop('checked', true);
                $('.section-cloud[title='+ App.current_section +']').addClass('section-ready ');
            }
            else {
                $('#section-ready').prop('checked', false);
            }
        },
        error: function(){
            $.notify("Something went wrong", { className: "error", globalPosition: 'top center' } );
        }
    })
}

App.resetEditScreen = function(){
    $('#entry-title').val("");
    $('#entry-description').val("");
    $('#items-body').html("");
}

$(document).ready(function(){
    $('#new-row').on('click', function(){
        App.addItemRow();
    });

    App.resetCloud();
    $('#reset-cloud').on('click', function(){
        App.resetCloud();
    });
    $('.section-cloud').drags();

    $('.section-cloud').on('dblclick', function(){
        App.loadSection($(this).attr('title'));
        App.getRecordList($(this).attr('title'));
        App.current_section = $(this).attr('title');
        $('#modal-edit-section').modal();
        App.hideEditScreen();
        App.setState();

    })

    $('#contents-section-records').on('click', '.btn-edit-entry', function(){
        App.resetEditScreen();
        App.editRecord($(this).attr('title'));
    });


    $('#items').on('change', '.item-type', function(){
        var id = App.getItemId($(this).attr('id'));

        if($(this).val() == 'media') {
            $('#res_' + id).addClass('hidden');
            $('#med_' + id).removeClass('hidden');
        }
        if($(this).val() == 'url') {
            $('#res_' + id).removeClass('hidden');
            $('#med_' + id).addClass('hidden');
        }
    });


    $('#items').on('click', '.btn-select-media', function(){

        var item_id = $(this).attr('title');
        App.setCurrentItem(item_id);
        App.setMedia(App.getMedia("#med_" + item_id + " img"));
        App.showMediaScreen();
    });

    $('.btn-back-to-edit').on('click', function(){
        App.showEditScreen();
        App.createSelectedMediaBlock(App.current_item, App.current_item_files);
    });

    $('.media-file').on('click', function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
        }
        else {
            $(this).addClass('active');
        }
        App.current_item_files = App.getMedia('.media-file.active');
    });

    $('#items-body').on('click', '.btn-delete-item', function(){
        var confirm = window.confirm("Are you sure you want to delete this record?");
        if(confirm){
            App.removeItemRow(App.getItemId($(this).attr('id')));
        }
    });

    $('#btn-return-entries').on('click', function(){
        App.saveRecord();
    });

    $('#btn-contents-save-section').on('click', function(){
        App.changeState();
    });

});

