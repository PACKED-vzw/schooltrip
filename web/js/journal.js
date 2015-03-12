var App = new Object();

App.setDateTabHeader = function(dom_id, value, dateFormat){
    $(dom_id).html(value).attr('data-format',dateFormat);
}

App.saveTab = function(tab_id){
    $.notify("Saving ...", { className: "info", globalPosition: 'top center' } );

    $.ajax({
        url : Routing.generate( 'journal_save_tab' ),
        data : App.getTabData(tab_id),
        type: "POST",
        success: function(data){
            $.notify("Saved!", { className: "success", globalPosition: 'top center' } );
        },
        error: function(){
            alert("Something went wrong...")
        }
    })
}

App.loadTab = function(tab_id){
    $.ajax({
        url : Routing.generate( 'journal_load_tab' ),
        data : {id : tab_id},
        type: "POST",
        success: function(data){
            App.records_state = data;
            App.doMapping(data);
        },
        error: function(){
            alert("Something went wrong...")
        }
    })
}

App.doMapping = function(data){
    $('#tab_records_selected_' + App.active_tab +'_' + data.id).html("");
    $('#tab_extra_'+ App.active_tab +'_' + data.id).html("");


    for (record in data.content.records){
        App.loadRecord(data.content.records[record].id, data.content.records[record].remarks, data.content.records[record].time);
    }
}

App.getTabData = function(tab_id){
    var data = new Object();
    data.date = $('#media_tab_'+ tab_id).attr('data-format');
    data.id = tab_id;
    data.contents = App.setContentsState(tab_id);
    return data;
}

App.loadRecord = function(id, remarks, time){
    if ( remarks == undefined ) { remarks = ""};
    if ( time == undefined ) { time = ""};
    if(id!=0){
        // todo hier entry opvragen ...
        $.ajax({
            url : Routing.generate( 'journal_load_record_html' ),
            data : {id : id, remarks: remarks, tab_id: App.active_tab , time: time},
            type: "POST",
            success: function(data){
                App.drawRecord(data, '#tab_records_selected_' + App.active_tab);

            },
            error: function(){
                alert("Something went wrong...")
            }
        })
    }
}


// get extra content for record for saving
App.setContentsState = function(tab_id){

    var contents = new Object();
    contents.records = new Array();
    $('#tab_'+ tab_id +' .tab_record').each(function(){
        var record = new Object();
        record.id = $(this).attr('title');
        record.remarks = $('#remarks_' + tab_id + '_' + record.id).val();
        record.time = $('#time_' + tab_id + '_' + record.id).val();

        var extra = new Array();

        $('#tab_' + tab_id +' .extra-content').each(function(){
            var content = new Object;
            content.type = $(this).attr('data-type');
            content.title = $(this).find('.title-input').val();
            content.description = $(this).find('.description-input').val();
            if(content.type=="iframe"){
                content.code = $(this).find('.code-input').html();
            }
            else if(content.type=="image"){
                content.image_id = $(this).find('.image-input').attr('title');
                content.image_src = $(this).find('.image-input').attr('src');
            }
            extra.push(content);

        });
        record.extra = extra;
        contents.records.push(record);

    });

    return contents;
}

/*
App.drawSchedule = function(){
    var records = App.records_state.content.records;
    for(record in records){
        var selector = '#record-heading-'+ App.active_tab +'-'+ records[record].id;

        var html = '<div class="panel panel-default">';
        html +=    '<div class="panel-heading record-heading">';

        html +=     $(selector).html();

        html +=    '</div>';
        html +=    '<div class="panel-body record-body">';


        html +=    '<label class="inline-label" for="schedule-time-'+ App.active_tab +'-'+ records[record].id +'">Hour</label>';
        html +=    '<input id="schedule-time-'+ App.active_tab +'-'+ records[record].id +'" type="text"  class="form-control schedule-time" placeholder="00:00">';



        html +=    '</div>';
        html +=    '</div>';

    }

    alert(html);
    console.log(html);



    return html;
}
*/

App.drawRecord = function(data, selector){




    $(selector).append('<li>' + data +'</li>');
    $( selector ).sortable();
    //$('.schedule').append(App.drawSchedule());
}

