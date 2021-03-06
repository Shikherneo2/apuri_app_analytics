Cretin App Analytics
-----------------------------

This web-app was created for a Japanese Company I was working for, but was never published because of, well, reasons.

It was part of a project to promote Partner companies' apps to users who bought phones through our company. This web-app is an analytics and management tool, for tracking installations on users' phones.

Here is a simplistic structure - 
------------------------------

1. The Super admin - Unlike what the name suggests, the super admin has a single job - to manage Admins.

2. An Admin can - Manage franchise, shops, shopkeepers, shop-employees, as well as add or remove apps.

3. A Franchise owns a string of shops and has the power to manage all shops, shop keepers and shop employees under him.

4. A Shopkeeper has control of a single shop, and everything related to it.

5. A shop employee does not have the ability to manage any shops or employees.

6. Users are only allowed to view analytics within their own sphere of control. For eg. a Franchise can only see analytics data of installations made on phones sold from his shops.

Structure of the app - 
------------------------------

1. The back-end is written in Laravel and MySQL.

2. The front-end uses jQuery and the ink framework.    

3. Server side Data-table is used to display information.

Instructions on Setting up
------------------------------

1. Choose the language you want to use, put that folder on your server's directory(only required files will be open to access), and rename the folder to "apuri_app_analytics".

2. The database can be setup by simply runnning the migrations. The database credentials can be setup by editing the .env file.

3. The analytics data was supposed to be sent by the phone app. So to view the analytics now, you need to manually enter data in the analytics table. Or just setup your own API.

4. In order to jump-start, setup a super admin with a numeric ID, Login type as 2, and a hashed password. Just put the userid, login_type and password in the login table.

5. The hashed password can be setup by using the Hash::make() function provided by Laravel. Or you can use this hash, created from the password "the_north_remembers" -- "$2y$10$m6ncasNc8kYyvg1WTRn4oOX44yUJRK0d45X.8vnngmt3shOrpuY6a"

6. Here are the login types for all users

    Super Admin -2
    Admin - 3
    Franchise - 4
    Shopkeeper - 5
    Shop Employee - 6

7. The default password is "spiderman".

8. The id is what is shown in the records below, when you create a new user (Except the admin, where you specify the ID).

You can use the project as you wish, without any restrictions.
