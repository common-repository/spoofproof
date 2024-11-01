    function GetSpoofProofResults()
    {
      var UserToGet = jQuery("#user_login").val();
      var SpoofProofResults = "False";
      if (jQuery('#SpoofProof_simulate').is(':checked'))
      { SpoofProofResults = "True"; }

//      alert(Token);
//      alert(ajaxurl);
//      alert(myHost);
      jQuery.post(ajaxurl,
        {
          action           : 'SpoofProof_Next',
          user_login       : UserToGet,
          spoofproof_token : Token,
          Simulate_Attack  : SpoofProofResults
        },
        function(response) {
//            alert(response);
            var Next_Obj = jQuery.parseJSON(response);
            if (Next_Obj.success===true)
            { 
//              jQuery("#user_pass").prop("readonly", false);
              jQuery('label[for=user_login], input#user_pass').hide();
              jQuery('label[for=user_pass], input#user_pass').show();
              jQuery('#user_pass').focus();
              jQuery('#sp-next').hide();
              jQuery('#wp-submit').show();
              jQuery('#SpoofProof_simulate').hide();
              jQuery('#SpoofProof_simulate_label').hide();
              jQuery('#spoofproof_space').hide();
              jQuery('#SpoofProof_revert').hide();
              jQuery('#SpoofProof_revert_label').hide();
            }
            jQuery('#SpoofProofResults').html(Next_Obj.data);
        }
      );
/* */        
    }
    
    function RevertScreen()
    {
        if (jQuery('#SpoofProof_revert').is(':checked'))
        {
            jQuery("#user_pass").prop("readonly", false);
            jQuery('#user_pass').focus();
            jQuery('#sp-next').hide();
            jQuery('label[for=user_pass], input#user_pass').show();
            jQuery('#wp-submit').show();
            jQuery('#SpoofProof_simulate').hide();
            jQuery('#spoofproof_space').hide();
            jQuery('#SpoofProof_simulate_label').hide();
            jQuery('#SpoofProof_revert_label').html('Don\'t Secure Screen with SpoofProof');
            jQuery('#SpoofProofResults').hide();
        }
        else
        {            
//            jQuery("#user_pass").prop("readonly", true);
//            jQuery('#user_pass').focus();
            jQuery('label[for=user_pass], input#user_pass').hide();
            jQuery('#sp-next').show();
            jQuery('#wp-submit').hide();
            jQuery('#SpoofProof_simulate').show();
            jQuery('#SpoofProof_simulate_label').show();
            jQuery('#spoofproof_space').show();
            jQuery('#SpoofProof_revert_label').html('Revert Screen');
            jQuery('#SpoofProofResults').show();
        }
    }
    
    function getParameter(theParameter) 
    { 
     var params = window.location.search.substr(1).split('&');

     for (var i = 0; i < params.length; i++) {
       var p=params[i].split('=');
           if (p[0] == theParameter) {
             return decodeURIComponent(p[1]);
           }
     }
     return false;
   }
   
   jQuery(document).ready(function() 
   { 
     if ( getParameter('action') != "lostpassword")
     {
       jQuery('#wp-submit').hide();
//       jQuery("#user_pass").hide();
       jQuery('label[for=user_pass], input#user_pass').hide();
//       jQuery("#user_pass").prop("readonly", true);
     }  
   });
