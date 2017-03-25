function onSignIn(googleUser){
    //window.open("login-success","_self")

    var profile = googleUser.getBasicProfile();

    var name = profile.getName();
    var icon = profile.getImageUrl();

    var content = '' +
    '<h1>Hello ' + name + '</h1>' +
    '<img src="' + icon + '">' +
    '';

    $("#userInfo").html(content);
}