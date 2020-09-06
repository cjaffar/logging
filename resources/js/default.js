var picker;
var query_params;
var datatable;

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
			}
		);
	}

	if( $('.table').length ) {
		datatable = $('.table').DataTable({
			// data: dataset
            "columns": [
                { 
                	"data":"created",
                	render: function(data, datatype, row, meta) {
                		return '<a href="#" class="view-log" data-detail="' + row['detail'] + '"  data-system="' + row['system'] + '" data-subject="' + row['subject'] + '" data-created="' + row['created'] + '" data-email="' + row['address_from'] + '" data-to="' + row['address_to'] + '" data-detail="' + row['detail'] + '"><ion-icon name="eye-outline" size="medium"></ion-icon></a>&nbsp;&nbsp;' + data;
                	}
                },
                { "data":"system" },
                { "data":"address_from" },
                { "data":"address_to"},
                { "data":"address_replyto"},
                { "data":"subject"}
            ],
            "language": {
			    search: '<i class="fa fa-filter" aria-hidden="true"></i> Filter',
			    searchPlaceholder: 'Filter'
			},
            "paging": false,
            "ordering": false,
            "info":false
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
			window.location = success_url;
			return;
		}

		// $('.login-errors').html('<p>Change password unsuccessful.</p>');
		// $('.login-errors').show();
	})
	.catch((err) => {
		console.log(err)
	});
});

$(document).on('submit', '.frmSearch', function(e) {

	e.preventDefault();

	var client = $("#inputClient").val();
	var email = $('#inlineAddress').val();
	var subject = $('#inlineSubject').val();
	var date1 = picker.getStartDate();
	var date2 = picker.getEndDate();

	var url = $('#search_url').val();
	url += client + '/1';

	$('.logs-loading').show();

	query_params = {}
	if($.trim(email) != '') {
		query_params['email'] = $.trim(email);
	}

	if($.trim(subject) != '') {
		query_params['subject'] = $.trim(subject);
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


$(document).on('click', '.view-log', function(e){
	e.preventDefault();

	var system =  $(this).data('system');

	var html = '<div class="row"><div class="col-12 text-left justify-content-left"><strong>System: </strong>' + system + '<br />';
	html += "<strong>Date:</strong>" + $(this).data('created') + '<br />';
	html += "<strong>From: </strong>" + $(this).data('email')  + '<br />';
	html += "<strong>To: </strong>" + $(this).data('to')  + '<br />';
	html += "<strong>Subject: </strong>" + $(this).data('subject');

	html += '<div class="mail-content my-2"><strong>Content:</strong><br />' + $(this).data('detail') + '</div>';


	html += '</div></div>';

	Swal.fire({

	  title: 'View Log.',
	  showClass: {
	    popup: 'animate__animated animate__fadeInDown'
	  },
	  hideClass: {
	    popup: 'animate__animated animate__fadeOutUp'
	  },
      html: html,
		
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
console.log(query_params);
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

	  	// $('#copy-print-csv_wrapper').remove
	  	$( '.download-pdf' ).remove();

	  	var download_btn = '<a href="#" class="btn btn-primary download-pdf" data-href="'+ url +'/pdf">';
	  	download_btn += '<span class="badge badge-white"><ion-icon name="cloud-download-outline"></ion-icon></span> Download PDF';
	  	download_btn += '<span class="sr-only">unread messages</span>';
	  	download_btn += '</a>';

	  	$('#copy-print-csv_filter').before( download_btn );

	  	document.querySelector('#copy-print-csv_wrapper').scrollIntoView({ 
		  behavior: 'smooth' 
		});
	})
	.catch((error) => {
	  console.error('Error:', error);

	  $('.logs-loading').hide();
	  $('#copy-print-csv').show();
	  $('#copy-print-csv_wrapper').show();
	});
}

$(document).on('click', '.page-link', function(e) {
	e.preventDefault();

	var url = $(this).data('href');
	doSearch(url, $(this).data('number'));

});

$(document).on('click', '.download-pdf', function(e){
	e.preventDefault();

	var url = $(this).data('href');
	doSearch( url, 1 );
})

$('form').parsley();
