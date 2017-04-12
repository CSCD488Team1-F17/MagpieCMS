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
    if (GoogleAuth.isSignedIn.get()) {} else {
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