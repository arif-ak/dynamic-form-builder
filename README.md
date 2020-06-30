Dynamic Form Builder
======
* A dynamic form builder web app, developed using Symfony 4.4

## System requirments / dependencies:
* Apache running on a linux server [512MB+ Ram] 
* PHP 7.3 
* Composer
* MySQL  

## Project setup 
* run the command "composer install" 
* run the command "php bin/console cache:clear"
* run the command "php bin/console doctrine:database:create"
* run the command "php bin/console doctrine:schema:update --force" 
* run the command "php bin/console doctrine:fixtures:load --append" to load default  

## Project running
* run command "bin/console server:run" to run the application in your system
* access url provided from terminal or run locally

## Project  description
**Admin login**
* For admin login, use email -> 'admin@company.com' and password -> 'password'
* Admin has option to create a new form by clicking on 'Create a new form'
* Admin has option to edit a new form by clicking on 'Edit' against the form in the list
* Admin can upload images in form creation and form edit page only.
* Admin can view uploaded images in 'Show' form option and 'Edit' form option
* Clicking on 'Show' against a form shows the uploaded images and a question creation option below
* Admin can create any number and any type of questions in any order (except for datetime picker)
* Admin can create only one datetime picker per form.
* 'x' buttons directly deletes an entry from database (question or choice)
* Questions cannot be deleted if response has already been recorded for that question
* Admin can create any number of choices for multiple choice and multiple selection based questions
* Admin can check responses of the form (grouped by users), after clicking on 'Show responses'
* Admin can delete choices at any given time
* Admin can 'publish' forms so that users that are registered in the system can view the new form.
* Admin can hide 'published' forms as and when required. users stop seeing the form in the list once admin hides the form.

**User login**
* For user login, use email -> 'johndoe@company.com' and password -> 'password'
* Users can see all published forms in forms listing page
* Users can click on 'View form' to enter his response
* Users can re-visit the same link to see already published data

**User registration**
* Users can register by clicking on 'Register' button on the login page.
* Users will receive an email with their password.
* Users will also receive a flash message with their password. This flash message is created as a means of knowing the password without depending on email (this proves helpful in an evenet where there might be a problem in mailing from the hosted server). This feature is to be removed when pushing the code to production.
* Developers may have to modify 'MAILER_URL' in .env file to receive registration emails in dev environment. An example will be given below:
* MAILER_URL=gmail://someone@gmail.com:password@localhost
* Developers may also be required to switch on 'Allow less secure apps' in Gmail to use emails for development
* 'https://myaccount.google.com/lesssecureapps' : Visit this link to get email access for your app
* The lack of emails is not a show stopper for the web app as we get the password via flash messages post-registration

**Role access (Custom exception pages included)**
* Two roles existing in the system are ROLE_USER and ROLE_ADMIN
* ROLE_USER will get 'Access Denied' message, in the event of accessing an ADMIN URL
* Custom html error pages have been developed to help user browse through the application

**Backup plan**
* In the event that the developer is not able to pre-load data to create an admin account, he/she could directly browse to 'base_url/createadmin'. Keep in mind this is strictly ill-advised and to be used for emergencies only. Credentials are same as the ones mentioned under 'Admin Login' section.
* In the event that the developer is not able to pre-load data to create a user account, he/she could directly browse to 'base_url/createuserjohndoe'. Keep in mind this is strictly ill-advised and to be used for emergencies only. Credentials are same as the ones mentioned under 'User Login' section.


