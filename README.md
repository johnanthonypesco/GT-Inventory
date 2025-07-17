# RMPOIMS
<p style="color: gray; margin: -20px 0 38px; font-style: italic"> Your one and only solution to ordering and inventory management! <br><br> Aptly named as "RCT Med Pharma: Ordering and Inventory Management System" it is designed and specialized for Pharmaceutical Companies to aid & ease the some of the company's operations. </p>

|ROLES|MEMBERS|
|:--------:|:------:|
|**PROJECT MANAGER / BACKEND / DEVOPS**|SIGRAE DERF E. GABRIEL|
|**BACKEND / LEAD MOBILE DEV**|ROMARK A. BAYAN|
|**FRONTEND / UI & UX**|JOHN ANTHONY L. PESCO|
|**FULLY STACKED DEV / AI SPECIALIST**|JOHN MICHAEL G. JONATAS|
---
---

# How to Make the Project Run

1. Go to XAMPP's apache php.ini file
2. CTRL + F then find this line: `;extension=gd`
3. Remove the ';', turning it into: `extension=gd`
4. Go Back to the Project Folder
5. run in terminal: `composer install`
6. change .env variables to this:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=*3306
    DB_DATABASE=rmpoims_breeze
    
    SESSION_DRIVER=cookie
    
    MAIL_MAILER=smtp
    MAIL_SCHEME=null
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=arkquestdev@gmail.com
    MAIL_PASSWORD=wxydytlvdlgeirdj
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=arkquestdev@gmail.com
    MAIL_FROM_NAME=RMPOIMS

    GOOGLE_VISION_API_KEY=(((MESSAGE SI KUYA PARA SA API KEY)))
    ```
7. run: `php artisan migrate:fresh --seed`
8. run: `php artisan storage:link`
9. Project is now ready for: `php artisan serve`

---
---

# How to Make the Project Testing Side Work

1. Run these commands to install Pest: 
    ```bash
   
   composer remove phpunit/phpunit
   composer require pestphp/pest --dev --with-all-dependencies
    
    ./vendor/bin/pest --init
    ```
2. Copy the .env file and then paste it.
3. rename the pasted file to: `.env.testing`
4. change database name in .env.testing to: `rmpoims_breeze_test` 
5. run this to create the new Testing database: 
   `php artisan migrate --env=testing`
6. Choose yes to create new DB.
7. run this to fill up the test database with data:
   `php artisan migrate:fresh --seed --env=testing`
8. Now the project is able to run: 
    ```bash 
    php artisan test 
    ```