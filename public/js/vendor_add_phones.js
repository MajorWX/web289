'use strict';

// A counter of all new phone numbers to create a temporarily unique id
let new_phone_elements = 0;

initPageVendorPhone();

/**
 * Adds an event listener to the new phone button that creates a new phone.
 */
function initPageVendorPhone() {
  // The Click to add new phone button
  let newPhoneButton = document.querySelector('.new-phones').querySelector('a');

  newPhoneButton.addEventListener('click', handleNewPhoneClick);
}

/**
 * Creates a new phone form when the new phone button is clicked.
 */
function handleNewPhoneClick() {
  // Gets the link element triggering this function
  let old_link = this;
  
  // Creating the dd element that will store all our inputs
  let new_input_line = document.createElement('dd');
  old_link.insertAdjacentElement('beforebegin', new_input_line);

  // Creating the 'Phone Number:' text
  let number_label = document.createElement('span');
  number_label.textContent = 'Phone Number: ';
  new_input_line.appendChild(number_label);

  // Creating the phone number input field
  let number_input_field = document.createElement('input');
  number_input_field.type = 'text';
  number_input_field.name = 'vendor[new_phone_numbers]['.concat(new_phone_elements,'][phone_number]');
  number_input_field.required = true;
  new_input_line.appendChild(number_input_field);

  // Creating the 'Phone Type:' text
  let type_label = document.createElement('span');
  type_label.textContent = ' Phone Type: ';
  new_input_line.appendChild(type_label);

  // Creating the phone type selection field
  let type_select = document.createElement('select');
  type_select.name = 'vendor[new_phone_numbers]['.concat(new_phone_elements,'][phone_type]');
  type_select.required = true;
  new_phone_elements++;
  new_input_line.appendChild(type_select);

  // Adding all the options
  let option_none = document.createElement('option');
  option_none.value = '';
  option_none.textContent = 'Select a phone type:';
  type_select.appendChild(option_none);

  let option_home = document.createElement('option');
  option_home.value = 'home';
  option_home.textContent = 'Home';
  type_select.appendChild(option_home);

  let option_mobile = document.createElement('option');
  option_mobile.value = 'mobile';
  option_mobile.textContent = 'Mobile';
  type_select.appendChild(option_mobile);

  let option_work = document.createElement('option');
  option_work.value = 'work';
  option_work.textContent = 'Work';
  type_select.appendChild(option_work);

  // Adding the cancel button
  let cancel_button = document.createElement('a');
  new_input_line.appendChild(cancel_button);
  cancel_button.textContent = 'Remove this phone'
  cancel_button.addEventListener('click', handleCancelPhoneClick);
}

/**
 * Deletes this new phone form when the cancel button is clicked.
 */
function handleCancelPhoneClick() {
  // Gets the link element triggering this function
  let old_cancel_button = this;
  // Gets the parent of the button
  let parent_line = old_cancel_button.parentElement;
  // Removes the button
  parent_line.remove();
}
