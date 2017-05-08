function collectionsInit(){
    user = getUser();
    if(user != null && user.UserID != null){
        // collections = getCollections(user.UserID);
        // if(collections != null){

        // }
        console.log(user);
    } else {
        //The user doesnt exist?!?!
    }
}

function getUser(){
    var auth2 = gapi.auth2.getAuthInstance();
    var googleUser = auth2.currentUser.get();
    var id_token = googleUser.getAuthResponse().id_token;
    var obj = { 'id_token': id_token };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/api/user/web',
        data: json,
        success: function (ret) {
            return ret;
        },
        error: function (err) {
            console.log("Error getting user.");
        }
    });
}

function getCollections(userID){
    var obj = { 'UserID': userID };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/api/user/web/collections',
        data: json,
        success: function (ret) {
            return JSON.parse(ret);
        },
        error: function (err) {
            console.log("Error getting collections.");
        }
    });
}