jQuery(function($) {
	var amount = 0;
	var total_qty = 0;
	var discountedprice = 0;
	var old_amount, old_discount;
	var globalqty = 0;
	var sip_currency_symbol = document.getElementById("sip_currency_symbol");
	var discount = {
		minimum: {
			percent: 0,
			desc: ""
		},
		products: {
			percent: 0,
			desc: ""
		},
		quantity: {
			percent: 0,
			desc: ""
		},
		override: {
			percent: 0,
			desc: "",
			price: 0
		},
		price: 0
	};
	
	$err = [];

	var olddesc = $(".woos-total p").text();
	var old_offers = offers;

	$(".woos-quantity input.woos-quantity-input").on("change", function() {
	
		validateQuantity( $(this) );
		resetOffers();
		
		refreshOffers();
		setFields();
	});
	
	$(".woos-product").on("click", function(e) {
		e.stopPropagation();
		if( $(e.target).is("input") && $(e.target).is(":visible") && $(e.target).parents(".woos-field").length > 0 )
			return;
		
		resetOffers();
		
		refreshOffers();
		setFields();
	});
	
	// Added by Usman
	$(".woos-product").ready(function(e) {
		// e.stopPropagation();
		if( $(e.target).is("input") && $(e.target).is(":visible") && $(e.target).parents(".woos-field").length > 0 )
			return;
		
		resetOffers();
		
		refreshOffers();
		setFields();
	});

	function get_total() {
		var d = 0;

		$(".woos-product").each(function(i, field) {
			$product = $(this);
			
			$added = $product.find(".woocheckbox input[type=checkbox]").is(":checked");
			if( $added == true && is_errored($product.find(".woos-quantity")) == false ) {
				var qty = $product.find(".woos-quantity-input").val();
				var price = $product.find(".woos-price-input").val();
				
				var total = parseInt(price) * parseInt(qty);
				d += total;
			}
		});

		return ( d < 0 ? 0 : d );
	}
	
	function resetOffers() {
		
		old_amount = amount;
		old_discount = discount;
		
		amount = 0;
		total_qty = 0;
		
		discount = {
			minimum: {
				fixed: 0,
				percent: 0,
				desc: ""
			},
			products: {
				fixed: 0,
				percent: 0,
				desc: ""
			},
			quantity: {
				fixed: 0,
				percent: 0,
				desc: ""
			},
			override: {
				fixed: 0,
				percent: 0,
				desc: "",
				price: 0
			},
			price: 0
		};
		
		for(var j = 0; j < offers.length; j++) {
			offers[j].applied = false;
		}
		for(var k = 0; k < product_offers.length; k++) {
			product_offers[k].applied = false;
		}
		for(var k = 0; k < cart_offers.length; k++) {
			cart_offers[k].applied = false;
		}
		$err = [];
	}
	
	function is_errored(elem) {
		var error_div = elem.find(".quantity-error");
		return ( (error_div.length>0) ? true : false );
	}
	
	$("a.woos-addcart").on("click", function(e) {
		e.preventDefault();
		
		var min = parseInt($("#woobundle").find("input#woos-min-items-input").val());
		var max = parseInt($("#woobundle").find("input#woos-max-items-input").val());
		
		var error = "";
		var quantity = parseInt(get_total_qty());
		
		if( quantity != 0 && quantity != "" ) {
		
			if( min == max && quantity != min && min != -1 ) {
				error = ((min!=-1)?min:0);
			}
			
			if( quantity < min ) {
				error = ((min!=-1)?min:0);
				if( min != max )
					error = ((min!=-1)?"between "+min+" and ":"") + ((max!=-1)?max:0);
			}
			
			if( quantity > max && max != -1 ) {
				error = ((max!=-1)?max:0);
				if( min != max )
					error = ((min!=-1)?"between "+min+" to ":"") + ((max!=-1)?max:0);
			}

			if( error != "" ) {
				doError("To order this bundle please select " + error + " items.", $(this));
			} else {
				removeError($(this));
				$("form#woobundler-form").submit();
			}
		} else
			doError("You haven't selected any item.", $(this));
	});
	
	function get_total_qty() {
		var total_qty = 0;
		$(".woos-product").each(function(i, field) {
			$product = $(this);
			
			if( is_variable($product) ) {
				if( option_selected($product) == false || price_updated($product) == false ) {
					return;
				}
			}
			
			$qty = $product.find(".woos-quantity-input").val();
			if(typeof $qty == 'NaN' || typeof $qty == 'undefined' || $qty == "" || is_errored($product.find(".woos-quantity")) == true )
				$qty = 0;

			total_qty += parseInt($qty);
		});
		return total_qty;
	}
	
	function refreshOffers() {
		
		discount.override.price = get_total();
		discount.price = get_total();
		var subtotal = get_total();
		
		$(".woos-product").each(function(i, field) {
			$product = $(this);
			
			if( is_variable($product) ) {
				update_price($product);
				if( option_selected($product) == false || price_updated($product) == false ) {
					return;
				}
			}
			
			$qty = $product.find(".woos-quantity-input").val();
			if(typeof $qty == 'NaN' || typeof $qty == 'undefined' || $qty == "" || is_errored($product.find(".woos-quantity")) == true )
				$qty = 0;

			total_qty += parseInt($qty);

			$added = $product.find(".woocheckbox input[type=checkbox]").is(":checked");
			if( $added == true && is_errored($product.find(".woos-quantity")) == false ) {
				var qty = $product.find(".woos-quantity-input").val();
				var price = $product.find(".woos-price-input").val();
				
				var total = parseInt(price) * parseInt(qty);
				amount += total;
			}
			
			for(var j = 0; j < offers.length; j++) {
				if( typeof offers[j].minimum != "undefined" ) {
					if( amount >= parseInt(offers[j].minimum) ) {
						applied = ( offers[j].applied == 'true' || offers[j].applied == true );
						if( !applied ) {
							if( offers[j].discount > discount.minimum.percent ) {
								discount.minimum.percent = parseInt(offers[j].discount);
								discount.price = discount.price - ( discount.price * (offers[j].discount/100) );
								offers[j].applied = true;
								discount.minimum.desc = "<span class=\""+offers[j].css+"\">" + offers[j].desc + "</span>";
								
								if( offers[j].override && discount.override.percent < offers[j].discount ) {
									discount.override.percent = parseInt(offers[j].discount);
									discount.override.desc = "<span class=\""+offers[j].css+"\">" + offers[j].desc + "</span>";
									discount.override.price = subtotal - ( subtotal * (offers[j].discount/100) );
								}
								
								if( offers[j].discount_type == 'amount' && offers[j].discount > discount.minimum.fixed ) {
									discount.minimum.fixed = parseInt(offers[j].discount);
									if( offers[j].override && discount.override.fixed < offers[j].discount ) {
										discount.override.fixed = parseInt(offers[j].discount);
									}
								}
							}
						} else {
							discount.minimum.percent = old_discount.minimum.percent;
							discount.minimum.fixed = old_discount.minimum.fixed;
							discount.minimum.desc = old_discount.minimum.desc;
						}
					}
				}
			}
			
			for(var k = 0; k < product_offers.length; k++) {
				if( typeof product_offers[k].quantity != "undefined" ) {
					
					var productid = $product.find(".woos-productid-input").val();
					var productqty = $product.find(".woos-quantity-input").val();
					
					if( productid == product_offers[k].id ) {
						productqty = parseInt(productqty);
						if(productqty >= product_offers[k].quantity && productqty <= $product.find(".woos-quantity-input").attr("max")) {
							applied = ( product_offers[k].applied == 'true' || product_offers[k].applied == true );
							if( ! applied ) {
								if( product_offers[k].discount > discount.quantity.percent ) {
									discount.quantity.percent = parseInt(product_offers[k].discount);
									discount.price = discount.price - ( discount.price * (product_offers[k].discount/100) );
									product_offers[k].applied = true;
									discount.quantity.desc = "<span class=\""+product_offers[k].css+"\">" + product_offers[k].desc + "</span>";
									
									if( product_offers[k].override && discount.override.percent < product_offers[k].discount ) {
										discount.override.percent = parseInt(product_offers[k].discount);
										discount.override.desc = "<span class=\""+product_offers[k].css+"\">" + product_offers[k].desc + "</span>";
										discount.override.price = subtotal - ( subtotal * (product_offers[k].discount/100) );
									}
									
									if( product_offers[k].discount_type == 'amount' && product_offers[k].discount > discount.quantity.fixed ) {
										discount.quantity.fixed = parseInt(product_offers[k].discount);
										if( product_offers[k].override && discount.override.fixed < product_offers[k].discount ) {
											discount.override.fixed = parseInt(product_offers[k].discount);
										}
									}
								}
							} else {
								discount.quantity.percent = old_discount.quantity.percent;
								discount.quantity.fixed = old_discount.quantity.fixed;
								discount.quantity.desc = old_discount.quantity.desc;
							}
						}
					}
				}
			}
			
			for(var k = 0; k < cart_offers.length; k++) {
				if( typeof cart_offers[k].cart != "undefined" ) {
					if(total_qty >= cart_offers[k].cart) {
						applied = ( cart_offers[k].applied == 'true' || cart_offers[k].applied == true );
						if( !applied ) {
							if( cart_offers[k].discount > discount.products.percent ) {
								discount.products.percent = parseInt(cart_offers[k].discount);
								discount.price = discount.price - ( discount.price * (cart_offers[k].discount/100) );
								cart_offers[k].applied = true;
								discount.products.desc = "<span class=\""+cart_offers[k].css+"\">" + cart_offers[k].desc + "</span>";
								
								if( cart_offers[k].override && discount.override.percent < cart_offers[k].discount ) {
									discount.override.percent = parseInt(cart_offers[k].discount);
									discount.override.desc = "<span class=\""+cart_offers[k].css+"\">" + cart_offers[k].desc + "</span>";
									discount.override.price = subtotal - ( subtotal * (cart_offers[k].discount/100) );
								}
								
								if( cart_offers[k].discount_type == 'amount' && cart_offers[k].discount > discount.products.fixed ) {
									discount.products.fixed = parseInt(cart_offers[k].discount);
									if( cart_offers[k].override && discount.override.fixed < cart_offers[k].discount ) {
										discount.override.fixed = parseInt(cart_offers[k].discount);
									}
								}
							}
						} else {
							discount.products.percent = old_discount.products.percent;
							discount.products.fixed = old_discount.products.fixed;
							discount.products.desc = old_discount.products.desc;
						}
					}
				}
			}
			old_discount = discount;
		});
	}
	
	$(".variation input").on("change", function() {
		
		resetOffers();
		
		$product = $(this).parents(".woos-product");
		update_price($product);
		
		validateQuantity($product.find(".woos-quantity-input"));
		
		refreshOffers();
		setFields();
		
	});
	
	$(".woos-total .woos-quantity input").on("change", function() {
		validateQuantity( $(this) );
	});
	
	function validateQuantity(elem) {
		$divqty = elem.parents(".woos-quantity");
		
		var error_div = $divqty.find(".quantity-error");
		var error_text = "";
		
		if( error_div.length > 0 ) {
			error_div.remove();
		}
		
		if( parseInt(elem.val()) <= 0 ) {
			error_text = "<i class=\"fa fa-warning\"></i> " + "Please enter quantity greater than 0";
			elem.focus();
			
			if( error_div.length > 0 ) {
				error_div.remove();
			}
			
			$('<div class="quantity-error">'+error_text+'</div>').appendTo($divqty);
			
			return false;
			
		}
		
		if( parseInt(elem.attr("max")) > 0 && parseInt(elem.val()) > parseInt(elem.attr("max")) ) {
			
			error_text = "<i class=\"fa fa-warning\"></i> " + (( elem.data("error") != "" ) ? elem.data("error") : "Not enough stock");
			elem.focus();
			
			if( error_div.length > 0 ) {
				error_div.remove();
			}
			
			$('<div class="quantity-error">'+error_text+'</div>').appendTo($divqty);
			
			return false;
			
		}
		return true;
	}
	
	function update_price($product) {
		$productid = $product.find(".woos-productid-input").val();
		
		if( is_variable($product) ) {
			$price = $product.find("woos-price-input");
			$variations = variable_products[$productid];
			$options = [];
			$product.find(".variation .woos-field").each(function() {
				$input = $(this).find("input:checked");
				$option = $input.data("option");
				
				$options[$option] = $input.val();
			});
			
			$info = variable_products[$productid];
			//console.log($info);
			$variables = $info.vars;
			
			$productprice = 0;
			$productqty = 0;
			$productvariation = "";
			
			$valid = false;
			
			for($k = 0; $k < $variables.length; $k++) {
				for($j = 0; $j < $variables[$k].length; $j++) {
					//console.log($k + ": " + $j + ": " + $variables[$k][$j] +" != "+ $options[$j]);
					if( $j > $variables[$k].length - 3 )
						continue;
					
					if( $variables[$k][$j] != "" && $variables[$k][$j] != $options[$j] ) {
						$valid = false;
						break;
					}
					$valid = true;
					if( $j == $variables[$k].length - 4 ) {
						break;
					}
				}
				
				if( $valid ) {
					$price_index = $variables[$k].length - 2;
					$qty_index = $variables[$k].length - 1;
					$variationid_index = $variables[$k].length - 3;
					
					$productprice = $variables[$k][$price_index];
					$productqty = $variables[$k][$qty_index];
					$productvariation = $variables[$k][$variationid_index];
					//console.log("PRICE: " + $productprice + " QTY: " + $productqty);
					break;
				}
			}
			
			if( $valid ) {
				$product.find(".woos-price-input").val($productprice);
				$product.find(".woos-stock-input").val($productqty);
				$product.find(".woos-quantity-input").attr('max', $productqty);
				$product.find(".woos-variationid-input").val($productvariation);
				
				$added = $product.find(".woocheckbox input[type=checkbox]").is(":checked");
				
				if( $added == true && ($productprice == '0' || $productqty < 1) ) {
					$err[$productid] = (wooerrors.combination_error).replace('[product-name]', '<strong>' + $product.find(".woos-product-title").text() + '</strong>')
														.replace('[product-price]', '<strong>' + $product.find(".woos-price-input").val() + '</strong>')
														.replace('[product-sku]', '<strong>' + $product.find(".woos-sku-input").val() + '</strong>')
														.replace('[product-description]', '<strong>' + $product.find(".woos-desc-input").val() + '</strong>')
														.replace('[product-id]', '<strong>' + $product.find(".woos-productid-input").val() + '</strong>');
				}
			}
		}
	}
	
	function is_variable($product) {
		return $product.find(".variation").length>0;
	}
	
	function option_selected($product) {
		$return = true;
		$product.find(".variation .woos-field").each(function() {
			$input = $(this).find("input");
			$checked = false;
			
			if( !$input.is(":checked") && !$checked )
				$return = false;
			
			$checked = true;
		});
		return $return;
	}
	
	function price_updated($product) {
		return ($product.find("input.woos-price-input").val() > 0) ? true : false;
	} 
	
	function setFields() {
		$discount = $("#woos-discount");
		$total = $("#woos-total-input");
		$input_discount = $("#woos-discount-input");
		$discounted_price = $("#woos-discounted-price");
		$total_price = $("#woos-total-price");
		$description = $(".woos-total p");
		$sip_currency_symbol = $(".sip_currency_symbol");

		amount = amount > 0 ? amount : 0;
		discount.price = amount;

		var newDiscount_ = [ discount.minimum.percent, discount.quantity.percent, discount.products.percent ];
		var newFixed_ = [ discount.minimum.fixed, discount.quantity.fixed, discount.products.fixed ];
		
		for( var j = 0; j < newDiscount_.length; j++ ) {
			if( newDiscount_[j] ) {
				if( newFixed_[j] )
					discount.price = discount.price - parseInt(newFixed_[j]);
				else
					discount.price = discount.price - (discount.price * (newDiscount_[j]/100));
			}
		}
		
		discountedprice = discount.price > 0 ? discount.price : 0;
		
		var newDiscount = 0;
		var desc = discount.minimum.desc + discount.quantity.desc + discount.products.desc;
		
		for( var j = 0; j < newDiscount_.length; j++ ) {
			if( newDiscount_[j] ) {
				if( newFixed_[j] )
					newDiscount += (newFixed_[j] * 100) / amount;
				else
					newDiscount += newDiscount_[j];
			}
		}
		
		if( discount.override.percent > 0 ) {
			newDiscount = discount.override.percent;
			if( discount.override.fixed ) {
				newDiscount = (discount.override.fixed * 100) / amount;
			}
			desc = discount.override.desc;
			discountedprice = discount.override.price;
		}
		
		$discount.text( (newDiscount % 1 === 0) ? newDiscount : newDiscount.toFixed(2) );
		$total.val( (discountedprice % 1 === 0) ? discountedprice : discountedprice.toFixed(2) );
		
		if( newDiscount > 0 ) {
			if( amount > 0 ) {
				$discounted_price.text( sip_currency_symbol.innerHTML + discountedprice.toFixed(2) );
				$total_price.text( sip_currency_symbol.innerHTML + amount.toFixed(2) );
			} else {
				$discounted_price.text( sip_currency_symbol.innerHTML + "0.00" );
				$total_price.text( sip_currency_symbol.innerHTML + "0.00" );
			}
		} else {
			$discounted_price.text( sip_currency_symbol.innerHTML + amount.toFixed(2) );
			$total_price.text("");
		}
		
		$input_discount.val( newDiscount );
		$description.html( ( desc == "" ) ? olddesc : desc );
		
		if( $err.length > 0 ) {
			$error_html = "";
			for( $e in $err ) {
				if( $e != "" )
					$error_html += "<li>"+$err[$e]+"</li>";
			}
			$(".woos-total .errors").addClass("show");
			$(".woos-total .savings").addClass("hide");
			$(".woos-total .errors").html($error_html);
		} else {
			$(".woos-total .errors").removeClass("show");
			$(".woos-total .savings").removeClass("hide");
			$(".woos-total .errors").html("");
		}
	}
	
	$(".woos-total .woos-quantity input").on("change", function() {
		
		$bundle_qty = $(this).val();
		globalqty = $bundle_qty;
		
		$(".woos-product").each(function(i, field) {
			$product = $(this);
			
			$added = $product.find(".woocheckbox input[type=checkbox]").is(":checked");
			if( $added == true && is_errored($product.find(".woos-quantity")) == false ) {
				$product.find(".woos-quantity-input").val($bundle_qty).change();
			}
		});
	});
});