## MiniAspire - Laravel Project

The project focuses on creating a mini version of Aspire so that the candidate can think about the systems and architecture the real project would have.

The task is defined below:

 - Build a simple API that allows to handle user loans.
 - Necessary entities will have to be (but not limited to): users, loans, and repayments.
 - The API should allow simple use cases, which include (but are not limited to): creating a new user, creating a new loan for a user, with different attributes (e.g. duration, repayment frequency, interest rate, arrangement fee, etc.), and allowing a user to make repayments for the loan.
 - The app logic should figure out and not allow obvious errors. For example a user cannot make a repayment for a loan that’s already been repaid.

## Installation Instructions
- Download the codebase and then go to that folder and run below commands one by one.
- Run `composer install`
- Run `php artisan optimize`
- Run `php artisan key:generate`
- Run `php artisan migrate`

## API Documentation

- [Postman Collection](https://www.getpostman.com/collections/5d4f13821c89e53c02fd)
- Copy above link and import in postman.


## Third-party Packages Used

- [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum)

## TODO

- Create Database
- Update DB information in .env file
- Please import above api json in postman.
- Create user
- Login using created user credential
- You will get token in response once successful login and then use that token in header with other rest of apis.

## API Documentation
#Note: Mentioned parameters in postman collection for per request is required param.

## Without token
- User Registeration
- User Login
## End Without Token

## With Token
Header Information should pass as below.
- Accept:application/json
- Authorization: Bearer {Token}(Token will get from login api)

- User Profile Get
- Create Loan
- Get All Loans of perticular customer
- Get Specific loan details
- Create repayment
## End With Token
