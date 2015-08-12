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


// settings
SchooltripApp = {};

function initialize(){
    resetSettings();
}
initialize()

function resetSettings(){
    SchooltripApp.currentTrip    = 0;
    SchooltripApp.currentSection = 0;
    SchooltripApp.sectionView    = 'list';
};

$( document ).ajaxStart(function() {
    $('#loading-modal').css('display', 'block');
});

$( document ).ajaxComplete(function() {
    $('#loading-modal').css('display', 'none');
});

// als modal sluit: settings resetten ...


function loadTrip(id){
    $.ajax ({
        'type' : 'POST',
        'url'  :  Routing.generate('trip_load_trip', { id: id }),
        'success': function(data){
            mapEdit(data);
        }
    });

}
// groups: 1) voor select te populaten 2) om geselecteerde groups 3) select picker laten lopen ...
function loadGroups(trip_id){

    // 1 load select values
    $.ajax ({
        'type' : 'POST',
        'url'  :  Routing.generate('trip_load_groups', { id: id }),
        'success': function(data){
            mapEdit(data);
        }
    });
    // 2 load selected classgroups
    $.ajax ({
        'type' : 'POST',
        'url'  :  Routing.generate('trip_load_groups', { id: id }),
        'success': function(data){
            mapEdit(data);
        }
    });

    // 3

}

function mapEdit(trip) {
    $('#edit-title').val(trip.title);
    $('#edit-description').val(trip.description);
    $('#edit-departure').val(trip.departure);
    $('#edit-destination').val(trip.destination);
    $("#edit-date-from").val(trip.datefrom);
    $("#edit-date-to").val(trip.dateto);
    $('#edit-age').selectpicker('val', trip.groups);

    //.selectpicker('val', ['Mustard','Relish']);


    if(trip.journalcreated === true){
        $('#journal-created').css('display', 'block');
        $("#edit-date-from").prop('disabled', true);
        $("#edit-date-to").prop('disabled', true);
    }
    else {
        $('#journal-created').css('display', 'none');
        $("#edit-date-from").prop('disabled', false);
        $("#edit-date-to").prop('disabled', false);
    }

}

function mapManage(){

}


function updateTrip(id){
    var trip = {};

    trip.title = $('#edit-title').val();
    trip.description = $('#edit-description').val();
    trip.departure = $('#edit-departure').val();
    trip.destination = $('#edit-destination').val();
    trip.datefrom = $('#edit-date-from').val();
    trip.dateto = $('#edit-date-to').val();
    trip.groups = $('#edit-age').selectpicker('val');

    $.ajax ({
        'type' : 'POST',
        'data' : trip,
        'url'  :  Routing.generate('trip_update_trip', { id: id }),
        'success': function(data){
            mapEdit(data);
            alert("Trip updated successfully!");
            location.reload();

        }
    });
};

function newTrip() {
    var trip = {};

    trip.title = $('#new-title').val();
    trip.description = $('#new-description').val();

    $.ajax ({
        'type' : 'POST',
        'data' : trip,
        'url'  :  Routing.generate('trip_new_trip'),
        'success': function(){
            alert("Trip created successfully!");
            location.reload();

        }
    });
};

$('.update-button').on('click', function(){
    updateTrip($(this).attr('data-id'));
});

$('.editbutton').on('click', function(){
    loadTrip($(this).attr('data-id'));
    $('.update-button').attr('data-id',$(this).attr('data-id'));
});

$('.create-button').on('click', function(){
    newTrip();
});

$('#new-age').selectpicker();

$('.button-manage').on('click', function(){
    SchooltripApp.currentTrip = $(this).attr('data-id');
    loadSections();
});

function loadSections(){
    var list = $('#paramModalTriplist tbody');
    // clear list
    list.html('');
    $.ajax({ 'type' : 'post',
             'url' : Routing.generate('load_sections', {'id' : SchooltripApp.currentTrip}),
             'success' : function(sections){
                for (section in sections){
                    if(sections.hasOwnProperty(section)){
                        var html = drawSection(sections[section]);
                        list.append(html);
                    }
                }
            }
    });
}

