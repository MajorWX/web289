# WEB289 - Reynolds Hill Farmers Market
Created by Kira Beitler

This is a database-driven website for a fictional farmers market that allows vendors to list produce and mark their availability for scheduled market days.

This project uses PHP, JavaScript, and CSS, and was tested on a local Apache and MySQL server through XAMPP.

Website Link: https://reynolds-hill-market.majorwx.xyz/public/index.php

## Site Credentials

**Vendor Level User**
* Username: VendorUser
* Password: Web289$$

**Super Admin Level User**
* Username: AdminUser
* Password: Web289$$

## Database Credentials

**Local**
* DB_SERVER: localhost
* DB_USER: farmersMarketUser
* DB_PASS: web289
* DB_NAME: farmers_market

**Web Host:**

Create 'db_credentials.php' in the 'private' folder and define DB_SERVER (will likely be localhost), DB_USER, DB_PASS, and DB_NAME to match your web hosted database. You will also need to uncomment line 30 in /private/initialize.php.

## Populating the Database
1. Create a database with the name farmers_market
2. Create a database user with the name farmersMarketUser and password web289 and give them full perms on farmers_market
3. Import the provided SQL file while using the farmers_market database, preferably by using phpMyAdmin

## Notable Errors
Your web host may not properly handle the URL for the text file home-content.txt, my solution was to manually write out the URL for the filepath directly.