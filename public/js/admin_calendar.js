'use strict';

const main = document.getElementsByTagName('main')[0];

let homeLink = document.getElementsByTagName('header')[0].querySelector('a');
let homeLinkURL = homeLink.href;
let a_publicURL = homeLinkURL.substring(0, homeLinkURL.indexOf('/index.php'));

initPageAdminCalendar();

/**
 * Initializes the page by adding the new month button to the bottom of the main as well as the admin-view CRUD buttons to every day.
 */
function initPageAdminCalendar() {
  // Getting all existing months
  let existingMonths = Array.from(document.querySelectorAll('table'));
  // Adds CRUD buttons to every day in the existing months
  existingMonths.forEach(itm => setUpAdminDateCRUD(itm));

  // Grabbing the last table element on the page
  let lastMonthElement = getLastMonth();

  // Reading the last two characters of the month table's data-date attribute to get the numerical value of that month+1 (PHP to JavaScript adds 1)
  let lastMonthValue = lastMonthElement.dataset.date;
  lastMonthValue = lastMonthValue.substring(lastMonthValue.length - 2);

  // Reading the first four characters of the month table's data-date attribute to get the numerical value of that year
  let lastYearValue = lastMonthElement.dataset.date
  lastYearValue = lastYearValue.substring(0, 4);

  // Converting PHP's month(1-12) to JavaScript's month(0-11)
  if(lastMonthValue > 11){
    lastYearValue++;
    lastMonthValue = 0;
  }

  // Creating the new month button and adding it to the page
  let monthButton = createMonthButton(lastYearValue, lastMonthValue);
  main.appendChild(monthButton);


}

/**
 * Gets a list of table cell elements representing all calendar days that are NOT market days.
 * 
 * @returns {Element[]} the list of all non market days
 */
function getNonMarketDays(){
  return Array.from(document.querySelector('main').querySelectorAll('td:not(.market_day)'));
}

/**
 * Gets a list of table cell elements representing all calendar days that are market days.
 * 
 * @returns {Element[]} the list of all market days
 */
function getMarketDays(){
  return Array.from(document.querySelectorAll('.market_day'));
}

/**
 * Finds the final month table element on the page.
 * 
 * @returns {HTMLTableElement} the last month on the page
 */
function getLastMonth(){
  let months = Array.from(document.getElementsByTagName('table'));
  return months[months.length -1];
}

/**
 * Creates a populated table element for a given month and year.
 * 
 * @param {int} year - the 4 digit year to use
 * @param {int} month - the month to use, January = 0, December = 11
 * 
 * @return {HTMLTableElement} the newly constructed month table
 */
