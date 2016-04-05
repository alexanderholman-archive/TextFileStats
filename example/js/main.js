var results = $('#results'),
    hasResults = results.find('#hasResults'),
    tplResults = $('#resultsTable'),
    noResults = results.find('#noResults'),
    form = $('#fileForm'),
    fileInput = form.find('#fileUpload'),
    uploadProgress = form.find('#uploadProgress'),
    submitInput = form.find('#submitInput'),
    resetInput = form.find('#resetInput'),
    allowedTypes = getOption('AcceptedFileTypes') || 'text/plain',
    extFilter = getOption('AcceptedExtensions') || 'txt',
    maxFileSize = getOption('AcceptedFileSize') || ( 2 * 1024 * 1024 ); //2MB

function getOption( Option ) {
    $.post(
        form.attr('action'),
        {
            GetOptions: Option
        },
        function ( data ) {
            data = $.parseJSON(data);
            if ( data.status ) {
                return data.data.GetOptions
            } else {
                handleError( data );
                return false;
            }
        }
    )
}

form
    .formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            fileUpload: {
                validators: {
                    file: {
                        extension: extFilter,
                        type: allowedTypes,
                        maxSize: maxFileSize,
                        message: 'Only <b>plain text (.txt)</b> files are supported. Up to <b>100MB</b>.'
                    }
                }
            }
        }
    })
    .on('success.form.fv', function( e ) {
        // Prevent form submission
        e.preventDefault();
        form.data('formValidation').resetForm();
        noResults.addClass('hide');
        hasResults.removeClass('hide');
        results.collapse('show');
    });

resetInput.on('click', function(e){
    e.preventDefault();
    fileInput.val('');
    resetForm();
    if (results.hasClass('in')) results.collapse('hide');
    submitInput.show();
});

results.on('hidden.bs.collapse', function () {
    noResults.removeClass('hide');
    hasResults.addClass('hide').html('');
});

uploadProgress.on('hidden.bs.collapse', function () {
    uploadProgress.find('.progress-bar').attr('aria-valuenow', 0).text( 0 + '%' ).width(0 + '%');
});

function resetForm() {
    form.data('formValidation').resetForm();
    uploadProgress.hide();
    uploadProgress.find('.progress-bar').attr('aria-valuenow', 0).text( 0 + '%' ).width(0 + '%');
}

function handleError( data ) {
    $ErrorString = "";
    for( $Key in data.data.errors ) {
        if ( $ErrorString != "" ) {
            $ErrorString += "\n";
        }
        $ErrorString += data.data.errors[$Key];
    }
    if ( $ErrorString != "" ) alert( $ErrorString );
    if ( !data.status && $ErrorString == "" ) alert("Something happened that I wasn't expecting... just in case, the only language this parser supports is English, let me know if you want to add others.");
}

function handleData( data ) {
    if ( data == "" ) {
        alert( "there has been an error parsing the txt file, zero data was returned" );
        resetForm();
    } else {
        data = $.parseJSON(data);
        if ( data.status ) {
            var tpl = $( tplResults.html() );
            tpl.find('#fileName').text(data.data.filename);
            tpl.find('#WordCount').text(data.data.count.words);
            tpl.find('#LineCount').text(data.data.count.lines);
            tpl.find('#Mean').text(data.data.letter.mean);
            tpl.find('#Mode').text(data.data.letter.mode);
            tpl.find('#Median').text(data.data.letter.median);
            tpl.find('#CommonLetter').text(data.data.letter.common);
            hasResults.append( tpl );
            form.data('formValidation').resetForm();
            uploadProgress.hide();
            uploadProgress.find('.progress-bar').attr('aria-valuenow', 0).text( 0 + '%' ).width(0 + '%');
            noResults.addClass('hide');
            hasResults.removeClass('hide')
            results.collapse('show');
        } else {
            resetForm();
        }
        handleError(data)
    }
}

$("#drop-area-div").dmUploader({
    url: './action.php',
    method: 'POST',
    extraData: {},
    maxFileSize: maxFileSize,
    allowedTypes: allowedTypes,
    extFilter: extFilter,
    maxFiles: 1,
    dataType: null,
    fileName: 'fileUpload',
    onInit: function(){ },
    onFallbackMode: function(message){
        console.log('Upload plugin can\'t be initialized: ' + message);
    },
    onNewFile: function(id, file){
        /* Fields available are:
         - file.name
         - file.type
         - file.size (in bytes)
         */
    },
    onBeforeUpload: function(id){
        submitInput.hide();
        uploadProgress.removeAttr('style').collapse('show');
    },
    onComplete: function(){
        submitInput.show();
    },
    onUploadProgress: function(id, percent){
        uploadProgress.find('.progress-bar').attr('aria-valuenow', percent).text( percent + '%' ).width(percent + '%');
    },
    onUploadSuccess: function(id, data){
        uploadProgress.collapse('hide');
        handleData(data);
    },
    onUploadError: function(id, message){
        alert('Error trying to upload #' + id + ': ' + message);
        form.data('formValidation').resetForm();
    },
    onFileTypeError: function(file){
        alert('File type of ' + file.name + ' is not allowed: ' + file.type);
        form.data('formValidation').resetForm();
    },
    onFileSizeError: function(file){
        alert('File size of ' + file.name + ' exceeds the limit');
        form.data('formValidation').resetForm();
    },
    onFileExtError: function(file){
        alert('File extension of ' + file.name + ' is not allowed');
        form.data('formValidation').resetForm();
    }
});
