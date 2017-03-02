$( document ).ready(function(){
    loadApp();
});

var rootURL = "/api/"

/* Entry point of our application */
function loadApp(){
    var app = '' +
    '<h1>Hello World!</h1>' +
    '<div id="test"></div>' +
    '';

    $("#app").html(app);
    test();
}

function test(){
    $.ajax({
			type: 'GET',
			url: rootURL + 'hello/sage',
			success: function(response){
				console.log('Success: ', response);
				$("#test").html(response);
			},
			error: function(xhr, type){
			   console.log(xhr, type);
			}
		});
}