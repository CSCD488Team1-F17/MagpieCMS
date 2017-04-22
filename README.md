# magpie-cms
Team Members: Nick Duke, Brad Howard, Vlad Sinitsa, Sage Zaugg

Support Members: Meg Lybbert, Matt Jett

Client: Ginelle Hustrulid

# Description
This project is for EWU CSCD-488 Senior Project. The goal is to create a web application that allows for easy content creation and management for the Magpie application while simutaniously serving the content to the mobile app.

# Setting up local development server

Before downloading the project, you must download and install the following:

- [XAMPP with PHP 7.x](https://www.apachefriends.org/index.html)
- [Composer](https://getcomposer.org/download/)
- [cacert.pem](https://curl.haxx.se/docs/caextract.html)

Now that you have the server installed, copy ```cacert.pem``` to ```C:\xampp\php\extras\ssl\``` ... This is for the Google API.

With Composer installed for package management, go ahead and clone the repository into a folder of your choice:

```
git clone https://github.com/EWU488W17Team9/magpie-cms
```

Next we need to download our vendor files (which includes Slimframework and Google API files). Navigate to ```app/``` and execute this command in your terminal:

```
composer install
```

Now we need to change some settings in XAMPP. Note that some of this is purely for convienence and not required, but highly recommended.

In Apache config, modify ```httpd.conf``` and ```httpd-ssl.conf``` and change all occurences of ```"C:\xampp\htdocs"``` to ```<your-git-clone-location>/app/public_html```

Also in Apache config, open ```php.ini``` and underneath ```php.ini Options``` write ```curl.cainfo = "C:\xampp\php\extras\ssl\cacert.pem"```

Lastly, you need to download the ```client_secret.json``` file for the Google API Credentials. As of now, we do not have a good method of sharing this. So for now, please email one of the members to download the file. Once downloaded, place it somwhere secure and modify ```credentialsFile``` in ```config.php``` to point to it.