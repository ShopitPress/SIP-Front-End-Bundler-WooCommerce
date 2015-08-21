jQuery(function($) {
	
	$.fn.initialize = function() {
		resize();
	};
	
	$.initialize = $.fn.initialize;
	
	// On load
	$(document).ready($.initialize);	
	// On resize
	$(window).resize($.initialize);
	
	function resize() {
		$window_width = $(window).width();
		
		if( is_template('template-one') ) {
			
			// define template one scripts
			$product_height = get_products_height();
			
			if( $window_width > 767 ) {
				$(".woos-product").css("min-height", $product_height);
				$(".woos-total").css("min-height", $product_height);
			}
			
			$button_height = $(".woos-total .woos-addcart").outerHeight();
			$(".woos-total").css("padding-bottom", $button_height + "px");
			
		} else if( is_template('template-two') ) {
			
			// define template two scripts
			
			$details_height = 0;
			
			$(".woos-product").each(function() {
				if( $(this).find(".woos-product-details").find(".details").outerHeight() > $details_height ) {
					$details_height = $(this).find(".woos-product-details").find(".details").outerHeight();
				}
			});
			
			if( $window_width > 767 ) {
				$(".template-two .woos-product-details .details").css("min-height", $details_height);
				
				$totalbox_height = $("#woobundle .woos-total").outerHeight();
				$("#woobundle .woos-total").find(".saving").css("min-height", $totalbox_height);
				$("#woobundle .woos-total").find(".pricing").css("min-height", $totalbox_height);
				$("#woobundle .woos-total").find(".qtyinput").css("min-height", $totalbox_height);
			}
			
		} else if( is_template('template-three') ) {
			
			// define template three scripts
			
			if( $window_width > 767 ) {
				
				$product_height = $("#woobundle.template-three .woos-product").outerHeight();
				$product_height = get_products_height();
				$totalbox_height = $("#woobundle .woos-total").outerHeight();
				
				if( $window_width < 960 ) {
					$totalbox_height -= $("#woobundle .woos-total .errors.show").outerHeight();
				}
				
				$(".template-three .woos-product-details .details").css("min-height", $product_height);
				$(".template-three .woos-product-details .image").css("min-height", $product_height);
				
				
				$("#woobundle .woos-total").find(".saving").css("min-height", $totalbox_height);
				$("#woobundle .woos-total").find(".pricing").css("min-height", $totalbox_height);
				$("#woobundle .woos-total").find(".qtyinput").css("min-height", $totalbox_height);
			}
		}
		
		if( $("#woobundle .woos-total").hasClass("horizontal") ) {
			if( ! $("#woobundle .woos-total.horizontal .qtyinput .woos-quantity").is(":visible") ) {
				$desc_height = $("#woobundle .woos-total.horizontal .qtyinput p").outerHeight();
				$box_height = $("#woobundle .woos-total.horizontal .qtyinput").outerHeight();
				
				$padding = ($box_height - $desc_height) / 2;
				$("#woobundle .woos-total.horizontal .qtyinput p").css("margin-bottom", 0);
				$("#woobundle .woos-total.horizontal .qtyinput p").css("margin-top", 0);
				
				$("#woobundle .woos-total.horizontal .qtyinput").css("padding-top", $padding);
				$("#woobundle .woos-total.horizontal .qtyinput").css("padding-bottom", $padding);
			}
		}
	}
	
	$("input[type=checkbox].woocheckbox").each( function(i, elem) {
		$(this).removeClass('woocheckbox').hide().wrap('<span class="woocheckbox"></span>');
		// $div = $(this).parent();
		// $state = $(this).is(":checked");
		
		// $class = "fa-square-o";
		// if( $state == true )
		// 	$class = "fa-check-square-o";
		
		// $div.append('<i class="fa '+$class+'"></i>');
	});
	
	$(".variation .woos-field label").on("click", function(e) {
		e.stopPropagation();
		
		if($(this).prev().is("input"))
			if($(this).prev().is(":checked"))
				$(this).prev().removeAttr("checked").change();
			else
				$(this).prev().attr("checked", "checked").change();
	});
	
	$(".woos-product").on("click", function(e) {
		e.stopPropagation();
		
		removeError($("#woobundle .woos-addcart"));
		
		if( ($(e.target).is("input") && $(e.target).is(":visible")) || ($(e.target).hasClass(".variation")) )
			return;
		
		$divqty = $(this).find("");
		$div = $divqty.find(".woocheckbox");
		
		$this = $div.find("i.fa");
		
		$input = $div.find('input[type=checkbox]');
		$variation = $(this).find('.image .variation');
		
		if( is_template("template-three") || is_template("template-two") )
			$variation = $(this).find('.image .variation.onactive');
			
		$qty = $divqty.find('input[type=number]');
		$state = $input.is(":checked");
		
		if( $state == true ) {
			
			$class = "fa-square-o";
			$input.removeAttr("checked");
			
			$qty.attr("readonly", "readonly");
			$qty.val("0");
			if( $divqty.find(".quantity-error").length > 0 ) {
				$divqty.find(".quantity-error").remove();
			}
			$variation.hide();
			
		} else {
			
			$class = "fa-check-square-o";
			$input.attr("checked", "checked");
			
			if( !$qty.hasClass("readonly") )
				$qty.removeAttr("readonly");
			
			if( $qty.val() == null || $qty.val() == "" || $qty.val() == "0" ) {
				$qty.val("1");
				
				if( $(".woos-total .woos-quantity input").val() > 0 )
					$qty.val($(".woos-total .woos-quantity input").val());
				
				if( $divqty.find(".quantity-error").length > 0 ) {
					$divqty.find(".quantity-error").remove();
				}
			}
			$variation.show();
		}
		$this.attr("class", "fa " + $class);
	});

	if( $(window).width() < 767 && !$("#woobundle").hasClass("template-one") ) {
		$("#woobundle .woos-products").next().remove();
		
	}
	
	$(window).scroll(function() {

		// if( ($(window).width() < 767 && $(window).width() >= 320) && $(window).height() < 760 ) {
		if( $(window).width() < 767 && $(window).width() >= 320) {


			var scroll = $(window).scrollTop();
			
			var position = $("#woobundle").position();
			var dheight = $("#woobundle .woos-total").outerHeight();
			var height = (position.top + $("#woobundle").outerHeight());


			if( scroll > ( position.top + (position.top * 0.20) ) && scroll < ( height - (height * 0.12) ) ) {
				$("#woobundle .woos-total").css("position", "fixed");
				$("#woobundle .woos-total").css("top", "0");
				$("#woobundle .woos-total").css("right", "0");
			} else {
				$("#woobundle .woos-total").css("position", "relative");
			}
		}
		
	});
	
	function is_template( $name ) {
		return $("#woobundle").hasClass($name);
	}
	
	function get_products_height() {
		$product_height = 0;
				
		$(".woos-product").each(function(i, f) {
			$height = $(this).outerHeight();
			if( $product_height < $height )
				$product_height = $height;
		});
		
		return $product_height;
	}
});

function doError(msg, elem) {
	jQuery('<div class="quantity-error"><i class=\"fa fa-warning\"></i> ' + msg + '</div>').appendTo(elem);
	elem.focus();
}

function removeError(elem) {
	var error_div = elem.find(".quantity-error");
	if( error_div.length > 0 ) {
		error_div.remove();
	}
}