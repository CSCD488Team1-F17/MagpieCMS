function onSignIn(googleUser){
    var id_token = googleUser.getAuthResponse().id_token;
    
    //Get base url
    var pathArray = location.href.split( '/' );
    var protocol = pathArray[0];
    var host = pathArray[2];
    var url = protocol + '//' + host;

    //Verify token
    //TODO: Move verify to backend for better security
    $.ajax({
        type: 'POST',
        url: 'https://www.googleapis.com/oauth2/v3/tokeninfo',
        dataType: "json",
        data: {
            'id_token' : id_token
        },
        success: function (ret) {
            //TODO: Do backend stuff here.

            window.location.href = url + "/dashboard";

            // var profile = googleUser.getBasicProfile();

            // var name = profile.getName();
            // var icon = profile.getImageUrl();

            // var content = '' +
            // '<h1>Hello ' + name + '</h1>' +
            // '<img src="' + icon + '">' +
            // '';

            // $("#userInfo").html(content);
        },
        error: function (err){
            console.log("Error logging in.");
        }
    });
}