function toggleEditSection(id){
    var editSection = $('#edit-section-' + id);
    var editButton  = $('#section-edit-button-' + id);
    var sectionRow  = $('#section-row-' + id);

    if(editButton.hasClass('section-open')||editSection.length){
        editSection.remove();
        editButton.html('Edit');
        editButton.removeClass('section-open');
        sectionRow.find('td').each(function(){
            $(this).removeClass('bluetemporary');
        });
    }
    else {
        editButton.html('Save');
        editButton.addClass('section-open');
        $.ajax({
            'type' : 'post',
            'url' : Routing.generate('load_section', {'id' : id}),
            'success' : function(section){
                var html = drawEditSection(section);
                $(html).insertAfter(sectionRow);
            }
        });
        sectionRow.find('td').each(function(){
            $(this).addClass('bluetemporary');
        });
    }
}

/*
function toggleEditChallengeSection(id){
    var editSection = $('.section-challenge-open');
    var editButton  = $('.edit-challenge-btn');
    var sectionRow  = $('#edit-challenge-row');

    if(SchooltripApp.toggleOpen === true){
        editSection.remove();
        editButton.html('Edit');
        sectionRow.find('td').each(function(){
            $(this).removeClass('bluetemporary');
        });
        SchooltripApp.toggleOpen = false;
    }
    else {
        SchooltripApp.toggleOpen = false;
        editButton.html('Save');

        $.ajax({
            'type' : 'post',
            'url' : Routing.generate('load_section_parameters', {'id' : id}),
            'success' : function(parameters){
                var html = drawChallengeEditSection(parameters);
                $(html).insertAfter(sectionRow);
            }
        });

        sectionRow.find('td').each(function(){
            $(this).addClass('bluetemporary');
        });
    }
}
*/

function toggleEditPane(id, domEditSection, domEditButton, domSectionRow, path, sourceTemplate){
    var editSection = $(domEditSection);
    var editButton  = $(domEditButton);
    var sectionRow  = $(domSectionRow);

    if(editButton.hasClass('section-open')||editSection.length){
        editSection.remove();
        editButton.html('Edit');
        editButton.removeClass('section-open');
        sectionRow.find('td').each(function(){
            $(this).removeClass('bluetemporary');
        });
    }
    else {
        editButton.html('Save');
        editButton.addClass('section-open');

        $.ajax({
            'type' : 'post',
            'url' : Routing.generate(path, {'id' : id}),
            'success' : function(parameters){
                var html = drawPane(parameters, sourceTemplate);

                $(html).insertAfter(sectionRow);
            }
        });

        sectionRow.find('td').each(function(){
            $(this).addClass('bluetemporary');
        });
    }
}

function toggleEditLinkSection(id){
    var editSection = $('.section-link-open');
    var editButton  = $('.edit-link-btn');
    var sectionRow  = $('#edit-link-row');

    if(editButton.hasClass('section-open')||editSection.length){
        editSection.remove();
        editButton.html('Edit');
        editButton.removeClass('section-open');
        sectionRow.find('td').each(function(){
            $(this).removeClass('bluetemporary');
        });
    }
    else {
        editButton.html('Save');
        editButton.addClass('section-open');

        $.ajax({
            'type' : 'post',
            'url' : Routing.generate('load_section_parameters', {'id' : id}),
            'success' : function(parameters){
                var html = drawLinkEditSection(parameters);

                $(html).insertAfter(sectionRow);
            }
        });

        sectionRow.find('td').each(function(){
            $(this).addClass('bluetemporary');
        });
    }
}


function toggleParametersSection(id){
    console.log("param");
    if( SchooltripApp.sectionView == 'list' ){
        SchooltripApp.currentSection = id;
        $.ajax({
            'type' : 'post',
            'url' : Routing.generate('load_section_parameters', {'id' : id}),
            'success' : function(parameters){
                var html = drawParametersSection(parameters);
                $('#paramModalTriplist tbody').html(html);
            }
        });
        $('#create-new-section').hide();
        $('#back-to-sections').show();
        SchooltripApp.sectionView='detail';

    }
    else if ( SchooltripApp.sectionView == 'detail' ) {
        SchooltripApp.currentSection = 0;
        loadSections();
        $('#create-new-section').show();
        $('#back-to-sections').hide();
        SchooltripApp.sectionView='list';
    }
    loadImages(id);
}

