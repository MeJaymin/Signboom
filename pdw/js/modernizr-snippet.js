  <!-- jQuery version might not be the latest; check jquery.com -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="js/jquery-1.5.2.min.js"%3E%3C/script%3E'))</script>

  <script>
    if(!Modernizr.input.placeholder) {

      $("input[placeholder], textarea[placeholder]").each(function() {
        if($(this).val()==""){
          $(this).val($(this).attr("placeholder"));
          $(this).focus(function(){
            if($(this).val()==$(this).attr("placeholder")) {
              $(this).val("");
              $(this).removeClass('placeholder');
            }
          });
          $(this).blur(function(){
            if($(this).val()==""){
              $(this).val($(this).attr("placeholder"));
              $(this).addClass('placeholder');
            }
          });
        }
      });

    $('form').submit(function(){
      // first do all the checking for required  element and form validation.
      // Only remove placeholders before final submission
      var placeheld = $(this).find('[placeholder]');
      for (var i = 0; i < placeheld.length; i++){
         if($(placeheld[i]).val() == $(placeheld[i]).attr('placeholder')) {
           // if not required, set value to empty before submitting
           $(placeheld[i]).attr('value','');
         }
      }
    });
  }
  </script>

