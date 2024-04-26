"use strict";

let vendorSearchBar = document.getElementById('vendor-search');
let vendorDisplayNameCells = Array.from(document.querySelectorAll('.vendor-display-name'));
let visibleRows = [];
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
}

/**
 * Takes in the values from the search bar and then hides all rows that lack that string and shows all hidden rows that include it.
 */
function handleVendorSearch() {
  // Getting the value of the search from the search bar
  let searchQuery = vendorSearchBar.value.trim().toLowerCase();

  if(searchQuery.length > 0) {
    let rowsToHide = visibleRows.filter((row) => !row['vendorName'].includes(searchQuery));
    let rowsToShow = hiddenRows.filter((row) => row['vendorName'].includes(searchQuery));

    rowsToHide.forEach((row) => {hideRow(row)});
    rowsToShow.forEach((row) => {showRow(row)});
  } else {
    hiddenRows.forEach((row) => {showRow(row)});
  }
}

/**
 * Hides the provided object, making it no longer display and moving it to the hiddenRows array
 * 
 * @param {Object} vendorRow - the row object created in the initialize function to be hidden
 */
function hideRow(vendorRow) {
  let rowIndex = visibleRows.indexOf(vendorRow);
  visibleRows.slice(rowIndex, rowIndex+1);
  vendorRow['rowElement'].style.display = 'none';
  hiddenRows.push(vendorRow);
}

/**
 * Shows the provided object, making it become visible and moving it to the visibleRows array
 * 
 * @param {Object} vendorRow -the row object created in the initialize function to be shown
 */
function showRow(vendorRow) {
  let rowIndex = hiddenRows.indexOf(vendorRow);
  visibleRows.slice(rowIndex, rowIndex+1);
  vendorRow['rowElement'].style.display = 'table-row';
}
