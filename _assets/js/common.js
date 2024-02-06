
var user_email = '',
    user_fname = '',
    user_lname = '',
    user_code = '',
    status_timer,
    recognized_attendees = [],
    talked_to_attendees = [],
    known_well_attendees = [];

function authenticateUser() {
  var authenticated = false;
  var authentication_html = '<dl><dt>Your Code</dt><dd><input type="text" name="code" id="code" value="" /></dd></dl><input type="submit" value="submit" />';
  $('#greeting').text('To start, enter the code you were given at registration.');
  $('#quiz-container').show();
  $('#quiz').html(authentication_html).show();
  $('#quiz').unbind('submit').submit(function(event) {
    checkCode();
    event.preventDefault();
  });
}

function updateStatus() {
  $('#status').css({'visibility': 'visible'});
  var status_text = $('#status div').text();
  if (status_text == '' || status_text == '--') {
    status_text = '\\';
  } else if (status_text == '\\') {
    status_text = '|';
  } else if (status_text == '|') {
    status_text = '/';
  } else if (status_text == '/') {
    status_text = '--';
  }
  $('#status div').text(status_text);
}

function createCookie(name, value, days) {
  var expires;
  if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toGMTString();
  } else {
      expires = "";
  }
  document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function readCookie(name) {
  var nameEQ = encodeURIComponent(name) + "=";
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) === ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
  }
  return null;
}

function eraseCookie(name) {
  createCookie(name, "", -1);
}

function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}
