'use strict';

// An array of all <td>s with the market_day class
// let allDays = Array.from(document.querySelectorAll('td'));
let allShowLinks = Array.from(document.querySelectorAll('.show-link'));
let outer = document.querySelector('#outer');
let inner = document.querySelector('#inner');

initPageCalendarResponse();


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

function handleShowClick(evt) {
  evt.preventDefault();
  let parentCell = this.parentElement;
  let dayContentDiv = parentCell.querySelector('.day-content');
  if(dayContentDiv.childElementCount > 0) {
    outer.style.display = 'block';
    inner.innerHTML = dayContentDiv.innerHTML;
  }
}
