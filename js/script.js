$.ajax({
    method: "POST",
    url: "./php/request_inv.php",
    data: {
        check: 'test'
    }
}).done(function (msg) {
    $('#onein').val(msg);
});