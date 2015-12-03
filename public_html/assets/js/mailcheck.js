(function(mailcheck) {
    'use strict';
    mailcheck.onDomReady = function() {
        //Initializations
        //mailcheck.init();
    };

    mailcheck.init = function() {
        var domains = ['hotmail.com', 'gmail.com', 'aol.com', 'kukua.cc'];
        var topLevelDomains = ["com", "net", "org", "nl", "eu", "cc"];
        $('#inputEmail').on('blur', function(event) {
            $(this).mailcheck({
                domains: domains,                       // optional
                topLevelDomains: topLevelDomains,       // optional
                suggested: function(element, suggestion) {
                    // callback code
                    console.log("suggestion ", suggestion.full);
                    $('#suggestion').html("Did you mean <b><i>" + suggestion.full + "</b></i>?");
                },
                empty: function(element) {
                    // callback code
                }
            });
        });
    };

})(window.mailcheck = window.mailcheck || {});
$(document).ready(mailcheck.onDomReady);
