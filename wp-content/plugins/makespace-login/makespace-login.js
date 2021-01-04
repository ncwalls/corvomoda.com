(function(win, $){

  var auth2, user;

  win.MakespaceGSuiteLoginInit = function() {
    gapi.load('auth2', gsuiteSignin);
  };

  var gsuiteSignin = function() {
    auth2 = gapi.auth2.init({
      client_id: msw_google_signin.client_id,
      scope: 'profile email'
    });
    auth2.isSignedIn.listen(signinChanged);
    auth2.currentUser.listen(userChanged);
    if (true === auth2.isSignedIn.get()) {
      auth2.signIn();
    }
    if (auth2){
      user = auth2.currentUser.get();
    }
    if ($('#msw-google-signin-container').length) {
      gapi.signin2.render('msw-google-signin', {
        'scope': 'profile email',
        'prompt': 'select_account',
        'width': 320,
        'height': 50,
        'longtitle': true,
        'theme': 'dark'
      });
    }
  };

  var signinChanged = function (val) {
    if ($('#login_error').length) {
      $('#login_error').remove();
    }
    if (true === val && $('#msw-google-signin-container').length) {
      var data = {
        action: 'msw_google_signin',
        token: user.getAuthResponse().id_token
      }
      jQuery.post( msw_google_signin.ajaxurl, data, function( response ){
        if( 1 === response.success ){
          window.location.replace( response.redirect );
        } else {
          $('#login > h1').after('<div id="login_error"><strong>ERROR</strong>: ' + response.error_message + '</div>');
        }
      }, 'json' );
    } else if (false === val) {
      var data = {
        action: 'msw_google_signout'
      }
      jQuery.post( msw_google_signin.ajaxurl, data, function( response ){
        window.location.replace( response.redirect );
      }, 'json' );
    }
  };

  var userChanged = function (googleUser) {
    user = googleUser;
  };

})(window, jQuery);