function drawSection(section){
    var source   = $("#section-template").html();
    var template = Handlebars.compile(source);

    return template(section);
}

function drawEditSection(section){
    var source   = $("#open-section-template").html();
    var template = Handlebars.compile(source);

    return template(section);
}



function drawPane(parameters, template){
    var source   = $(template).html();
    var template = Handlebars.compile(source);

    var an_array=[
        {name: "My name"},
        {name: "Another name"}
    ];
    return template(parameters);
    //return template({objects:an_array});
}

function drawParametersSection(parameters){
    console.log(parameters);
    var source   = $("#manage-parameters-template").html();
    var template = Handlebars.compile(source);

    return template(parameters);
}



function saveSection(id, tripid, title, description, genre ){

    $.ajax({
        'url' : Routing.generate('save_section'),
        'data': {
            'title' : title,
            'description' : description,
            'type' : genre,
            'id' : id,
            'tripid' : tripid
        },
        'type': 'post',
        'success': function(data){
            alert("Section saved");
            loadSections();
        },
        error: function (jqXHR, textStatus, errorThrown) {
	alert ('An error occured!');
	console.log (jqXHR);
	console.log (textStatus);
	console.log (errorThrown);
}
    });
}

function deleteSection(id){
    var confirmed = confirm("Are you sure you want to delete?");
    if(confirmed){
        $.ajax({
            'url' : Routing.generate('delete_section', {id: id}),
            'type': 'post',
            'success': function(data){
                if(data == "ok"){
                    alert("Section deleted!");
                    loadSections();
                }
            },
            error: function(){
                alert('An error occurred while deleting!');
            }
        });
    }
}

