
addToCartRunning = false;

$(document).ready(function(){
    shop.init();
});

(function(shop, $, undefined) {
    
    shop.init = function() {
        $('.btn-cart').click(function(){
            var amountField = $(this).data("amount");
            var amount = 1;

            if(amountField) {
                var amountFieldInput = $('#' + amountField);

                if(amountFieldInput) {
                    amount = amountFieldInput.val();

                    if(amount <= 0) {
                        amount = 1;
                    }
                }
            }

            shop.addToCart($(this).data("id"), amount, $(this));
        });

        $('select.site-reload').change(function() {
            var val = $(this).val();

            var parsedUrl = $.url(window.location.href);
            var params = parsedUrl.param();

            params[$(this).attr("name")] = val;

            window.location.href = "?" + $.param(params);
        });

        $('.selectpicker[name=variant]').change(function() {
            window.location.href = $(this).find("[value=" + $(this).val() + "]").data("href");
        });
       
        if($('#shop-register-form').length > 0)
        {
            shop.initRegisterForm();
        }

        $('.price-rule').click(function() {
            $('#priceRule').val($(this).find(".price-rule-code").html())
        })

        $('.btn-compare').on('click', function(ev) {

            var $el = $(ev.target),
                product_id = $el.data("id");
            shop.addToCompareList( product_id, $el );

        });

        $('.btn-compare-remove').on('click', function( ev ) {

            var $el = $(ev.target),
                product_id = $el.data("id");
            shop.removeFromCompareList( product_id, $el );

        });

        shop.initChangeAddress();
        shop.addCartEventListeners();

        $(document.body).append( $('<div/>', {'id' : 'coreshop-bt-message'}) );


    };

    shop.addCartEventListeners = function()
    {
        $('.removeFromCart').unbind("click");
        $('.removeFromCart').bind("click", function(){

            var button = $(this);

            shop.removeFromCart($(this).data("id"));
        });

        $('.cart-item-amount').unbind("change");
        $('.cart-item-amount').change(function(){
            shop.modifyCartItem($(this).data("id"), $(this).val());
        });
    };

    shop.addToCart = function(product_id, amount, sender, extraData, callback)
    {
        var data = $.extend({product : product_id, amount : amount}, extraData ? extraData : {});
        
        $.ajax({
            url : '/de/cart/add',
            data : data,
            dataType: 'json',
            success : function(result,status,xhr) {
                if(status == "success")
                {
                    if(result.success)
                    {
                        var imgtofly = $($(sender).data("img"));
                        
                        if(imgtofly.length > 0)
                        {
                            var cart = $('#cart');
                            var imgclone = imgtofly.clone();
                            
                            imgclone.offset({ top:imgtofly.offset().top, left:imgtofly.offset().left });
                            imgclone.css({'opacity':'0.7', 'position':'absolute', 'height':'150px', 'width':'150px', 'z-index':'1000'});
                            imgclone.appendTo($('body'));
                            imgclone.animate({'top':cart.offset().top + 10,'left':cart.offset().left + 30, 'width' : 55, 'height' : 55}, 1000);
                            imgclone.animate({'width':0, 'height':0}, function(){ $(this).detach() });
                        }

                        shop.updateCart(result);
                        
                        if(callback)
                            callback();
                    }
                    else {
                        alert(result.message);
                    }
                }
            }
        });
    };
    
    shop.removeFromCart = function(cartItem, callback)
    {
        $.ajax({
            url : '/de/cart/remove',
            data : {cartItem : cartItem},
            dataType: 'json',
            success : function(result,status,xhr) {
                if(status == "success")
                {
                    if(result.success)
                    {
                        shop.updateCart(result);
                        
                        if(callback)
                            callback();
                    }
                }
            }
        });
    };
    
    shop.modifyCartItem = function(cartItem, amount, callback)
    {
        $.ajax({
            url : '/de/cart/modify',
            data : {cartItem : cartItem, amount:amount},
            dataType: 'json',
            success : function(result,status,xhr) {
                if(status == "success")
                {
                    if(result.success)
                    {
                        shop.updateCart(result);
                        
                        if(callback)
                            callback();
                    }
                }
            }
        });
    };
    
    shop.updateCart = function(cart)
    {
        $('.shopping-cart-table').replaceWith(cart.cart);
        $('#cart').replaceWith(cart.minicart);

        shop.addCartEventListeners();
    };

    shop.addToCompareList = function(product_id, sender, extraData, callback)
    {
        var data = $.extend({product : product_id}, extraData ? extraData : {});

        $.ajax({
            url : '/de/compare/add',
            data : data,
            dataType: 'json',
            success : function(result,status,xhr) {
                if(status == "success")
                {
                    if(result.success)
                    {

                        shop.showMessage('product successfully added to compare list.');

                        if(callback)
                            callback();

                    } else {

                        shop.showMessage( result.message );
                    }
                }
            }
        });
    };

    shop.removeFromCompareList = function(product_id, sender, extraData, callback)
    {
        var data = $.extend({product : product_id}, extraData ? extraData : {});

        $.ajax({
            url : '/de/compare/remove',
            data : data,
            dataType: 'json',
            success : function(result,status,xhr) {
                if(status == "success")
                {
                    if(result.success)
                    {
                        sender.closest('.compare-block').remove();
                        window.location.reload();

                        if(callback)
                            callback();

                    }
                }
            }
        });
    };

    shop.initRegisterForm = function()
    {
        $('#shop-register-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok-circle',
                invalid: 'glyphicon glyphicon-remove-circle',
            },
            excluded: ':disabled',
            fields: shop.fieldsForRegister()
        });
    };

    shop.showMessage = function( message, type ) {

        $('#coreshop-bt-message').html('' +
        '<div class="modal fade" tabindex="-1" role="dialog">' +
        '  <div class="modal-dialog" role="document" style="z-index:1040;">' +
        '        <div class="modal-content">' +
        '        <div class="modal-header">' +
        '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>' +
        '        <h4 class="modal-title">Modal title</h4>' +
        '        </div>' +
        '        <div class="modal-body">' +
        '        <p>' + message + '</p>' +
        '        </div>' +
        '        <div class="modal-footer"> ' +
        '        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
        '        </div>' +
        '        </div>' +
        ' </div>' +
        '</div>');

        $('#coreshop-bt-message').find('.modal').modal();

    };

    shop.fieldsForRegister = function()
    {
        return $.extend({
            email: {
                container: '[data-for=email]',
                validators: {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Email is required'
                    },
                    emailAddress: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Email is invalid'
                    },
                    identical: {
                        field: 'reemail',
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Email hast to be equal'
                    }
                }
            },
            reemail: 
            {
                container: '[data-for=reemail]',
                validators: 
                {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Email is required'
                    },
                    emailAddress: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Email is invalid'
                    },
                    identical: 
                    {
                        field: 'email',
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Email has to be equal'
                    }
                }
            },
            password: 
            {
                container: '[data-for=password]',
                validators: 
                {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Password is required'
                    },
                    different: {
                        field: 'username',
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Password and Username must be different'
                    },
                    stringLength: {
                        min: 8,
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Password must be at least 8 characters'
                    },
                    identical: {
                        field: 'repassword',
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Passwords has to be equal'
                    }
                }
            },
            repassword: {
                container: '[data-for=repassword]',
                validators: 
                {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Password is required'
                    },
                    different: {
                        field: 'username',
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Password and Username must be different'
                    },
                    stringLength: {
                        min: 8,
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Password must be at least 8 characters'
                    },
                    identical: {
                        field: 'password',
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Passwords has to be equal'
                    }
                }
            },
        
            firstname: 
            {
                container: '[data-for=firstname]',
                validators: {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Firstname is required'
                    }
                }
            },
            lastname : 
            {
                container: '[data-for=lastname]',
                validators : 
                {
                    notEmpty: 
                    {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Lastname is required'
                    }
                }
            },
        
            gender: 
            {
                container: '[data-for=gender]',
                validators: 
                {
                    notEmpty: 
                    {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Gender is required'
                    }
                }
            },
        }, shop.fieldsForAddress());
    };
    
    shop.fieldsForAddress = function() {
        return {
            address_firstname: 
            {
                container: '[data-for=adress_firstname]',
                validators: {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Firstname is required'
                    }
                }
            },
            
            address_lastname : 
            {
                container: '[data-for=adress_lastname]',
                validators : 
                {
                    notEmpty: 
                    {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Lastname is required'
                    }
                }
            },
            
            address_street : 
            {
                container: '[data-for=address_street]',
                validators : 
                {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Street is required'
                    }
                }
            },
          
            address_nr : 
            {
                container: '[data-for=address_number]',
                validators : {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Number is required'
                    }
                }
            },
          
            address_zip : 
            {
                container: '[data-for=address_zip]',
                validators : 
                {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> ZIP is required'
                    },
                    regexp: {
                        regexp: /^\d{4,}$/,
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> ZIP has to be a number'
                    }
                }
            },
          
            address_city : 
            {
                container: '[data-for=address_city]',
                validators : {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> City is required'
                    }
                }
            },
          
            address_country : 
            {
                container: '[data-for=address_country]',
                validators : {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Country is required'
                    }
                }
            },

            address_name:
            {
                container: '[data-for=address_name]',
                validators : {
                    notEmpty: {
                        message: '<i class="glyphicon glyphicon-remove-circle"></i> Name is required'
                    }
                }
            }
        };
    };
    
    shop.initChangeAddress = function()
    {
        $('select[name=shipping-address]').change(function(){
            var value = $(this).val();
            
            value = $(this).find("[value='"+value+"']").data("value");
            
            $('.panel-delivery-address').html($('#address-' + value).html());
            
            if($('[name=useShippingAsBilling]').is(":checked"))
            {
                $('.panel-billing-address').html($('#address-' + value).html());
                
                $('select[name=billing-address]').val($(this).val());
            }
        });
        
        $('select[name=billing-address]').change(function(){
            var value = $(this).val();
            value = $(this).find("[value='"+value+"']").data("value");
            
            $('.panel-billing-address').html($('#address-' + value).html());
        });
        
        $('[name=useShippingAsBilling]').change(function(){
            if($(this).is(":checked"))
            {
                $('.billing-address-selector').slideUp();
                
                var value = $('select[name=delivery-address] :selected').val();
                var htmlValue = $('select[name=delivery-address]').find("[value='"+value+"']").data("value");

                $('.panel-billing-address').html($('#address-' + htmlValue).html());
                
                $('select[name=billing-address]').val(value);
            }
            else
            {
                $('.billing-address-selector').slideDown();
            }
        });
    };
    
}( window.shop = window.shop || {}, jQuery ));