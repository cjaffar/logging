var picker;
var query_params;
var datatable;
var page_number = 1;
var numberofpages = 1;

$(document).ready(function(){
	$('.password-change').hide();

	var minlog = $('#min_date').val();

	if($('#datepicker').length) {
		picker = new Lightpick(
			{ 
				field: document.getElementById('datepicker'),
				singleDate: false,
				maxDate: moment().add(1, 'day'),
				minDate: moment().subtract(minlog, 'day'),
				numberOfMonths: 2,
				dropdowns: {
					years: {
						minYear: 1990,
						maxYear: null,
						min:1990,
						max:null
					}
				}
//			}
			}
		);
		picker.setDateRange(moment().subtract(30, 'day'), new Date());
	}

	if( $('.log-table').length ) {
		datatable = $('.log-table').DataTable({
			// data: dataset
            "columns": [
                { 
                	"data":"created",
                },
                // { "data":"system" },
                { "data":"from_address" },
                { "data":"to_address"},
//                { "data":"address_replyto"},
                { "data":"subject_line"},
                { 
            		"data" : "guid",
            		render:function(data, datatype, row, meta) {
            			return '<a href="#" class="view-log" data-rowid="' + row['guid'] + '" data-detail="' + row['detail'] + '"  data-system="' + row['system'] + '" data-subject="' + row['subject_line'] + '" data-created="' + row['created'] + '" data-email="' + row['from_address'] + '" data-to="' + row['to_address'] + '" data-detail="' + row['detail'] + '"><ion-icon name="eye-outline" size="medium"></ion-icon>&nbsp;&nbsp;View</a>' ;
            		}
        		}
            ],
            "language": {
			    search: '<i class="fa fa-filter" aria-hidden="true"></i> Filter',
			    searchPlaceholder: 'Filter'
			},
            "paging": false,
            "ordering": false,
            "info":false,
            // "search":false
		});
	}

	if( $('.datatable').length ) {
		datatables = $('.datatable').DataTable({
            "paging": false,
            "ordering": false,
		});
	}
});

$(document).on('submit', '.loginFrm', function(e){
	e.preventDefault();

	frm = $(this).serialize();
	url = $(this).attr('action');

	$('.login-errors').hide();

	fetch(url, {
		method:'POST',
    	headers: {
      		// 'Content-Type': 'application/json'
      		'Content-Type': 'application/x-www-form-urlencoded',
    	},
		body: frm
	})
	.then(response => response.json())
	.then(data => {

		if(!data['success']) { $('.login-errors').html("<p>Login unsuccessful, pls check your username/password combination.</p>"); $('.login-errors').show(); }

		if(data['dirty'] == 1) {
			$('.loginFrm').hide();
			$('.password-change').show();
			$('#userattempt').val( data['username'] );
		} else if( data['success']) {
			
			window.location = '/search';
		}
		
	})
	.catch((err) => {
		console.log(err)
	})
});


$(document).on('submit', '.pwdChangeFrm', function(e){
	e.preventDefault();

	frm = $(this).serialize();
	url = $(this).attr('action');

	$(this).parsley().validate();

	if (!$(this).parsley().isValid()) { return false; }

	$('.login-errors').hide();

	fetch(url, {
		method:'POST',
    	headers: {
      		// 'Content-Type': 'application/json'
      		'Content-Type': 'application/x-www-form-urlencoded',
    	},
		body: frm
	})
	.then(response => response.json())
	.then(data => {
		
		if(data['success']) {
			window.location = '/search';
			return;
		}

		$('.login-errors').html('<p>Change password unsuccessful.</p>');
		$('.login-errors').show();
	})
	.catch((err) => {
		console.log(err)
	});
});

