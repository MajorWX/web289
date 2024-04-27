"use strict";

// The select drop down for searching categories
let categorySearchSelect = document.getElementById('product-category');
// The search bar element
let productSearchBar = document.getElementById('product-search');

// All product categories that are visible
let visibleCategories = [];
// All product categories that are hidden by the category searches
let hiddenCategories = [];
// All product categories that are hidden because the have no visible product children
let emptyCategories = [];

// All product rows that are visible
let visibleProducts = [];
// All product rows that are hidden by the product search
let hiddenProducts = [];

initPageProductSearchAdmin();

function initPageProductSearchAdmin() {
  // Getting all table row elements
  let tableRows = Array.from(document.getElementsByTagName('tr'));
  // Removing the table header row
  tableRows.shift();
  // Assigning the rows
  let lastCategory = null;
  while(tableRows.length > 0) {
    // Getting the first remaining row in the tableRows array
    let currentRow = tableRows.shift();

    if(currentRow.childElementCount == 1) {
      // Row is category
      let productCategory = {
        categoryRow: currentRow,
        categoryName: currentRow.querySelector('td').textContent.toLowerCase(),
        childProducts: []
      }
      visibleCategories.push(productCategory);
      lastCategory = productCategory;
    } else {
      // Row is product
      let product = {
        productRow: currentRow,
        productName: currentRow.querySelectorAll('td')[1].textContent.toLowerCase()
      }
      visibleProducts.push(product);
      lastCategory['childProducts'].push(product);
    }
  }

  // Adding event listeners
  categorySearchSelect.addEventListener('change', handleCategorySearch);
  productSearchBar.addEventListener('keyup', handleProductSearch);

  // Preventing the php search from occurring
  let form = document.querySelector('form');
  form.addEventListener('submit', (evt) => evt.preventDefault());
}

/**
 * Takes in the selected category from the category search select element and then filters the displayed categories by that selection or shows all categories if no category is selected.
 */
function handleCategorySearch() {
  // Getting the value of the selected search
  let categoryQuery = categorySearchSelect.value.trim().toLowerCase();

  if(categoryQuery.length > 0) {
    // Find all categories to show and hide, based on their category name
    let categoriesToHide = visibleCategories.filter((productCategory) => !productCategory['categoryName'].includes(categoryQuery));

    categoriesToHide.concat(...emptyCategories.filter((productCategory) => !productCategory['categoryName'].includes(categoryQuery)));

    let categoriesToShow = hiddenCategories.filter((productCategory) => productCategory['categoryName'].includes(categoryQuery));

    // Hide the categories to hide, show the categories to be shown
    categoriesToHide.forEach((productCategory) => hideCategory(productCategory));
    categoriesToShow.forEach((productCategory) => showCategory(productCategory));
  } else {
    // If there is no category selected, show all categories
    let categoriesToShow = [...hiddenCategories];
    categoriesToShow.forEach((productCategory) => showCategory(productCategory));
  }
}

/**
 * Hides the provided category, making it no longer display and moving it to the hiddenCategories array
 * 
 * @param {Object} productCategory - the category object created in the initialize function to be hidden
 */
function hideCategory(productCategory) {
  // If the product is currently visible, remove it from the visibleCategories array and set its display to none
  if(visibleCategories.includes(productCategory)) {
    let categoryIndex = visibleCategories.indexOf(productCategory);
    visibleCategories.splice(categoryIndex, 1);
    productCategory['categoryRow'].style.display = 'none';
    productCategory['childProducts'].forEach((product) => product['productRow'].style.display = 'none');
  } else {
    // Otherwise, just remove it from the emptyCategories array without changing its display
    let categoryIndex = emptyCategories.indexOf(productCategory);
    emptyCategories.splice(categoryIndex, 1);
  }
  
  // Add it to the hidden categories array
  hiddenCategories.push(productCategory);
}

/**
 * Removes a given category from the hiddenCategories array, then making it visible if it is not empty
 * 
 * @param {Object} productCategory - the category object created in the initialize function to be shown
 */
