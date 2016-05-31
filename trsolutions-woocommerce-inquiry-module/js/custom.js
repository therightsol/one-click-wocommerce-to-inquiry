/**
 * Created by Ali Shan on 31/05/2016.
 */



jQuery(document).ready(function ($){

    $('#order_comments').attr('placeholder', 'Notes about your inquiry, e.g. some extra points.');


    $('.amount').remove();

    var a = $('.quantities').text();
    $('.quantities').text(a.replace('×', ''));

});