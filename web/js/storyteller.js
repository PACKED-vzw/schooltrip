var App = new Object();

App.loadRecord = function(id, tab_id, remarks){
    if ( remarks == undefined ) { remarks = ""};
    if(id!=0){
        App.active_record = id;
        // todo hier entry opvragen ...
        $.ajax({
            url : Routing.generate( 'storyteller_load_record_html' ),
            data : {id : id, remarks: remarks},
            type: "POST",
            success: function(data){
                App.drawRecord(data, '#record_'+ tab_id +'_' + id);

            },
            error: function(){
                alert("Something went wrong...")
            }
        })
    }
}

App.drawRecord = function(data, selector){
    $(selector).html(data);
}

App.addExtraToRecord = function(html_data, tab_id){
    var html = '';
    html += '<div class="well">';
    html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    html += '' +html_data + '</div>';
    $('#tab_extra_' + App.tab_id).append(html);

}

App.loadTab = function(tab_id){
    $.ajax({
        url : Routing.generate( 'journal_load_tab' ),
        data : {id : tab_id},
        type: "POST",
        success: function(data){
            App.doMapping(data);
        },
        error: function(){
            alert("Something went wrong...")
        }
    })
}


App.drawExtraImage = function(image_src, image_id, title, description, tab_id){
    if ( title == undefined ) { title = ""};
    if ( description == undefined ) { description = ""};

    var html="";
    html += "<div data-type=\"image\" class=\"extra-content media\">";
    html += "  <a class=\"pull-left\" href=\"#\">";
    html += "    <img class=\"media-object image-input\" src=\""+ image_src + " \" title=\""+ image_id +"\" >";
    html += "  <\/a>";
    html += "  <div class=\"media-body\">";
    html += "    <h4 class=\"media-heading\"><input class=\"form-control title-input\" value=\""+ title +"\" type=\"text\" placeholder=\"Enter title for image...\"><\/h4>";
    html += "       <textarea class=\"form-control description-input\" placeholder=\"Enter description for image...\">"+ description +"</textarea>";
    html += "  <\/div>";
    html += "<\/div>";
    html += "";
    App.addExtraToRecord(html, tab_id);

}

App.drawExtraIframe = function(title, code, description, tab_id){
    var html="";
    html += "<div data-type=\"iframe\" class=\"extra-content media\">";
    html += "  <div class=\"pull-left code-input\">";
    html +=  code;
    html += "  <\/div>";
    html += "  <div class=\"media-body\">";
    html += "    <h4 class=\"media-heading\"><input class=\"form-control title-input\" type=\"text\" value=\"" + title + "\" placeholder=\"Enter title for content...\"><\/h4>";
    html += "       <textarea class=\"form-control description-input\"  placeholder=\"Enter description for content...\">"+ description +"</textarea>";
    html += "  <\/div>";
    html += "<\/div>";
    html += "";
    App.addExtraToRecord(html, tab_id);
}

App.doMapping = function(data){
    $('#tab_records_selected_'+data.id).html("");
    $('#tab_extra_'+data.id).html("");

    for (content in data.content.extra){
        if(data.content.extra[content].type=="image"){
            App.drawExtraImage(data.content.extra[content].image_src, data.content.extra[content].image_id, data.content.extra[content].title, data.content.extra[content].description, data.id )
        }
        else if(data.content.extra[content].type=="iframe"){
            App.drawExtraIframe( data.content.extra[content].title, data.content.extra[content].code, data.content.extra[content].description, data.id )
        }
    }
    for (record in data.content.records){
        App.loadRecord(data.content.records[record].id, data.content.records[record].remarks);
    }
}


$(document).ready(function(){
    $('.storyteller-record').each(function(){
        App.loadRecord($(this).attr('data-record-id'), $(this).attr('data-tab-id'), $(this).attr('data-remarks'));
        App.loadTab($(this).attr('data-tab-id'));

    });

});
