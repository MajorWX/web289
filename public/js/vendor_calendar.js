'use strict';

let allMarketDays = Array.from(document.querySelectorAll('.market_day'));

const loginButton = document.querySelector('#logged-in');
let vendorName = loginButton.querySelectorAll('a')[0].textContent;
let vendorPage = loginButton.querySelectorAll('a')[0].href;
let vendorID = vendorPage.substring(vendorPage.indexOf('=') + 1);
let publicURL = vendorPage.substring(0, vendorPage.indexOf('/vendors'));

// console.log("test");
// console.log(vendorName);
// console.log(vendorID);
// console.log(publicURL);

// Find days with vendors and without
let daysWithVendors = [];
let daysWithoutVendors = [];

// For each market day td
allMarketDays.forEach(function (el) {
  let listedVendors = [];
  // Get the content of every li inside each market day td
  el.querySelectorAll('li').forEach(function (el) {
    listedVendors.push(el.textContent);
  });
  if(listedVendors.includes(vendorName)){
    daysWithVendors.push(el);
  } else {
    daysWithoutVendors.push(el);
  }
});

// Setting up the forms on days without vendors
daysWithoutVendors.forEach(function (el){
  let link = document.createElement('a');
  link.textContent = 'Sign up for this day.';
  link.href = publicURL + '/calendar/create.php?id=' + vendorID + '&date=' + el.dataset.date;
  el.appendChild(link);
});

// Setting up the forms on days with vendors
daysWithVendors.forEach(function (el){
  let link = document.createElement('a');
  link.textContent = 'Retract availability for this day.';
  link.href = publicURL + '/calendar/delete.php?id=' + vendorID + '&date=' + el.dataset.date;
  el.appendChild(link);
});