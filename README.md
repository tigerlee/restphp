restphp
=======

Restful api for php & mysql, according from extjs restful example


Usage
-----

1. Set up file structure

        WWWROOT/restful
        ├── restful.php
        ├── plugins
        │   ├── controllers
        │   │   └── foo.php
        │   └── models
        │       └── foo.php
        └── restphp (*THIS LIBRARY*)
            ├── controller.php
            ├── init.php
            ├── lib
            ├── model.php
            ├── mysql.php
            ├── query.php
            ├── README.md
            ├── request.php
            └── response.php

2. Create file `restful.php` under the `WWWROOT/restful`, paste it:

        <?php
        $plugin_path = dirname(__FILE__) . "/plugins";
        require("restphp/query.php");
        ?>

3. Put below in rewrite rules:

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^rest/.\*$ restful/restful.php

4. Change MySQL configuration in `mysql.php`
5. Restart web server and run `curl http://localhost/rest/TABLE_NAME`
