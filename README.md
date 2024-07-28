# PHP setup:
Please make sure the following lines exist in php.ini:
```
extension=pgsql
extension=pdo_pgsql
```

# DB connection setup:
Replace the content of the file \config\PostgresqlConnect.php at every service 
with the content that was provided privetly.


# How to run:
1. Users service:
   
   php -S localhost:8001 -t ./user-service/public

3. Events service:

   php -S localhost:8002 -t ./event-service/public/

5. Orders service:

   php -S localhost:8003 -t ./order-service/public/



# API Documentation:

https://documenter.getpostman.com/view/11505937/2sA3kaBJZJ