function showCategory(productCategory) {
  // Remove the category from the hidden array
  let categoryIndex = hiddenCategories.indexOf(productCategory);
  hiddenCategories.splice(categoryIndex, 1);

  // If the category is empty, move it to the emptyCategories array
  if(categoryIsEmpty(productCategory)) {
    emptyCategories.push(productCategory);
  } else {
    // If the category is not empty, make it visible and add it to the visibleCategories array
    productCategory['categoryRow'].style.display = 'table-row';
    productCategory['childProducts'].forEach((product) => product['productRow'].style.display = 'table-row');
    visibleCategories.push(productCategory);
  }
}

/**
 * Filters out products and categories based on the content of the search bar.
 */
function handleProductSearch() {
  // Getting the value of the search from the search bar
  let searchQuery = productSearchBar.value.trim().toLowerCase();

  if(searchQuery.length > 0) {
    // Find all the products to show and hide, based on their product names
    let productsToHide = visibleProducts.filter((product) => !product['productName'].includes(searchQuery));
    let productsToShow = hiddenProducts.filter((product) => product['productName'].includes(searchQuery));

    // Hide all products to hide, show all products to be shown
    productsToHide.forEach((product) => hideProduct(product));
    productsToShow.forEach((product) => showProduct(product));

    // Update all non-hidden categories based on emptiness
    [...visibleCategories].forEach((productCategory) => handleCategoryEmptiness(productCategory));
    [...emptyCategories].forEach((productCategory) => handleCategoryEmptiness(productCategory));
  } else {
    // If there is no search query reveal all products
    let productsToShow = [...hiddenProducts];
    productsToShow.forEach((product) => showProduct(product));

    // Make all empty categories visible
    [...emptyCategories].forEach((productCategory) => handleCategoryEmptiness(productCategory));
  }
}

/**
 * Hides the provided product, making it no longer display and moving it to the hiddenProducts array
 * 
 * @param {Object} product - the product object created in the initialize function to be hidden
 */
function hideProduct(product) {
  let productIndex = visibleProducts.indexOf(product);
  visibleProducts.splice(productIndex, 1);
  product['productRow'].style.display = 'none';
  hiddenProducts.push(product);
}

/**
 * Shows the provided product, making it become visible and moving it to the visible Products array
 * 
 * @param {Object} product - the product object created in the initialize function to be shown
 */
function showProduct(product) {
  let productIndex = hiddenProducts.indexOf(product);
  hiddenProducts.splice(productIndex, 1);
  product['productRow'].style.display = 'table-row';
  visibleProducts.push(product);
}

/**
 * Checks if a category has no visible products inside it.
 * 
 * @param {Object} productCategory - the category to check
 * 
 * @returns {boolean} if there are no visible product objects in this category
 */
function categoryIsEmpty(productCategory) {
  let isEmpty = true;

  productCategory['childProducts'].forEach((product) => {
    if(visibleProducts.includes(product)) {
      isEmpty = false;
      return isEmpty;
    }
  });
  return isEmpty;
}

/**
 * Checks if a given category should be moved to the empty or visible array then does so.
 * 
 * @param {Object} productCategory - the category to check
 */
function handleCategoryEmptiness(productCategory) {
  // If the product is empty but currently visible
  if(categoryIsEmpty(productCategory) && visibleCategories.includes(productCategory)) {
    // Hide the category and move it to the emptyCategories array
    let categoryIndex = visibleCategories.indexOf(productCategory);
    visibleCategories.splice(categoryIndex, 1);
    productCategory['categoryRow'].style.display = 'none';
    productCategory['childProducts'].forEach((product) => product['productRow'].style.display = 'none');
    emptyCategories.push(productCategory);
  } 
  // Otherwise, if the product isn't empty but is currently listed as empty
  else if ((!categoryIsEmpty(productCategory)) && emptyCategories.includes(productCategory)) {
    let categoryIndex = emptyCategories.indexOf(productCategory);
    emptyCategories.splice(categoryIndex, 1);
    productCategory['categoryRow'].style.display = 'table-row';
    productCategory['childProducts'].forEach((product) => product['productRow'].style.display = 'table-row');
    visibleCategories.push(productCategory);
  }
}