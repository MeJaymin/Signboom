  // Special case: Proof and RIP checkbox are on the same page for Len's convenience.
  // Scenario 1: If neither box was ticked when page opens, and user ticks RIP, then proofed is automatically ticked.
  //   Scenario 1a: If RIP is then unchecked, proofed stays checked.
  //   Scenario 1b: If proofed is then unchecked, RIP also gets unchecked.
  // Scenario 2: If proofed was already ticked when page opens, it will be greyed out. If user then ticks RIP, proofed is unchanged.
  //   Scenario 2a: If RIP is then unchecked, proofed stays checked.

  function CheckboxTicked(line_number, queue, job_id)
  {
    //alert("You ticked the " + queue + " box on line number " + line_number + " for job id " + job_id);

    // Get id of box that was checked.
    if (queue == 'Proof')
      box = eval("document.main_form.proofed_" + line_number); 
    else
      box = eval("document.main_form.done_" + line_number); 

    if (box.checked) 
    {
      /*
      if (queue == "Proof") 
      {
        // Remember proofed has just been ticked, so we can allow it to be unticked.
        varname = 'ticked_proof_' + line_number;
        CheckboxTicked[varname] = true;
      }
      else 
      */
      if (queue == "RIP") 
      {
	// If proofed checkbox on same line is not ticked, then tick it as well when RIP box is ticked.
	box2 = eval("document.main_form.proofed_" + line_number); 
        if (box2.checked == false)
        {
          box2.checked = true;
          // Remember proofed has just been ticked, so we can allow it to be unticked.
          //varname = 'ticked_proof_' + line_number;
          //CheckboxTicked[varname] = true;
        }
      }
    }

    // Boxes which have been ticked SINCE this page was displayed can be unticked
    // BEFORE the page is submitted.

    else // box was unticked
    { 
      //if (CheckboxTicked.hasOwnProperty(varname)) 
      if (queue == "Proof") 
      {
        // If RIP checkbox on same line is ticked, then untick it as well when proofed box is unticked.
	box2 = eval("document.main_form.done_" + line_number); 
        if (box2.checked == true) box2.checked = false;
      }
      /*
      else if (queue == "RIP") 
      {
	// If proofed checkbox on same line has been ticked since page opened, then untick it as well when RIP box is ticked.
	box2 = eval("document.main_form.proofed_" + line_number); 
        if (box2.checked == false)
        {
          box2.checked = true;
          // Remember proofed has just been ticked, so we can allow it to be unticked.
          varname = 'ticked_proof_' + line_number;
          CheckboxTicked[varname] = true;
        }
      }
      */

    }

  }


