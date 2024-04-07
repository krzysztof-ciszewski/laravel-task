To set up run:
composer install
php artisan migrate
php artisan serve

Endpoints:
GET /api/activity - get all activities, possible query parameters: location, type, occurred_at_from, occurred_at_to, see tests/Feature/Application/Http/Controllers/GetActivitiesControllerTest.php for examples

POST /api/report - upload report file, parameter name: report, only html file is accepted, the html file attached with the task description will be parsed

Example requests(done after importing the report file):

To get all events between x and y, date format is d-m-Y: 
curl --location '127.0.0.1:8000/api/activity?occurred_at_from=10-01-2022&occurred_at_to=11-01-2022'

To get all flights for the next week (current date can be set to 14 Jan 2022)
curl --location '127.0.0.1:8000/api/activity?occurred_at_from=17-01-2022&occurred_at_to=23-01-2022&type=FLT'

To get all Standby events for the next week (current date can be set to 14 Jan 2022)
curl --location '127.0.0.1:8000/api/activity?occurred_at_from=17-01-2022&occurred_at_to=23-01-2022&type=SBY'

To get all flights that start on the given location
curl --location '127.0.0.1:8000/api/activity?type=FLT&location=KRP'

Tests:
php artisan test
Test coverage 96.2%