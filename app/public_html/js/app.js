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
}