App.addExtraToRecord = function(html_data){
    if(App.active_record==undefined){
        alert("There is no record active!");
    }
    else {
        var html = '';
        html += '<div class="well">';
        html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        html += '' +html_data + '</div>';
        $('#tab_extra_'+ App.active_tab +'_' + App.active_record).append(html);
        return true;
    }
}


App.isRecordAlreadyAdded = function(record_id){
    for(record in App.records_state.content.records){
        if(record_id==App.records_state.content.records[record].id){
            return true;
        }
    }
    return false;
}

// load all extras for active record
App.setActiveRecord = function(record_id){
    for(record in App.records_state.content.records){
        if(record_id==App.records_state.content.records[record].id){
            var extra = App.records_state.content.records[record].extra
            for (content in extra){
                if(extra[content].type=="image"){
                    App.drawExtraImage(extra[content].image_src, extra[content].image_id, extra[content].title, extra[content].description )
                }
                else if(extra[content].type=="iframe"){
                    App.drawExtraIframe( extra[content].title, extra[content].code, extra[content].description )
                }
            }

        }
    }
}


App.drawExtraImage = function(image_src, image_id, title, description){
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
    App.addExtraToRecord(html);

}

App.drawExtraIframe = function(title, code, description){
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
    App.addExtraToRecord(html);
}


App.closeAllRecords = function(){
    $('.record-heading').each(function(){
        $(this).removeClass('active-record');

    });
    $('.record-body').each(function(){
        $(this).addClass('hidden');
    });
    App.active_record = undefined;
}

App.enableRecord = function(record_id, tab_id){
    App.active_record = record_id;
    App.setActiveRecord(App.active_record);

    $('#record-body-'+ tab_id +'-' + record_id).removeClass('hidden');
    $('#record-heading-'+ tab_id +'-' + record_id).addClass('active-record');
    /*
    if($('#record-heading-'+ tab_id +'-' + record_id).hasClass('active-record')){
        $('#record-body-'+ tab_id +'-' + record_id).addClass('hidden');
        $('#record-heading-'+ tab_id +'-' + record_id).removeClass('active-record');
        App.active_record = undefined;



    }
    else {
        App.active_record = record_id;
        App.setActiveRecord(App.active_record);

        $('#record-body-'+ tab_id +'-' + record_id).removeClass('hidden');
        $('#record-heading-'+ tab_id +'-' + record_id).addClass('active-record');

    }
    */

}

$(document).ready(function(){
    $('#journal-media-block').flipster();

    $('#btn-new-journal').on('click', function(){
        //App.createNewJournal();
    });

    $('.datepicker-field').datepicker({
        format: "dd/mm/yyyy"
    });


    $('.datepicker-field').datepicker()
        .on('changeDate', function(e){
           App.setDateTabHeader($(this).attr('data-target'), e.format('DD dd MM yyyy'), e.format('yyyy-mm-dd'));
    });

    $('.btn-save-tab').on('click', function(){
        App.saveTab($(this).attr('title'));
    });

    $(".select2-records").select2({
        placeholder: "Select record"

    });

    $(".select2-records").on('change', function(){

        if(App.isRecordAlreadyAdded($(this).val())){
            alert('Record was already added');
        }
        else {
            App.loadRecord($(this).val());
            alert('Record added!');
        }

    });

    $('.btn-add-extra').on('click', function(){
    });

    $('.mediacoverflow').on('dblclick', function(){
        App.drawExtraImage($(this).attr('src'), $(this).attr('data-id'));
        alert("Content added to tab!");

    });

    $('#btn-add-extra').on('click', function(){
        App.drawExtraIframe($('#title-extra').val(),$('#embed-code-extra').val(), $('#embed-description-extra').val());
        alert("Content added to tab!");

    });

    $('.journal-tab').on('click', function(){
        App.active_tab = $(this).attr('title');
        App.loadTab($(this).attr('title'));

    });

    tinymce.init({
        selector: ".wysiwyg"
    });

    $('.tab_records_selected_ctn').on('click', '.record-heading', function(){
        var record_id = $(this).attr('title');
        if($(this).hasClass('active-record')){
            App.closeAllRecords();
        }
        else {
            App.enableRecord(record_id, App.active_tab);
        }
    });

    $('.tab_records_selected_ctn').on('click', '.close-record', function(){
        alert('Record is being deleted!');
    });




});