$(document).on('submit', '.profileFrm', function(e){
	e.preventDefault();

	frm = $(this).serialize();
	url = $(this).attr('action');
	success_url = $('#success_url').val();
	
	$('.profileFrm').remove('.alert-warning');

	fetch(url, {
		method:'POST',
    	headers: {
      		// 'Content-Type': 'application/json'
      		'Content-Type': 'application/x-www-form-urlencoded',
    	},
		body: frm
	})
	.then(function(response) {
		if(response.ok) {
			return response.json();
		}
		
		throw new Error(response.statusText);
	})
	.then(data => {
		
		if(!data['success']) {
			$('.profileFrm').before('<div class="alert alert-warning mb-2">Editing Profile failed, please try again.</div>');
		}
		
		if(data['success']) {
			window.location = success_url;
			return;
		}

		// $('.login-errors').html('<p>Change password unsuccessful.</p>');
		// $('.login-errors').show();
	})
	.catch((err) => {
		$('.profileFrm').before('<div class="alert alert-warning mb-2">Editing Profile failed: ' + err + '</div>');
	});
});

$(document).on('submit', '.frmSearch', function(e) {

	e.preventDefault();
	
	page_number = 1;

	var client = $("#inputClient").val();
	var email = $('#inlineAddress').val();
	var subject = $('#inlineSubject').val();
	var detail = $('#inlineDetail').val();
	var date1 = picker.getStartDate();
	var date2 = picker.getEndDate();
	
	var email_from = $('#inlineAddressFrom').val()
	var email_to = $('#inlineAddressTo').val()
	var email_reply = $('#inlineAddressReplyTo').val()

	var url = $('#search_url').val();
	url += client + '/1';

	$('.logs-loading').show();

	query_params = {}
	if($.trim(email_from) != '') {
		query_params['email_from'] = $.trim(email_from);
	}
	
	if($.trim(email_to) != '') {
		query_params['email_to'] = $.trim(email_to);
	}
	
	if($.trim(email_reply) != '') {
		query_params['email_reply'] = $.trim(email_reply);
	}

	if($.trim(subject) != '') {
		query_params['subject'] = $.trim(subject);
	}

	if($.trim(detail) != '') {
		query_params['detail'] = $.trim(detail);
	}

	if($.trim(date1) != '') {
		query_params['date1'] = date1.format('YYYY-MM-DD');
	}

	if($.trim(date2) != '') {
		query_params['date2'] = date2.format('YYYY-MM-DD');
	}

	query_params = JSON.stringify(query_params);

	return doSearch(url, 1);
});

const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-success',
    cancelButton: 'btn btn-primary'
  },
  buttonsStyling: false
})

$(document).on('click', '.view-log', function(e){
	e.preventDefault();

	var system =  $(this).data('system');
	var thisid = $(this).data('rowid');

	var html = '<div class="row"><div class="col-12 text-left justify-content-left"><strong>System: </strong>' + system + '<br />';
	html += "<strong>Date:</strong>" + $(this).data('created') + '<br />';
	html += "<strong>From: </strong>" + $(this).data('email')  + '<br />';
	html += "<strong>To: </strong>" + $(this).data('to')  + '<br />';
	html += "<strong>Subject: </strong>" + $(this).data('subject');

	html += '<div class="mail-content my-2"><strong>Content:</strong><br />' + $(this).data('detail') + '</div>';


	html += '</div></div>';

	Swal.fire({

	  title: 'View Log.',
	  showCancelButton: true,
	  cancelButtonText: ' <ion-icon name="archive-outline"></ion-icon> Download Log',
	  showClass: {
	    popup: 'animate__animated animate__fadeInDown'
	  },
	  hideClass: {
	    popup: 'animate__animated animate__fadeOutUp'
	  },
      html: html,
		
	}).then((result) => {
		if (result.dismiss === Swal.DismissReason.cancel) {

			var url = '/search/aws-log/' + thisid;
			fetch(url)
			.then(function(resp) {
				if(resp.ok) {
					return resp.json();
				}
				
				throw new Error(resp.statusText);
			})
			.then(data => {
				if(data['location']) {
					var win = window.open( data['location'] , '_blank');
					win.focus();
					return;
				}
				
				if(data['error']) {
					Swal.fire(
						'Error downloading log file.',
						data['error'],
						'error'
					);
				}
			})
			.catch((error) => {
				console.log(error);
				Swal.fire('500 error', error.text, 'error');
			});
		}
	});
});