function createMonthElement(year, month){

  let new_month = new Date(year, month);

  let table = document.createElement('table');
  month++;
  if(month < 10) {
    month = '0' + month;
  }
  table.dataset.date = `${year}-${(month)}`;
  month--;

  let caption = document.createElement('caption');
  caption.textContent = new_month.toLocaleString('default', { month: 'long' });
  table.appendChild(caption);

  let tbody = document.createElement('tbody');
  table.appendChild(tbody);

  // Header row for days of the week
  let headerRow = document.createElement('tr');
  tbody.appendChild(headerRow);

  // The days of the week as table headers
  let mondayHeader = document.createElement('th');
  mondayHeader.textContent = "Monday";
  headerRow.appendChild(mondayHeader);

  let tuesdayHeader = document.createElement('th');
  tuesdayHeader.textContent = "Tuesday";
  headerRow.appendChild(tuesdayHeader);

  let wednesdayHeader = document.createElement('th');
  wednesdayHeader.textContent = "Wednesday";
  headerRow.appendChild(wednesdayHeader);

  let thursdayHeader = document.createElement('th');
  thursdayHeader.textContent = "Thursday";
  headerRow.appendChild(thursdayHeader);

  let fridayHeader = document.createElement('th');
  fridayHeader.textContent = "Friday";
  headerRow.appendChild(fridayHeader);

  let saturdayHeader = document.createElement('th');
  saturdayHeader.textContent = "Saturday";
  headerRow.appendChild(saturdayHeader);

  let sundayHeader = document.createElement('th');
  sundayHeader.textContent = "Sunday";
  headerRow.appendChild(sundayHeader);


  // Setting up some variables
  let days_in_month = new Date(year, month + 1, 0).getDate();
  let day_counter = 1;
  let starting_weekday = new Date(year, month, 1).getDay();
  if(starting_weekday == 0) {
    starting_weekday = 7;
  }
  let weekday_counter = 1;

  // Creates the first table row
  let firstWeek = document.createElement('tr');
  tbody.appendChild(firstWeek);

  // Adds empty day cells if the month doesn't start on a monday
  while(weekday_counter < starting_weekday) {
    let empty_day = document.createElement('td');
    empty_day.className = "empty";
    firstWeek.appendChild(empty_day);
    weekday_counter++;
  }

  // Adds in day cells to the first week
  while(weekday_counter < 8) {
    let new_day = createDayElement(day_counter);
    firstWeek.appendChild(new_day);
    weekday_counter++;
    day_counter++;
  }

  weekday_counter = 1;

  // Non first weeks
  while(day_counter <= days_in_month) {
    let new_week = document.createElement('tr');
    tbody.appendChild(new_week);

    while(weekday_counter < 8) {

      if(day_counter <= days_in_month) {
        let new_day = createDayElement(day_counter);
        new_week.appendChild(new_day);
        weekday_counter++;
        day_counter++;
      } else {
        let empty_day = document.createElement('td');
        empty_day.className = "empty";
        new_week.appendChild(empty_day);
        weekday_counter++;
      }
    } // End Day loop
    weekday_counter = 1;
  } // End Week loop

  return table;
} // End createMonthElement


/**
 * Creates a new day td cell for a new month and populates it with the appropriate child content.
 * 
 * @param {int} day_counter - the current day within the month
 * 
 * @returns {HTMLElement} the table cell of the new day
 */
function createDayElement(day_counter) {
  let new_day = document.createElement('td');

  let dateSpan = document.createElement('span');
  dateSpan.classList.add('day-counter');
  dateSpan.textContent = day_counter;
  new_day.appendChild(dateSpan);

  let dayContentDiv = document.createElement('div');
  dayContentDiv.classList.add('day-content');
  new_day.appendChild(dayContentDiv);

  return new_day;
}

/**
 * Creates a clickable link element that creates a new month table when clicked.
 * 
 * @param {int} year - the 4 digit year to create
 * @param {int} month - the month to create, January = 0, December = 11
 * 
 * @returns {HTMLAnchorElement} the clickable link element
 */
function createMonthButton(year, month){
  // Creating the date object
  let new_month = new Date(year, month);

  // Creating the link to interact with
  let month_link = document.createElement('a');
  // Styling the link
  // month_link.style.fontSize = "1.5rem";

  // Getting the name of the month an using it in the link's text content
  let month_name = new_month.toLocaleString('default', { month: 'long' });
  month_link.textContent = `Click to add ${month_name} below.`;
  // Adding data attributes to the link
  month_link.dataset.year = year;
  month_link.dataset.month = month;
  // Adding a click event listener to create a
  month_link.addEventListener('click', handleNewMonthClick);

  // Returning the anchor element
  return month_link;
}

/**
 * Triggers when the new month button is clicked, adds a new month table and an additional new month button to the page.
 */
function handleNewMonthClick() {
  // Gets the link element triggering this function
  let old_link = this;

  // Getting the year and month data for the table
  let year = old_link.dataset.year;
  let month = old_link.dataset.month;

  // If the new month being made is January, adds a new year heading to the document
  if(month <= 0){
    let new_year_heading = document.createElement('h3');
    new_year_heading.textContent = year;
    main.appendChild(new_year_heading);
  }

  // Creates the new month table element
  let new_month_table = createMonthElement(year, month);

  // Incrementing the month value after the table is created
  month++;
  // Incrementing the year if the new month is January
  if(month > 11){
    year++;
    month = 0;
  }
  // Creates the next new month button element
  let new_month_button = createMonthButton(year, month);

  // Adding the elements to the document
  main.appendChild(new_month_table);
  main.appendChild(new_month_button);
  // Adding the admin crud buttons
  setUpAdminDateCRUD(new_month_table);

  // Removes the old link
  old_link.remove();
} 

