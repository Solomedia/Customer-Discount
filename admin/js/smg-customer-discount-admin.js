(function( $ ) {
	'use strict';
	
	$('h4.accordion-toggle').click(function () {
		//Expand or collapse this panel
		$(this).next().slideToggle('fast');
		$('h4.accordion-toggle > span.icon').not($(this).find('span.icon')[0]).addClass('indicador-tab-on').removeClass('indicador-tab-off');
		$(this).find('span.icon').toggleClass('indicador-tab-off').toggleClass('indicador-tab-on');

		//Hide the other panels
		$(".accordion-content").not($(this).next()).slideUp('fast');
	});
	
	$('body').on('click', 'input.action_saved_xcd', function(){
		var data = { 
			post_id: $('#post_ID').val(), 
			element_id: $(this).closest('.accordion-content').data('element-id'),
			new_xcd: $(this).prev('input').val(),
			nonce: $('#nonce_xcd_save').val()
		};

		$.ajax({
            url: 'admin-ajax.php?action=smg_saved_xcd',
            method: 'post',
            data: data,
        beforeSend: function() {
            $(this).attr('disabled', 'disable');
            $('#product_xcd_panel').block({message: null,overlayCSS: {background: '#e4e4e4',opacity: 0.6}});
        },
        success : function( response ) {
            	if(!response.success){
					alert(response.data);
					$('#product_xcd_panel').unblock();
					return;
				}
				$('#product_xcd_panel').unblock();
				alert(response.data);
			},
		}).done(function (data) {
            $(this).removeAttr('disabled');
        });

	});
	
	$(document).on('change', 'select#type_trigger', function(){
		if($(this).val() > 0)
			$('input#quantity_discount').prop( "disabled", true );
		else
			$('input#quantity_discount').prop( "disabled", false );
	});

	$('body').on('click','.divTableCell.table-data.msg > input', function(e){
		e.preventDefault();
		alert($(this).data('msg'));
	});


	$('body').on('click', 'input#add_discount', function(){
		var data = { 
				post_id: $('#post_ID').val(), 
				post_var_id: $('#post_id_var').length ? $('#post_id_var').val() : '', 
				quantity_discount: $('#quantity_discount').val(), 
				type_trigger: $('#type_trigger').val(), 
				discount_message: $('#discount_message').val(), 
				type_discount: $('#type_discount').val(), 
				customer_id: $('#customer_id').val(), 
				discount_cart_message: $('#discount_cart_message').val(), 
				discount_amount: $('#discount_amount').val(),
				nonce: $('#nonce_discount_save').val()
        	};
			
        $.ajax({
            url: 'admin-ajax.php?action=smg_saved_discount',
            method: 'post',
            data: data,
        beforeSend: function() {
            $(this).attr('disabled', 'disable');
            $('#product_d_customer_panel').block({message: null,overlayCSS: {background: '#e4e4e4',opacity: 0.6}});
        },
        success : function( response ) {
            	if(!response.success){
					alert(response.data);
					$('#product_d_customer_panel').unblock();
					return;
				}
				if ($('.empty_reg_event_kwe_ac').length) {
					$('.empty_reg_event_kwe_ac').remove();
				}
				$(response.data).appendTo('#product_d_customer_panel > .cd-panel > .cd-panel__container > .cd-panel__content > .divTable > .divTableBody > .table-content').show('slow');
				$('#product_d_customer_panel').unblock();
				alert('Discount is successfully registered, click on show all discounts to see details');
			},
		}).done(function (data) {
            $(this).removeAttr('disabled');
        });
	});

	$('#product_d_customer_panel').on('click', '.remove_discoun_customer', function (evnt) {
		evnt.preventDefault();
		var current = document.querySelectorAll('.button.remove_discoun_customer.active');
		if (current.length > 0) {
			current[0].className = current[0].className.replace(" active", "");
		}
		this.className += " active";
	});

	$('body').on('click', '#product_d_customer_panel .remove_discoun_customer > div[class^="target-discount"] > .confirm-no', function (evnt) {
		evnt.preventDefault();
		var garet = $(this).closest('.remove_discoun_customer');
		$(garet).removeClass('active');
	});

	$('body').on('click', '#product_d_customer_panel .remove_discoun_customer > div[class^="target-discount"] > .confirm-yes', function (evnt) {
		evnt.preventDefault();
		$(this).closest('.remove_discoun_customer').removeClass('active');
		var remove_fila_padre = $(this).closest('.divTableRow');
		var data = { 
			post_id: $('#post_ID').val(), 
			rem_post_var_id: $(this).closest('.remove_discoun_customer').data('element-varid'), 
			customer_id: $(this).closest('.remove_discoun_customer').data('element-id'), 
			discount_qty: $(this).closest('.remove_discoun_customer').data('element-qty'), 
			nonce: $('#nonce_discount_save').val()
		};

		$.ajax({
			url: 'admin-ajax.php?action=smg_remove_discount',
			method: 'post',
			data: data,
			beforeSend: function () {
				$('.cd-panel__content > .divTable').block({message: null,overlayCSS: {background: '#e4e4e4',opacity: 0.6}});
			},
			success: function (response) {
				if (!response.success) {
					
					alert(response.data);
					$('.cd-panel__content > .divTable').unblock();
					return;
				}

				if (response.success) {
					$(remove_fila_padre).remove();
					if ($('.cd-panel__content > div.divTable > div.divTableBody .divTableRow').length < 1) {
						$('<div class="divTableRow empty_reg_event_kwe_ac"><div  style="padding: 15px;"><em style="color: #a5a5a5;">There are no registered customers.</em></div></div>').appendTo('.cd-panel__content > .divTable > .divTableBody').show(1000);
					}
					$('.cd-panel__content > .divTable').unblock();
				}
			}
		});
	});


	var properties = [
		'customer',
		'variation_id',
		'quantity_discount',
		'type',
		'discount',
		'final_price',
	];

	$.each( properties, function( i, val ) {
		var orderClass = '';
		$("#product_d_customer_panel > .cd-panel > .cd-panel__container > .cd-panel__content > .divTable").on('click', '#' + val, function(e){
			e.preventDefault();
			$('.filter__link.filter__link--active').not(this).removeClass('filter__link--active');
			  $(this).toggleClass('filter__link--active');
			   $('.filter__link').removeClass('asc desc');
	
			   if(orderClass == 'desc' || orderClass == '') {
					$(this).addClass('asc');
					orderClass = 'asc';
			   } else {
				   $(this).addClass('desc');
				   orderClass = 'desc';
			   }
	
			var parent = $(this).closest('.header__item');
				var index = $(".header__item").index(parent);
			var $table = $('.table-content');
			var rows = $table.find('.divTableRow').get();
			var isSelected = $(this).hasClass('filter__link--active');
			var isNumber = $(this).hasClass('filter__link--number');
				
			rows.sort(function(a, b){
	
				var x = $(a).find('.table-data').eq(index).text();
					var y = $(b).find('.table-data').eq(index).text();
					
				if(isNumber == true) {
							
					if(isSelected) {
						return x - y;
					} else {
						return y - x;
					}
	
				} else {
				
					if(isSelected) {		
						if(x < y) return -1;
						if(x > y) return 1;
						return 0;
					} else {
						if(x > y) return -1;
						if(x < y) return 1;
						return 0;
					}
				}
				});
	
			$.each(rows, function(index,row) {
				$table.append(row);
			});
	
			return false;
		});
	});

	/************* PANEL SLIDE ******************/
	var panelTriggers = document.getElementsByClassName('js-cd-panel-trigger');
	if (panelTriggers.length > 0) {
		for (var i = 0; i < panelTriggers.length; i++) {
			(function (i) {
				var panelClass = 'js-cd-panel-' + panelTriggers[i].getAttribute('data-panel'),
						panel = document.getElementsByClassName(panelClass)[0];
				// open panel when clicking on trigger btn
				panelTriggers[i].addEventListener('click', function (event) {
					event.preventDefault();
					addClass(panel, 'cd-panel--is-visible');
				});
				//close panel when clicking on 'x' or outside the panel
				panel.addEventListener('click', function (event) {
					if (hasClass(event.target, 'js-cd-close') /*|| hasClass(event.target, panelClass)*/) {
						event.preventDefault();
						removeClass(panel, 'cd-panel--is-visible');
					}
				});
			})(i);
		}
	}
	function hasClass(el, className) {
		if (el.classList)
			return el.classList.contains(className);
		else
			return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
	}
	function addClass(el, className) {
		if (el.classList)
			el.classList.add(className);
		else if (!hasClass(el, className))
			el.className += " " + className;
	}
	function removeClass(el, className) {
		if (el.classList)
			el.classList.remove(className);
		else if (hasClass(el, className)) {
			var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
			el.className = el.className.replace(reg, ' ');
		}
	}
	/********** end pandel slide ***********/

})( jQuery );

function searchCustomerFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("search_customer");
	filter = input.value.toUpperCase();
	table = document.querySelector(".table-content");
	tr = table.getElementsByClassName("divTableRow");
	for (i = 0; i < tr.length; i++) {
	
	  var rowContent = tr[i].textContent;    
	  rowContent = rowContent.replace(/[\s]+/g, ' ');
	  //console.log(rowContent);
	
	  if (rowContent) {
		if (rowContent.toUpperCase().includes(filter)) {
		  tr[i].style.display = "";
		} else {
		  tr[i].style.display = "none";
		}
	  }  
	  
	}
  }