function deleteImageFromSection(sectionId, imageId){
    var confirmed = confirm("Are you sure you want to delete?");
    if(confirmed){
        $.ajax({
            'url' : Routing.generate('remove_image_section'),
            'type': 'post',
            'data': {
                section_id: sectionId,
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

function updateImageSection(sectionId, imageId, label, description){
    var data = {
        section_id: sectionId,
        image_id: imageId,
        label: label,
        description: description
    };


    $.ajax({
        'url' : Routing.generate('update_image_section'),
        'type': 'post',
        'data': data,
        'success': function(images){
            $('#image-edit').html('');
            drawImages(images);
            //$('#parameter-images').html(html);
        },
        error: function(){
            alert('An error occurred while updating image!');
        }
    });
}

function saveParameters(sectionId, method){
    var data = {
        method: method,
        data: {}
    };

    switch(method){
        case "link":
            var link = {
                'label' : $('#link-label-text').val(),
                'url' : $('#link-url-text').val()
            };
            data.data = link;
            $('#syn-link').html(link.url);
            break;
        case "challenge":

            var challenge = {
                'challenge' : $('#challenge-text').val()
            };
            data.data = challenge;
            $('#syn-challenge').html(challenge.challenge);
            break;
        case "media":
            break;
    }

    // save to path
    $.ajax({
        'url' : Routing.generate('save_section_parameters', {'id' : sectionId}),
        'data': data,
        'type': 'post',
        'success': function(data){
            alert("Parameters saved!");
        },
        error: function (jqXHR, textStatus, errorThrown) {
	alert ('An error occured!');
	console.log (jqXHR);
	console.log (textStatus);
	console.log (errorThrown);
}
    });
}

function createNewSection(){

    $.ajax({
        'url' : Routing.generate('new_section', { id: SchooltripApp.currentTrip }),
        'type': 'post',
        'success': function(data){
            alert("Section created");
            loadSections();
        },
        error: function (jqXHR, textStatus, errorThrown) {
	alert ('An error occured!');
	console.log (jqXHR);
	console.log (textStatus);
	console.log (errorThrown);
}
    });

}



$('#paramModalTriplist').on("click", ".edit-section-btn.section-open", function(){
    var id = $(this).attr('data-id');
    var title = $('#section-title-' + id).val();
    var description = $('#section-description-' + id).val();
    var genre =  $('#section-genre-' + id).val();

    saveSection( id, SchooltripApp.currentTrip, title, description, genre );
});

$('#paramModalTriplist').on("click", ".edit-section-btn", function() {
    var id = $(this).attr('data-id');
    toggleEditSection( id )
});

$('#paramModalTriplist').on("click", ".modalparambutton", function(){
    toggleParametersSection($(this).attr('data-id'));
});

$('#create-new-section').on('click', function(){
    createNewSection();
});

$('#paramModalTriplist').on('click', '.edit-challenge-btn', function(){
    if($(this).hasClass('section-open')){
        saveParameters(SchooltripApp.currentSection, $(this).attr('data-save-method'));
    }
    toggleEditPane(SchooltripApp.currentSection,'.section-challenge-open','.edit-challenge-btn','#edit-challenge-row', 'load_section_parameters', '#edit-challenge-template');
});

$('#paramModalTriplist').on('click', '.edit-link-btn', function(){
    if($(this).hasClass('section-open')){
        saveParameters(SchooltripApp.currentSection, $(this).attr('data-save-method'));
    }
    toggleEditPane(SchooltripApp.currentSection, '.section-link-open','.edit-link-btn', '#edit-link-row', 'load_section_parameters', "#edit-link-template");
});

/*
This creates the edit form for the "Media" parameter
 */
$('#paramModalTriplist').on('click', '.edit-media-btn', function(){
    if($(this).hasClass('section-open')){
        loadImages (SchooltripApp.currentSection);
        saveParameters(SchooltripApp.currentSection, $(this).attr('data-save-method'));
    }
    else {
        loadImages(SchooltripApp.currentSection);
    }
    toggleEditPane(SchooltripApp.currentSection, '.section-media-open','.edit-media-btn', '#edit-media-row', 'load_section_parameters', "#edit-media-template");
});

$('#paramModalTriplist').on('click', '.add-image-btn', function(){
    $('.add-image-btn').addClass('hidden');
    $('#new-image-btn').removeClass('hidden');

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
            $.ajax({
                type: 'POST',
                url: Routing.generate('add_image_section'),
                data: {
                    section_id: SchooltripApp.currentSection,
                    image: response.filename,
                    thumbnail: response.thumbnail
                },
                success: function(images){
                    drawImages(images);


                },
                error: function(){
                    alert("something went wrong");
                }
            });


        }
    });

});

$('#paramModalTriplist').on('click', '.thumbnail-cell', function(){

    loadImage(SchooltripApp.currentSection, $(this).attr('data-id'));

});


$('#paramModalTriplist').on('click', '#back-to-sections', function(){
    toggleParametersSection(0);
});


$('#paramModalTriplist').on('click', '.section-delete-btn', function(event){
    deleteSection($(this).attr('data-id'));
});

// reset current trip if aborted preliminary
$('div').on('hidden.bs.modal', function () {
    resetSettings();
    $('#paramModalTriplist tbody');
})

$('#paramModalTriplist').on('click', '.delete-image', function(event){
    event.stopPropagation();
    deleteImageFromSection(SchooltripApp.currentSection, $(this).attr('data-id'));
});

$('#paramModalTriplist').on('click', '.update-image-btn', function(event){
    updateImageSection(SchooltripApp.currentSection, $(this).attr('data-id'), $('#media-label-image').val(), $('#media-description').val());
});


$('.trip-delete').on('click', function(){
    var confirm = window.confirm("Are you sure you want to delete this trip?");
    var tripId = $(this).attr('data-id');
    if (confirm){
        $.ajax ({
            'type' : 'POST',
            'data' : {id: tripId},
            'url'  :  Routing.generate('trip_delete_trip'),
            'success': function(data){
                alert("Trip deleted!");
                location.reload();
            }
        });
    }

});

