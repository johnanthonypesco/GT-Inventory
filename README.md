# How to Make the Project Run

1. run in terminal: `composer install`
2. change .env variables to this:
    ```dotenv
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
    ```
3. run: `php artisan migrate:fresh --seed`
4. Project is now ready for: `php artisan serve`

---
---