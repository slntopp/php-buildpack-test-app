$.ajax({
    method: "POST",
    url: "./php/request_inv.php",
    data: {
        check: 'test'

    }
}).done(function (msg) {
    console.log(msg);
    $('#onein').val(msg);
});