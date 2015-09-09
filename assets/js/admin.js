jQuery(function($) {
	
	$("#bundle-fields-offers").appendTo($("#poststuff"));
	
	fillInput();
	
	$("#products").bind("DOMSubtreeModified", function(e) {
		fillInput();
	});

	$( "input#tags" ).autocomplete({
      minLength: 0,
      source: productSKUS,
      focus: function( event, ui ) {
        $( "#tags" ).val( ui.item.label );
        return false;
      },
      select: function( event, ui ) {
		$products = $("#products");
		$field = $("#add-product input[type=text]");
		
		
		if(ui.item.var_pro == "variable_product")
			alert("This feature is available only in PRO version");
		else{
			$html = '<div class="sortitem" data-id="'+ui.item.value+'"><span class="sorthandle" unselectable="on"> </span>'+ui.item.desc+'<br/>SKU: ' + ui.item.label + '<span class="close">x</span></div>';-
			$($html).appendTo($products);
		}
		
		$products.vSort();
		fillInput();
		
		$field.val("");

        return false;
      }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a>SKU: " + item.label + "<br/>" + item.desc + "</a>" )
        .appendTo( ul );
    };
	
	$(document).on('click', '#products .sortitem .close', function(e) {
		e.preventDefault();
		
		$products = $("#products");
		
		$parent = $(this).parent().remove();
		$products.vSort();
		
		fillInput($items);
	});
	
	function fillInput() {
		
		$products = $("#products");
		$input = $("#add-product .products-field");
		
		$items = $products.children();
		$values = [];
		var i = -1;
		
		$items.each(function() {
			i++;
			$id = $(this).data('id');
			$values[i] = $id;
		});
		$input.attr('value', $values.toString());
	}

	$(document).on("click", "#newoffer", function(e) {
		e.preventDefault();
		
		$offers = $("#offers");
		$coupon = $offers.find(".coupon");
		
		$offers.find("#loader").css("display", "block");
		
		var request = validateCoupon($coupon);
		request.done(function() {
			
			var offer_exists = false;
			
			$offers.find('tr.data').each(function(i, tr) {
				if( offer_exists == false ) {
					
					var single_offer = {type: 0, value: 0, product: ""};
					
					$(tr).find('td').each(function(j, td) {
						
						td = $(td);
						
						if( td.data('id') == '#type' )
							single_offer.type = ( (td.text() == 'Minimum amount') ? 0 : (td.text() == 'Total qty. cart') ? 1 : 2 );
						
						if( td.data('id') == '#type' && single_offer.type == 2 )
							single_offer.product = td.find(".product-id").text();
						
						if( $(this).data('id') == '#value' )
							single_offer.value = td.find("input").val();
						
					});
					
					if( single_offer.type == $offers.find("select#type").val() && ( single_offer.value == $offers.find("input#value").val() || (single_offer.type == '2' && single_offer.value == $offers.find("input#value").val() && single_offer.product == $offers.find("select#type-product").val()) ) ) {
						offer_exists = true;
					}
				}
			});
			
			$offers.find("#loader").hide();
			
			if( $validate_coupon == false ) {
				$("#publishing-action input[type=submit]").addClass("disabled");
				alert("Coupon: \"" + $coupon.val() + "\" already exists");
			} else if( offer_exists ) {
				$("#publishing-action input[type=submit]").addClass("disabled");
				alert("Another offer for the criteria selected already exists, please modify your criteria and try again");
			} else {
				$("#publishing-action input[type=submit]").removeClass("disabled");
			
				$offer = '<tr class="data"><td data-id="#css"></td><td data-id="#desc"></td><td data-id="#type"></td><td data-id="#value"></td><td data-id="#discount"></td><td data-id="#coupon-name"></td><td data-id="#override" align="center"><input type="checkbox" name="bundle[offers]['+offerCount+'][override]" /></td><td><a href="#" class="button removebtn">x</a></td></tr>';
				$offer = '<tr class="data">'
						+'<td data-id="#css"><input type="text" name="bundle[offers]['+offerCount+'][css]" /></td>'
						+'<td data-id="#desc"><input type="text" name="bundle[offers]['+offerCount+'][desc]" /></td>'
						+'<td data-id="#type"></td>'
						+'<td data-id="#value"><input type="text" name="bundle[offers]['+offerCount+'][value]" /></td>'
						+'<td data-id="#discount-type"></select>'
						+'<td data-id="#discount"><input type="text" name="bundle[offers]['+offerCount+'][discount]" /></td>'
						+'<td data-id="#coupon-name"><input type="text" name="bundle[offers]['+offerCount+'][coupon-name]" /></td>'
						+'<td data-id="#override" align="center"><input type="checkbox" name="bundle[offers]['+offerCount+'][override]" /></td>'
						+'<td><a href="#" class="button removebtn">x</a></td>'
						+'</tr>';
				append($offer);
				var randomNumber = Math.floor((Math.random() * 999999) + 1);
				$offer = '<tr class="hidden">'
						+'<td data-id="#id"><input type="hidden" name="bundle[offers]['+offerCount+'][id]" value="'+randomNumber+'" /><input type="hidden" name="bundle[offers]['+offerCount+'][coupon]" /></td>'
						+'<td data-id="#type"><input class="type" type="hidden" name="bundle[offers]['+offerCount+'][type]" /><input class="type-product" type="hidden" name="bundle[offers]['+offerCount+'][type-product]" /></td>'
						+'<td data-id="#coupon-name"><input type="hidden" name="bundle[offers]['+offerCount+'][coupon-name]" class="coupon" /></td>'
						+'<td data-id="#discount-type"><input type="hidden" name="bundle[offers]['+offerCount+'][discount-type]" /></td>'
						+'</tr>';
				append($offer);
				offerCount++;
				jQuery( '#css' ).val('');
				jQuery( '#value' ).val('');
				jQuery( '#discount' ).val('');
				jQuery( '#coupon-name' ).val('');
			}
		});
	});
	
	$(document).on("click", "#offers tr a.removebtn", function(e) {
		e.preventDefault();
		
		$offers = $("#offers");
		$row = $(this).parent().parent();
		$values = $row.next();
		$row.remove();
		$values.remove();
	});
	
	$(document).on("change", ".bundle-fields #offers .coupon", function() {
		
		//$coupon = $(this);
		//validateCoupon($coupon);
	});
	
	$(document).on("change", ".bundle-fields #offers #type", function() {
		if($(this).val() == '2')
			$(".bundle-fields #offers #type-product").show();
		else
			$(".bundle-fields #offers #type-product").hide();
	});
	
	function append($offer) {
		
		$offer = $($offer);
		setFields($offer);
		
		$offer.appendTo($offers);
		
	}
	
	function validateCoupon($coupon) {
		$validate_coupon = true;
		
		var request = $.ajax({
							url: window.location.href,
							data: {
								coupon: $coupon.val()
							},
							type: "GET",
							dataType: "HTML",
							success: function(result) {
								if( $(result).find("#coupon-exists").text().indexOf("1") > -1 ) {
									$validate_coupon = false;
								}
							},
							error: function(error) {
								console.log(error);
							}
						});
		return request;
	}
	
	function setFields($offer) {
		if($offer.hasClass('data')) {
			$offer.find("td[data-id]").each(function(i,field) {
				$input = $($(field).data("id"));
				if( $(field).data("id") != "#type" && $(field).data("id") != "#override" ) {
					$(field).find("input").val($input.val());
					if( $(field).children().length <= 0 ) {
						$(field).text($input.val());
					}
				} else {
					if( $input.val() == "2" ) {
						$input2 = $("#type-product");
						$(field).text(types[$input.val()] + " " + $input2.find(":selected").text());
					} else
						$(field).text(types[$input.val()]);
				}
			});
		}
		
		if($offer.hasClass('hidden')) {
			$offer.find("td[data-id]").each(function(i,field) {
				$input = $($(field).data("id"));
				if( $(field).data("id") != "#type" && $(field).data("id") != "#override" ) {
					$(field).find("input").val($input.val());
				}
				else {
					if( $input.val() == "2" ) {
						$input2 = $("#type-product");
						$(field).find("input.type").val($input.val());
						$(field).find("input.type-product").val($input2.val());
					} else
						$(field).find("input.type").val($input.val());
				}
			});
		}
	}
	
	$(".bundle-fields .templates .box").on("click", function(e) {
		e.preventDefault();
		$value = $(this).data("value");
		$(".bundle-fields .templates .box").removeClass('active');
		$(this).addClass('active');
		$(this).parent().find("input.template-input").val($value);
	});
});