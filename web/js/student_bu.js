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

Handlebars.registerHelper('escape', function(variable) {
    return variable.replace(/(['"])/g, '\\$1');
});




function initialize(){
    resetSettings();
}

// initialise app
SchooltripApp = {};

initialize();

function resetSettings(){
    SchooltripApp.currentTrip    = 0;
    SchooltripApp.currentSection = 0;
    SchooltripApp.currentRecord  = 0;
    SchooltripApp.currentDate    = 0;
    SchooltripApp.currentSectionDom = null;
    SchooltripApp.sectionView    = 'list';
    SchooltripApp.journal        = $('.main-journal').attr('data-journal');
}

$( document ).ajaxStart(function() {
    $('#loading-modal').css('display', 'block');
});

$( document ).ajaxComplete(function() {
    $('#loading-modal').css('display', 'none');
});

function drawRecord(record){
    var source   = $("#row-template").html();
    var template = Handlebars.compile(source);

    return template(record);
}

function loadRecords(section_dom_id, section_id){
    var list = $(section_dom_id);
    // clear list
    list.html('');
    $.ajax({ 'type' : 'post',
        'url' : Routing.generate('student_load_records', {'id' : section_id}),
        'success' : function(records){
            for (var record in records){
                if(records.hasOwnProperty(record)){
                    var html = drawRecord(records[record]);
                    list.append(html);
                }
            }
        }
    });
}


function saveRecord(id, title, description, url ){
    $.ajax({
        'url' : Routing.generate('student_save_record', {id : id}),
        'data': {
            'title' : title,
            'description' : description,
            'url' : url
        },
        'type': 'post',
        'success': function(data){
            alert("Record saved");
            loadRecords(SchooltripApp.currentSectionDom, SchooltripApp.currentSection);
        },
        error: function(){
            alert('An error occurred!');
        }
    });
}

function createNewRecord(section_id){
    $.ajax({
        'url' : Routing.generate('student_new_record', {id : section_id}),
        'type': 'post',
        'success': function(data){
            alert("Record added!");
            loadRecords(SchooltripApp.currentSectionDom, SchooltripApp.currentSection);
        },
        error: function(){
            alert('An error occurred!');
        }
    });
}

function markSectionAsReady(section_id){
    $.ajax({
        'url' : Routing.generate('mark_section_as_ready', {id : section_id}),
        'type': 'post',
        'success': function(data){
            location.reload();
        },
        error: function(){
            alert('An error occurred!');
        }
    });
}


function drawImages(images){
    $('#record-images').html('');
    for (var img in images){
        if (images.hasOwnProperty(img)){
            var html = drawImage(images[img]);
            $('#record-images').append(html);
        }

    }
}

function deleteImageFromSection(recordId, imageId){
    var confirmed = confirm("Are you sure you want to delete?");
    if(confirmed){
        $.ajax({
            'url' : Routing.generate('remove_image_record'),
            'type': 'post',
            'data': {
                record_id: recordId,
                image_id: imageId
            },
            'success': function(images){
                drawImages(images);
            },
            error: function(){
                alert('An error occurred while deleting!');
            }
        });
    }
}


function drawImage(image){
    var source   = $("#record-image-template").html();
    var template = Handlebars.compile(source);

    return template(image);
}

function drawEditRecord(record){
    var source   = $("#open-record-template").html();
    var template = Handlebars.compile(source);

    return template(record);
}

function drawEuropeanaImages(images){
    $('#europeana-results').html('');
    $('#europeana-results').css('display','block');
    for (var img in images){
        if (images.hasOwnProperty(img)){
            var html = drawEuropeanaImage(images[img]);
            $('#europeana-results').append(html);
        }

    }
}


function drawEuropeanaImage(image){
    var source   = $("#europeana-image-template").html();
    var template = Handlebars.compile(source);

    return template(image);
}

function toggleEditRecord(id){
    var editRecord = $('#edit-record-' + id);
    var editButton  = $('#edit-record-button-' + id);
    var recordRow  = $('#record-row-' + id);

    if(editButton.hasClass('record-open')||editRecord.length){
        editRecord.remove();
        editButton.html('Edit');
        editButton.removeClass('record-open');
        recordRow.find('td').each(function(){
            $(this).removeClass('bluetemporary');
        });
    }
    else {
        editButton.html('Save');
        editButton.addClass('record-open');
        $.ajax({
            'type' : 'post',
            'url' : Routing.generate('student_load_record', {'id' : id}),
            'success' : function(record){
                var html = drawEditRecord(record);
                $(html).insertAfter(recordRow);
            }
        });
        recordRow.find('td').each(function(){
            $(this).addClass('bluetemporary');
        });
    }
}



function addJournalEntry(journalId, dateId, originalRecordId){
    $.ajax({
        'type' : 'post',
        'url'  : Routing.generate('add_journal_entry', {'id' : journalId}),
        'data' : {'date_id': dateId,
                  'original_record_id': originalRecordId
        },
        'success' : function(date){
            drawDate(date);
        },
        'error' : function(){
            alert("An error occurred!");
        }
    });

}


function editTimeJournalEntry(journalId, dateId, dateEntryId, hour){
    $.ajax({
        'type' : 'post',
        'url'  : Routing.generate('edit_time_journal_entry', {'id' : journalId}),
        'data' : {
            'date_id': dateId,
            'date_entry_id': dateEntryId,
            'hour': hour
        },
        'success' : function(date){
            drawDate(date);
        },
        'error' : function(){
            alert("An error occurred!");
        }
    });

}




function updateJournalEntry(journalId, data){
    $.ajax({
        'type' : 'post',
        'url'  : Routing.generate('update_journal_entry', {'id' : journalId}),
        'data' : data,
        'success' : function(data){
            alert("Changes saved!");
        },
        'error' : function(){
            alert("An error occurred!");
        }
    });
}

function deleteJournalEntry(journalId, dateId, entryId){
    $.ajax({
        'type' : 'post',
        'url'  : Routing.generate('delete_journal_entry', {'id' : journalId}),
        'data' : {
               'date_id' : dateId,
               'entry_id': entryId
            },
        'success' : function(date){
            drawDate(date);
        },
        'error' : function(){
            alert("An error occurred!");
        }
    });
}

function loadDateJournal(journalId, dateId){
    $.ajax({
        'type' : 'post',
        'url'  : Routing.generate('load_date_journals', {'id' : journalId}),
        'data' : {
            'date_id' : dateId
        },
        'success' : function(date){
            drawDate(date);
        },
        'error' : function(){
            alert("An error occurred!");
        }
    });
}




function drawDate(date){
    console.log(date);
    $('#date-record-boxes-' + SchooltripApp.currentDate).html('');
    for (entry in date.entries){
        if(date.entries.hasOwnProperty(entry)){
            var html = drawDateRecord(date.entries[entry]);
            $('#date-record-boxes-' + SchooltripApp.currentDate ).append(html);

        }
    }
    /*for (var i= 0; i < date.entries.length; i++){
        alert("in array");
        alert(drawDateRecord(date.entries[i]));
    }*/


    $('.time-picker').timepicker({'timeFormat': 'H:i' , 'show2400' : true});
}

function drawDateRecord(date){
    var source   = $("#date-record-template").html();
    var template = Handlebars.compile(source);

    return template(date);
}



function drawSelect(entries){
    $.ajax({ 'type' : 'post',
        'url' : Routing.generate('student_load_records', {'id' : section_id}),
        'success' : function(records){
            for (var record in records){
                var source   = $("#date-record-template").html();
                var template = Handlebars.compile(source);

                return template(entries);
            }
        }
    });
}


function europeanaSearch(query){
    var url = "http://europeana.eu/api/v2/search.json?wskey=jws8nziqw&query="+ query +"&start=1&rows=96&profile=standard";
    $.ajax({
        dataType: "json",
        url: url,
        success: function(data){
            console.log(data);
            drawEuropeanaImages(data.items);
        }
    });


}

function uploadEuropeanaImage(image){
    $.ajax({
        url: '/uploader.php',
        type: 'POST',
        data: image,
        success: function(response){
            console.log(response);

            $.ajax({
                type: 'POST',
                url: Routing.generate('add_image_record'),
                data: {
                    record_id: SchooltripApp.currentRecord,
                    image: response.filename,
                    thumbnail: response.thumbnail,
                    type: "europeana",
                    title: image.title,
                    link: image.link,
                    description: image.description
                },
                success: function(images){
                    drawImages(images);
                },
                error: function(){
                    alert("Something went wrong");
                }
            });


        },
        error: function(){
            alert("Somerhting went wrong while uploading!")
        }
    });
}


// reset current trip if aborted preliminary
$('div').on('hidden.bs.modal', function () {
    resetSettings();
});

$('.managerecords').on('click', function(){
    SchooltripApp.currentSection = $(this).attr('data-section');
    SchooltripApp.currentSectionDom = $(this).attr('data-id');
    console.log($(this).attr('data-id'));
    loadRecords($(this).attr('data-id'), SchooltripApp.currentSection);
});


$('document').on("click", ".edit-record-btn.record-open", function(){

});

$(document).on("click", ".edit-record-btn", function() {
    if($(this).hasClass('record-open')){
        var id = $(this).attr('data-id');
        var title = $('#record-title-' + id).val();
        var description = $('#record-description-' + id).val();
        var url =  $('#record-url-' + id).val();

        saveRecord( id, title, description, url );
    }
    else {
        var id = $(this).attr('data-id');
        // todo: change to correct settings
        toggleEditRecord( id );

    }
});


$(document).on('click', '.add-image-btn', function(){

    $('.add-image-btn').addClass('hidden');
    $('#new-image-btn').removeClass('hidden');

    SchooltripApp.currentRecord = $(this).attr('data-id');

    var sizeBox = document.getElementById('sizeBox'), // container for file size info
        progress = document.getElementById('progress'),// the element we're using for a progress bar
        button = document.getElementById('new-image-btn');

    var uploader = new ss.SimpleUpload({
        button: button, // file upload button
        url: '/uploader.php', // server side handler

        //url: Routing.generate('upload_image_section'), // server side handler
        name: 'uploadfile', // upload parameter name
        //progressUrl: 'uploadProgress.php', // enables cross-browser progress support (more info below)
        responseType: 'json',
        allowedExtensions: ['jpg', 'jpeg', 'png'],
        maxSize: 10240, // kilobytes
        hoverClass: 'ui-state-hover',
        focusClass: 'ui-state-focus',
        disabledClass: 'ui-state-disabled',
        onSubmit: function(filename, extension) {
            this.setFileSizeBox(sizeBox); // designate this element as file size container
            this.setProgressBar(progress); // designate as progress bar
        },
        onComplete: function(filename, response) {
            if (!response) {
                alert(filename + ' upload failed');
                return false;
            }
            alert("going to upload now!!");
            $.ajax({
                type: 'POST',
                url: Routing.generate('add_image_record'),
                data: {
                    record_id: SchooltripApp.currentRecord,
                    image: response.filename,
                    thumbnail: response.thumbnail
                },
                success: function(images){
                    drawImages(images);


                },
                error: function(){
                    alert("Something went wrong");
                }
            });


        }
    });

});


$(document).on('click', '.delete-image', function(event){
    event.stopPropagation();
    deleteImageFromSection(SchooltripApp.currentRecord, $(this).attr('data-id'));
});

$(document).on('click', '.create-new-record', function(event){
    createNewRecord(SchooltripApp.currentSection);
});

$(document).on('click', '.mark-section-ready', function(event){
    markSectionAsReady(SchooltripApp.currentSection);
});

$(document).on('click', '#europeana-search-btn', function(){
    SchooltripApp.currentRecord = $(this).attr('data-id');
    var query = $('#europeana-search-input').val();
    europeanaSearch(query);
});

$(document).on('click', '.europeana-image', function(){


    var confirm = window.confirm("Are you sure you want to add this image?");
    if( confirm ){
        var europeanaImage = {
            image_source : "uri",
            image_uri: $(this).attr('src'),
            link : $(this).attr('data-link'),
            description : $(this).attr('data-description'),
            title : $(this).attr('data-title')

        };

        uploadEuropeanaImage(europeanaImage);
    }

});



$(document).on('click', '.heading-records', function(){
    var dateId = $(this).attr('data-id');
    SchooltripApp.currentDate = dateId;
    loadDateJournal(SchooltripApp.journal, dateId);
});

$('.selectpicker').on('change', function(){
    var originalId = $(this).find("option:selected").val();
    addJournalEntry(SchooltripApp.journal, SchooltripApp.currentDate, originalId);
});

$(document).on('click','.btn-edit-datetab-entry', function(){
    var dateEntryId = $(this).attr('data-id');
    var hour = $("#hour-" + dateEntryId).val();
    editTimeJournalEntry(SchooltripApp.journal, SchooltripApp.currentDate, dateEntryId, hour);
});

$(document).on('click', '.btn-delete-datetab-entry', function(){
    var dateEntryId = $(this).attr('data-id');
    var confirm = window.confirm("Are you sure you want to delete this entry?");
    if(confirm){
        deleteJournalEntry(SchooltripApp.journal, SchooltripApp.currentDate, dateEntryId);
    }
});

$(document).on('click', '.save-record-entry', function(){
    var id = $(this).attr('data-id');
    var entry = {
        id: id,
        date_tab_d: $(this).attr('data-datetab-id'),
        hour: $('#date-' + id).val(),
        title: $('#title-' + id).val(),
        description: $('#description-' + id).val(),
        extra_description: $('#extra-description-' + id).val(),
        extra_title: $('#extra-title-' + id).val()

    };

    updateJournalEntry(SchooltripApp.journal , entry);


});



$('.time-picker').timepicker({'timeFormat': 'H:i' , 'show2400' : true});
