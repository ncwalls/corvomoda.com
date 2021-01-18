(function($){

	var wooGallery = function(){
		$('.woocommerce-product-gallery__wrapper').magnificPopup({
			delegate: 'a',
			type: 'image',
			gallery:{
				enabled:true
			}
		});
	};

	var wooAddToCart = function(){
		// $( document ).on( 'click', '.single_add_to_cart_button', function(e) {
		// 	e.preventDefault();
		// });

		//https://quadmenu.com/add-to-cart-with-woocommerce-and-ajax-step-by-step/
		var cartForm = $('form.cart');
		var addToCartButton = $('.single_add_to_cart_button');

		if($('input.variation_id').length > 0){
			if($('input.variation_id').val() && $('input.variation_id').val() !== '0'){
				addToCartButton.removeClass('disable');
			}
			else{
				addToCartButton.addClass('disable');
			}
			$('input.variation_id').on('change', function(){
				if($('input.variation_id').val() && $('input.variation_id').val() !== '0'){
					addToCartButton.removeClass('disable');
				}
				else{
					addToCartButton.addClass('disable');
				}
			});
		}

		var afterCartHtml = '<div class="after-cart-buttons">';
		afterCartHtml += '<p>Added to cart!</p>';
		afterCartHtml += '<p><a href="' + MSWObject.home_url + '/cart/" class="view-cart">View Cart</a></p>';
		afterCartHtml += '</div>';

		addToCartButton.on('click', function(e){
			e.preventDefault();

			var thisButton = $(this);

			if(thisButton.hasClass('disable')){
				return;
			}
			else{
				var productCartUrl = thisButton.attr('href');
				// var productId = thisButton.attr('data-id');
				var productId = cartForm.attr('data-product_id');
				var productQty = 1;

				if($('input.qty').length && $('input.qty').val()){
					productQty = $('input.qty').val();
				}

				var productData = {
		            action: 'woocommerce_ajax_add_to_cart',
		            product_id: productId,
		            product_sku: '',
		            quantity: productQty,
				};

				if($('input.variation_id').length > 0){
					var variationId = $('input.variation_id').val();
					productData.variation_id = variationId;
					console.log('variation:', variationId);
				}

				$.ajax({
		            type: 'post',
		            url: wc_add_to_cart_params.ajax_url,
		            data: productData,
		            beforeSend: function (response) {
		                thisButton.removeClass('added').addClass('loading');
		            },
		            complete: function (response) {
		                thisButton.addClass('added').removeClass('loading');
		                console.log(response);
		            },
		            success: function (response) {

		                // console.log(response);

		                if (response.error & response.product_url) {
		                    window.location = response.product_url;
		                    return;
		                } else {
		                	// console.log(response.fragments, response.cart_hash);
		                	$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, thisButton]);
		                	$('.after-cart-button').remove();
		                	$('.woocommerce-variation-add-to-cart').append(afterCartHtml);
		                }
		            },
		        });
			}
	    });
	};

	var numberInputIncrement = function () {
		$(".numberinput-increment").on("click", function (e) {
			var thisObj = $(this);
			var theInput = thisObj.siblings('input[type="number"]');
			var inputMin = theInput.attr('min') || 0;
			var inputMax = theInput.attr('max') || 100000;
			var theVal = parseInt(theInput.val());
			if (thisObj.hasClass("up") && theVal < inputMax) {
				theVal++;
			} else if (thisObj.hasClass("down") && theVal > inputMin) {
				theVal--;
			}
			// console.log(theVal);
			theInput.val(theVal).trigger("change");
		});
	};


	$(document).ready(function(){
		wooGallery();
		wooAddToCart();
		numberInputIncrement();
	});

})(jQuery);