$(document).ready(function () {
	//Live search
    $('#search_key').on('keyup', function(){
		var searchTerm = $(this).val().toLowerCase();
		console.log(searchTerm);

	    $('#ul-strings_list li').each(function(){
	        if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
	            $(this).show();
	        } else {
	            $(this).hide();
	        }
	    });
	});

	//Blur save
	$('.string_content').blur(function(){
		var id = this.id;
		var arr_id = id.split('-');
		var language_id = arr_id[1];
		var file_id = arr_id[2];
		var string_id = arr_id[3];
		var value = $('#'+id).val();

		$.ajax({
            cache: false,
            type: 'POST',
            url: $('#linguo_url').val()+"/-/-/update_string",
            data: {
            	language_id: language_id,
            	file_id: file_id,
            	string_id: string_id,
            	value: value
            },
            success: function(data) { 

            }
        });
	});


	//New String
	$('#btn-new_string').click(function(){
		var id = this.id;
		var language_id = $('#language_id').val();
		var file_id = $('#file_id').val();
		var key = $('#string-key').val();
		var value = $('#string-value').val();

		$.ajax({
            cache: false,
            type: 'POST',
            url: $('#linguo_url').val()+"/-/-/create_string",
            data: {
            	language_id: language_id,
            	file_id: file_id,
            	key: key,
            	value: value
            },
            success: function(data) { 
            	$('#string-key').val('');
				$('#string-value').val('');
				location.reload(); 
            }
        });
	});

	//New Language
	$('#btn-new_language').click(function(){
		var value = $('#language-name').val();
		$.ajax({
            cache: false,
            type: 'POST',
            url: $('#linguo_url').val()+"/-/-/create_language",
            data: {
            	value: value,
                clone: $('input[name=language-clone_from_master]:checked').val()
            },
            success: function(data) { 
            	$('#language-name').val('');
				location.reload(); 
            }
        });
	});

	//New File
	$('#btn-new_file').click(function(){
		var value = $('#file-name').val();
		var language_id = $('#language_id').val();

		$.ajax({
            cache: false,
            type: 'POST',
            url: $('#linguo_url').val()+"/-/-/create_file",
            data: {
            	language_id: language_id,
            	value: value,
                clone: $('input[name=file-clone_from_master]:checked').val()
            },
            success: function(data) { 
            	$('#language-name').val('');
				location.reload(); 
            }
        });
	});

    //Set Master
    $('#btn-set_master').click(function(){
        var language_id = $('#language_id').val();

        $.ajax({
            cache: false,
            type: 'POST',
            url: $('#linguo_url').val()+"/-/-/set_master",
            data: {
                language_id: language_id
            },
            success: function(data) { 
                $('#language-name').val('');
                location.reload(); 
            }
        });
    });

});