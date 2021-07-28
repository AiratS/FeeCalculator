## Fee calculator application.
***

### How to build the application

*To run containers as your current host machine user, create file `.env` in `<application-root>/docker` directory and define the content below:*

> USER_ID=1000 <br/>
> GROUP_ID=1000

*Replace the numbers assigned to `USER_ID` and `GROUP_ID`  to `UID` and `GID` of your host machine current user.*

Application installation:
- Run  `docker-compose build` in `<application-root>/docker` directory to build containers.<br/>

### How to run the application

Run `docker-compose run app php app.php app:calculate-fee input.csv` in `<application-root>/docker` directory

### How to run tests

Run `docker-compose run app ./vendor/bin/phpunit` in `<application-root>/docker` directory
