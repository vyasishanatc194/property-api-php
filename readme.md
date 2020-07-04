
## About Property System

- Property Listing with search and filter option
- Property Add,Edit & Delete functions with jquery/php validation
- Property Fetch from API 
- On fetch property from API it will create or update record in local database (property_type & property)
- Image upload with generate thumbnail 
- Auto generate UUID for property


## Install Property System(php)

- Create database and import the file 'property_system.sql'
- Set credential in config.php file at root of project folder 


## Important Config Variables

API_KEY='3NLTTNlXsi6rBWl7nYGluOdkl2htFHug' <br />
API_MAX_CALL=5 <br />
API_PER_PAGE=100 <br />
DATA_LISTING_PER_PAGE=10 <br />
API_BASE_URL='http://trialapi.craig.mtcdevserver.com/api' <br />
TABLE_PROPERTY = "properties" <br /> 
TABLE_PROPERTY_TYPE = "property_type" <br />
