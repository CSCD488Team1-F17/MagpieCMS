var GoogleAuth;

function handleClientLoad() {
    // Load the API's client and auth2 modules.
    // Call the initClient function after the modules load.
    gapi.load('client:auth2', initClient);
}

function initClient() {
    gapi.load('auth2', function () {
        /**
         * Retrieve the singleton for the GoogleAuth library and set up the
         * client.
         */
        GoogleAuth = gapi.auth2.init({
            client_id: '1051549843498-vkaq31j25faui6j6tp6hccrulrklvmdt.apps.googleusercontent.com'
        });

        GoogleAuth.then(onInit, onError);
    });
}

function onInit() {
    if (GoogleAuth.isSignedIn.get()){
        
    } else {
        //Get base url
        var pathArray = location.href.split('/');
        var protocol = pathArray[0];
        var host = pathArray[2];
        var url = protocol + '//' + host;
        window.location.href = url;
    }
}

function onError() {
    console.log("error");
}

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

            window.location.href = url + "/collections";
        },
        error: function (err){
            console.log("Error logging in.");
        }
    });
}

function onSignOut(){
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
        var pathArray = location.href.split( '/' );
        var protocol = pathArray[0];
        var host = pathArray[2];
        var url = protocol + '//' + host;
        window.location.href = url;
    });
}