/**
 * Adds buttons for creating and removing CalendarDate days in every day of a given month.
 * 
 * @param {HTMLTableElement} month_element the month to add CRUD buttons to
 */
function setUpAdminDateCRUD(month_element){
  // Reading the last two characters of the month table's data-date attribute to get the numerical value of that month (1-12)
  let monthValue = month_element.dataset.date;
  monthValue = monthValue.substring(monthValue.length - 2);

  // Reading the first four characters of the month table's data-date attribute to get the numerical value of that year
  let yearValue = month_element.dataset.date
  yearValue = yearValue.substring(0, 4);

  // Getting a list of existing market day td elements within the month
  let marketDays = Array.from(month_element.querySelectorAll('.market_day'));
  // Getting a list of days not marked as market days
  let nonMarketDays = Array.from(month_element.querySelectorAll('td:not(.market_day):not(.empty)'));

  nonMarketDays.forEach(itm => addAdminCreateButton(itm, yearValue, monthValue));
  marketDays.forEach(itm => addAdminDeleteButton(itm, yearValue, monthValue));
  marketDays.forEach(itm => addAdminEditButton(itm, yearValue, monthValue));
}

/**
 * Creates a button adding this day as a date in the database.
 * 
 * @param {Element} tableCellElement - the table cell containing the date
 * @param {int} year - the year of the given date
 * @param {int} month - the month of the given date
 */
function addAdminCreateButton(tableCellElement, year, month){
  // Getting this table cell's date
  let day = tableCellElement.querySelector('span').textContent;
  let fullDateString = year.concat('-', month, '-', day);

  // Creating the new link button
  let newLink = document.createElement('a');
  newLink.href = a_publicURL + '/calendar/create_day.php?date=' + fullDateString;
  newLink.innerHTML = '<span>+</span>';
  newLink.className = 'create-button';

  let dayContentDiv = tableCellElement.querySelector('.day-content');
  dayContentDiv.appendChild(newLink);
}

/**
 * Creates a button removing this day as a date from the database.
 * 
 * @param {Element} tableCellElement - the table cell containing the date
 * @param {int} year - the year of the given date
 * @param {int} month - the month of the given date
 */
function addAdminDeleteButton(tableCellElement, year, month){
  // Getting this table cell's date
  let day = tableCellElement.querySelector('span').textContent;
  let fullDateString = year.concat('-', month, '-', day);

  // Creating the new link button
  let newLink = document.createElement('a');
  newLink.href = a_publicURL + '/calendar/delete_day.php?date=' + fullDateString;
  newLink.innerHTML = '<span>-</span>';
  newLink.className = 'delete-button';

  let dayContentDiv = tableCellElement.querySelector('.day-content');
  dayContentDiv.appendChild(newLink);
}

/**
 * Creates a button that links to calendar edit_listings.php.
 * 
 * @param {Element} tableCellElement - the table cell containing the date
 * @param {int} year - the year of the given date
 * @param {int} month - the month of the given date
 */
function addAdminEditButton(tableCellElement, year, month){
  // Getting this table cell's date
  let day = tableCellElement.querySelector('span').textContent;
  let fullDateString = year.concat('-', month, '-', day);

  // Creating the new link button
  let newLink = document.createElement('a');
  newLink.href = a_publicURL + '/calendar/edit_listings.php?date=' + fullDateString;
  newLink.textContent = 'Edit';
  newLink.className = 'edit-button';

  let dayContentDiv = tableCellElement.querySelector('.day-content');
  dayContentDiv.appendChild(newLink);
}
