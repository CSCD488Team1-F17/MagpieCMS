function accountInit(){
    getUser();
}

function getUser(){
    var auth2 = gapi.auth2.getAuthInstance();
    var googleUser = auth2.currentUser.get();
    displayAccount(googleUser);
}

function displayAccount(googleUser){
    var basicProfile = googleUser.getBasicProfile();

    $('#email').html(basicProfile.getEmail());
    $('#name').html(basicProfile.getName());
}