var doSearch = function( url, page ) {
	fetch(url, {
		
		method: 'POST',
  		headers: {
    		'Content-Type': 'application/json',
  		},
  		body: query_params

	})
	.then(response => response.json())
	.then(data => {
	  
	  	$('.logs-loading').hide();
	  	$('#copy-print-csv_wrapper').show();
	  	$('#copy-print-csv').show();

	  	if(data['location']) {
	  		window.location = data['location'];
	  		return;
	  	}

	  	datatable.clear();
	  	datatable.rows.add( data.logs );
	  	datatable.draw();

	  	if($.trim(data['pagination']) != '') {
	  		$('.pages').html( data.pagination );
	  	}

	  	numberofpages = data['num_pages']; 
	  	if(numberofpages <= 1) {
	  		$('.next').attr('disabled', 'disabled');
	  		$('.prev').attr('disabled', 'disabled');
	  	}
	  	// $('#copy-print-csv_wrapper').remove
	  	$( '.download-csv' ).remove();

	  	var download_btn = '<a href="#" class="btn btn-primary download-csv float-right" data-href="'+ url +'/pdf">';
	  	download_btn += '<span class="badge badge-white"><ion-icon name="cloud-download-outline"></ion-icon></span> Download CSV';
//	  	download_btn += '<span class="sr-only">unread messages</span>';
	  	download_btn += '</a>';

	  	$('#copy-print-csv_filter').before( download_btn );

	  	document.querySelector('#copy-print-csv_wrapper').scrollIntoView({ 
		  behavior: 'smooth' 
		});
	})
	.catch((error) => {

	  $('.logs-loading').hide();
	  $('#copy-print-csv').show();
	  $('#copy-print-csv_wrapper').show();
	});
}

$(document).on('click', '.page-link', function(e) {
	e.preventDefault();

	if($(this).hasClass('prev')) {
		page_number = page_number - 1;
	}
	
	if($(this).hasClass('next')) {
		page_number = page_number + 1;
	}
	console.log(numberofpages);
	console.log(page_number);
	if(page_number > numberofpages) {
		page_number = numberofpages;
		return;
	}
	
	if(page_number <= 0) {
		page_number = 1;
		return;
	}
	
	var url = $(this).data('href');
	doSearch(url + page_number, page_number );

	$('.page-number-1').text( page_number );

});

$(document).on('click', '.download-csv', function(e){
	e.preventDefault();

	var url = $(this).data('href');
	doSearch( url, 1 );
});

$(document).on('click', '.view-aws-file', function(e){
	e.preventDefault();

	var url = '/aws/get-file?file=' + encodeURIComponent( $(this).data('href') );

	fetch(url)
	.then( response => response.json() )
	.then( data => {

		if(data['error']) {
			Swal.fire(
				'Error getting a file from AWS.',
				data['error'],
				'error'
			);

			return;
		}

		if(data['location']) {
			window.location = data['location'];
		}
	});
});

$(document).on('change', '#inputSystem', function(e) {

	$('#inputClient').empty();

	if($(this).val() == '') {
		$('.log-search').hide();
	}

	if($(this).val() != '') {

		var url = '/search/get-clients/' + $(this).val();
		fetch(url)
		.then(response=>response.json())
		.then(data => {

			if(data['clients'].length == 0) { $('.log-search').hide(); return; }

			$.each(data['clients'], function(key, val) {
				$('#inputClient').append( new Option(val['name'], val['slug']) );
			});

			$('.log-search').show();

		});

	}
});

$(document).on('submit', '.frmClientNew', function(e){
	e.preventDefault();

	var url = $(this).attr('action');
	var params = {};
	
	$('.card').remove('.alert-warning');
	params['system'] = $('#system').val();
	params['name'] = $('#name').val();
	params['slug'] = $('#slug').val();

	fetch(url, {
		method:'POST',
  		headers: {
    		'Content-Type': 'application/json',
  		},
  		body: JSON.stringify( params )
	})
	.then(response => response.json())
	.then(data => {
		
		if(data['success']) {
			window.location = '/client';
		}
		
		if(data['success'] == false) {
			$('.frmClientNew').before('<div class="alert alert-warning mb-2">Client edit/add failed, please try again.</div>');
		}
		
	});
});

//frmClientEdit

$('form').parsley();
