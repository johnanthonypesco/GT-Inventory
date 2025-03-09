# How to Make the Project Run

1. Go to XAMPP's apache php.ini file
2. CTRL + F then find this line: `;extension=gd`
3. Remove the ';', turning it into: `extension=gd`
4. Go Back to the Project Folder
5. run in terminal: `composer install`
6. change .env variables to this:
    ```dotenv
    - DB_CONNECTION=mysql
    - DB_HOST=127.0.0.1
    - DB_PORT=3306
    - DB_DATABASE=rmpoims_breeze
    
    - SESSION_DRIVER=cookie
    
    - MAIL_MAILER=smtp
    - MAIL_SCHEME=null
    - MAIL_HOST=smtp.gmail.com
    - MAIL_PORT=587
    - MAIL_USERNAME=arkquestdev@gmail.com
    - MAIL_PASSWORD=wxydytlvdlgeirdj
    - MAIL_ENCRYPTION=tls
    - MAIL_FROM_ADDRESS=arkquestdev@gmail.com
    - MAIL_FROM_NAME=RMPOIMS

    GOOGLE_VISION_API_KEY=(((MESSAGE SI KUYA PARA SA API KEY)))
    ```
7. run: `php artisan migrate:fresh --seed`
8. run: `php artisan storage:link`
9. Project is now ready for: `php artisan serve`

---
---
