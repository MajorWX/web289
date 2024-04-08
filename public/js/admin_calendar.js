'use strict';

const main = document.getElementsByTagName('main')[0];
// console.log(main);
// let monthApril = getLastMonth();
// console.log(monthApril);
// let lastMonthValue = monthApril.dataset.date;
// console.log(lastMonthValue);
// lastMonthValue = lastMonthValue.substring(lastMonthValue.length - 2);
// let monthMay = createMonthElement(2024, lastMonthValue);
// console.log(monthMay);
// main.appendChild(monthMay);
initPage();

function initPage() {
  let lastMonthElement = getLastMonth();
  let lastMonthValue = lastMonthElement.dataset.date;
  lastMonthValue = lastMonthValue.substring(lastMonthValue.length - 2);
  let lastYearValue = lastMonthElement.dataset.date
  lastYearValue = lastYearValue.substring(0, 4);

  if(lastMonthValue > 11){
    lastYearValue++;
    lastMonthValue = 0;
  }

  let monthButton = createMonthButton(lastYearValue, lastMonthValue);
  main.appendChild(monthButton);

}

function getNonMarketDays(){
  return Array.from(document.querySelector('main').querySelectorAll('td:not(.market_day)'));
}

function getMarketDays(){
  return Array.from(document.querySelectorAll('.market_day'));
}

function getLastMonth(){
  let months = Array.from(document.getElementsByTagName('table'));
  return months[months.length -1];
}

/**
 * 
 * @param {int} year 
 * @param {int} month - January = 0, December = 11
 */
function createMonthElement(year, month){

  let new_month = new Date(year, month);

  let table = document.createElement('table');
  month++;
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

  // The days of the week
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
    let new_day = document.createElement('td');
    new_day.textContent = day_counter;
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
        let new_day = document.createElement('td');
        new_day.textContent = day_counter;
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

function createMonthButton(year, month){
  let new_month = new Date(year, month);

  let month_link = document.createElement('a');
  month_link.style.fontSize = "1.5rem";
  let month_name = new_month.toLocaleString('default', { month: 'long' });
  month_link.textContent = `Click to add ${month_name} below.`;
  month_link.dataset.year = year;
  month_link.dataset.month = month;
  month_link.addEventListener('click', handleNewMonthClick);
  return month_link;
}

function handleNewMonthClick(el) {
  // el.prevent.preventDefault();
  let old_link = this;

  let year = old_link.dataset.year;
  let month = old_link.dataset.month;

  let new_month_table = createMonthElement(year, month);

  month++;
  if(month > 11){
    year++;
    month = 0;
  }
  let new_month_button = createMonthButton(year, month);

  main.appendChild(new_month_table);
  main.appendChild(new_month_button);
  old_link.remove();
} 