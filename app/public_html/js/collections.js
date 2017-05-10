function collectionsInit(){
    getUser();
}

function displayCollections(collections){
    var html = '';
    if(collections != null){
        html = '' +
            '<table class="table table-striped">' +
                '<thead>' +
                    '<tr>' +
                        '<th>Name</th>' +
                        '<th>Description</th>' +
                    '</tr>' +
                '</thead>' +
                '<tbody>';
        for(var i = 0; i < collections.length; i++){
            html += '<tr>' +
                    '<td>' + collections[i].Name + '</td>' +
                    '<td>' + collections[i].Description + '</td>' +
                '</tr>';
        }

        html += '</tbody>' +
            '</table>';
    } else {
        html = '<p>No collections yet.</p>'
    }

    console.log(html);
    $('#collections').html(html);
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
            if(ret != null){
                getCollections(ret.UserID);
            } else {
                //The user doesnt exist?!?!
            }
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
                displayCollections(ret);
        },
        error: function (err) {
            console.log("Error getting collections.");
        }
    });
}