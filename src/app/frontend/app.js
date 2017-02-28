$( document ).ready(function(){
    loadApp();
});

/* Entry point of our application */
function loadApp(){
    var app = '' +
    '<h1>Hello World!</h1>' +
    '';

    $("#app").html(app);
}