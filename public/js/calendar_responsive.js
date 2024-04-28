'use strict';

// An array of all anchor tags that link to show.php
let allShowLinks = Array.from(document.querySelectorAll('.show-link'));

// The popup menu and the screen covering
let outer = document.querySelector('#outer');
let inner = document.querySelector('#inner');

initPageCalendarResponse();

/**
 * Initializes the page by adding event listeners to the show links and the popup menu's screen covering, as well as styling the links' cursors
 */
function initPageCalendarResponse() {
  // Adding a click event to all show links and styling their cursor if they have no content
  allShowLinks.forEach((element) => {
    element.addEventListener('click', handleShowClick);
    let parentCell = element.parentElement;
    let dayContentDiv = parentCell.querySelector('.day-content');
    if(dayContentDiv.childElementCount <= 0) {
      element.style.cursor = 'default';
    }
  });

  // Adding a click event to the outer div to hide it on click
  outer.addEventListener('click', () => outer.style.display = 'none');
}

/**
 * Opens up the popup display of the clicked on cell.
 * 
 * @param {Event} evt - The Event triggering this function
 */
function handleShowClick(evt) {
  evt.preventDefault();
  let parentCell = this.parentElement;
  let dayContentDiv = parentCell.querySelector('.day-content');
  if(dayContentDiv.childElementCount > 0) {
    outer.style.display = 'block';
    inner.innerHTML = dayContentDiv.innerHTML;
  }
}
