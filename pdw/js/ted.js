
function showInstructions() 
{
  document.getElementById("instructions_on").style.display="block";
  document.getElementById("instructions_off").style.display="none";
}
function hideInstructions()
{
  document.getElementById("instructions_on").style.display="none";
  document.getElementById("instructions_off").style.display="block";
}
function filterProducts(product_type)
{
  var my_elements, index;

  if ((product_type == "") | (product_type == "all")) // display all products
  {
    //my_elements = document.getElementsByClassName("flexible");
    //for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "block";

    my_elements = document.getElementsByClassName("rigid");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "block";

    my_elements =document.getElementsByClassName("flexible");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "block";

    my_elements =document.getElementsByClassName("adhesive");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "block";

    my_elements =document.getElementsByClassName("paper");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "block";

    my_elements =document.getElementsByClassName("other");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "block";
  }
  else // display just the category requested
  {
    // first hide all of them

    my_elements =document.getElementsByClassName("rigid");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "none";

    my_elements =document.getElementsByClassName("flexible");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "none";
    
    my_elements =document.getElementsByClassName("adhesive");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "none";
    
    my_elements =document.getElementsByClassName("paper");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "none";
    
    my_elements =document.getElementsByClassName("other");
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "none";
    
    // now display just the category requested 
    my_elements =document.getElementsByClassName(product_type);
    for (index = 0; index < my_elements.length; index++) my_elements[index].style.display = "block";
  }
}

