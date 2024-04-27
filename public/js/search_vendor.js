"use strict";

// The search bar element
let vendorSearchBar = document.getElementById('vendor-search');
// All table cells containing the vendors' display name
let vendorDisplayNameCells = Array.from(document.querySelectorAll('.vendor-display-name'));
// All rows that are visible
let visibleRows = [];
// All rows that are hidden by the search
let hiddenRows = [];

initPageVendorSearch();


/**
 * Initializes the page, storing all the vendor rows in the table into the visibleRows array, then adding search functionality to the search bar
 */
function initPageVendorSearch() {
  // Filling the visible row array with all rows
  vendorDisplayNameCells.forEach(element => {
    let vendorRow = {
      rowElement: element.parentElement,
      vendorName: element.textContent.toLowerCase()
    }
    visibleRows.push(vendorRow);
  });

  // Adding the event listener to the search bar that triggers the filtering of vendor rows
  vendorSearchBar.addEventListener('keyup', handleVendorSearch);

  // Preventing the php search from occurring
  let form = document.querySelector('form');
  form.addEventListener('submit', (evt) => evt.preventDefault());
}

/**
 * Takes in the values from the search bar and then hides all rows that lack that string and shows all hidden rows that include it.
 */
function handleVendorSearch() {
  // Getting the value of the search from the search bar
  let searchQuery = vendorSearchBar.value.trim().toLowerCase();

  if(searchQuery.length > 0) {
    // Find all rows to show and hide, based on their vendor name
    let rowsToHide = visibleRows.filter((row) => !row['vendorName'].includes(searchQuery));
    let rowsToShow = hiddenRows.filter((row) => row['vendorName'].includes(searchQuery));

    // Hide the rows to hide, show the rows to be shown
    rowsToHide.forEach((row) => hideRow(row));
    rowsToShow.forEach((row) => showRow(row));
  } else {
    // If there is no search query, show all rows
    [...hiddenRows].forEach((row) => showRow(row));
  }
}

/**
 * Hides the provided object, making it no longer display and moving it to the hiddenRows array
 * 
 * @param {Object} vendorRow - the row object created in the initialize function to be hidden
 */
function hideRow(vendorRow) {
  let rowIndex = visibleRows.indexOf(vendorRow);
  visibleRows.splice(rowIndex, 1);
  vendorRow['rowElement'].style.display = 'none';
  hiddenRows.push(vendorRow);
}

/**
 * Shows the provided object, making it become visible and moving it to the visibleRows array
 * 
 * @param {Object} vendorRow - the row object created in the initialize function to be shown
 */
function showRow(vendorRow) {
  let rowIndex = hiddenRows.indexOf(vendorRow);
  hiddenRows.splice(rowIndex, 1);
  vendorRow['rowElement'].style.display = 'table-row';
  visibleRows.push(vendorRow);
}
