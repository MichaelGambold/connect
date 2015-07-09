// validate search.php form
function validateSearchForm() {
   // hide current error messages
   $('span.errorMsg').css('display', 'none');

   // get required form fields for validation
   var wineName = $('#wineName').val();
   var wineryName = $('#wineryName').val();
   var minYear = $('#minYear').val();
   var maxYear = $('#maxYear').val();
   var numInStock = $('#minWinesInStock').val();
   var numOrdered = $('#minWinesOrdered').val();
   var minCost = $('#minCost').val();
   var maxCost = $('#maxCost').val();

   // wine name
   if (wineName.length != 0)
      if (!validAlphanumeric(wineName)) {
         $('#errorWineName').css('display', 'inline');
         return false;
      }

   // winery name
   if (wineryName.length != 0)
      if (!validAlphanumeric(wineryName)) {
         $('#errorWineryName').css('display', 'inline');
         return false;
      }

   // valid min year
   if (minYear.length != 0)
      if(!validYears(minYear)) {
         $('#errorYears').css('display', 'inline');
         return false;
      }
   
   // valid max year
   if (maxYear.length != 0)
      if(!validYears(maxYear)) {
         $('#errorYears').css('display', 'inline');
         return false;
      }

   // have both min and max years, check min year not larger than max
   if (minYear.length != 0 && maxYear.length != 0)
      if (parseInt(minYear) > parseInt(maxYear)) {
         $('#errorYears').css('display', 'inline');
         return false;
      }

   // num in stock
   if (numInStock.length != 0)
      if (parseInt(numInStock) < 0) {
         $('#errorInStock').css('display', 'inline');
         return false;
      }

   // num ordered
   if (numOrdered.length != 0)
      if (parseInt(numOrdered) < 0) {
         $('#errorOrdered').css('display', 'inline');
         return false;
      }

   // validate min cost
   if (minCost.length != 0)
      if (minCost < 0) {
         $('#errorMinCost').css('display', 'inline');
         return false;
      }

   // validate max cost
   if (maxCost.length != 0 )
     if (maxCost < 0) {
        $('#errorMaxCost').css('display', 'inline');
        return false;
     }

   // if have both min and max cost, check min cost not greater than max cost
   if (minCost.length != 0 && maxCost.length != 0)
      if (parseFloat(minCost) > parseFloat(maxCost)) {
         $('#errorMinCost').css('display', 'inline');
         $('#errorMaxCost').css('display', 'inline');
         return false;
      }

   // passed validation so return true
   return true;
}

// chech alphanumeric regex function
function validAlphanumeric(string) {
   var alphanumericRegex = /^[a-z0-9 ]*$/i

   if (string.match(alphanumericRegex))
      return true;
   else
      return false;
}

// check years regex function
function validYears(year) {
   var yearsRegex = /^[12][0-9]{3}$/;

   if (year.match(yearsRegex))
      return true;
   else
      return false;
}
