# Duktig.Microservice
## RESTFul API Documentation

### Table of content

Version 1.0.0

 - [Getting started](2-getting-started.md)
 - [General Error responses](3-general-error-responses.md)
 - [Response status codes](4-response-status-codes.md)
 - Test Requests (for Developers) `/tests`
     - Data Validation 
        - [TEST - Validate Array from Json](tests/data_validation/1-validate_array_from_json.md)
        - [TEST - Validate Multidimensional Array from Json](tests/data_validation/2-validate_multidimensional_array_from_json.md)
        - [TEST - Validate form data](tests/data_validation/3-validate_form_data.md)
        - [TEST - Validate GET Request Data](tests/data_validation/4-validate_get_request_data.md)
     - Getting Data 
        - [TEST - Get/Download a file](tests/getting_data/1-get_download_file.md)
        - [TEST - Response all Request data](tests/getting_data/2-response_all_request_data.md)
     - Response data Caching
        - [TEST - Cache response data by system](tests/caching/1-cache_response_data_by_system.md)
        - [TEST - Cache response data manually](tests/caching/2-cache_response_data_manually.md)
 - Resources
     - Authorization 
        - [Get access and refresh tokens](auth/authorize.md) `/auth/token`
        - [Get new access token by refresh token](auth/refresh_token.md) `auth/refresh_token`
     - User `/user`
        - [Register](user/1-register.md)
        - [Get account data](user/2-get-account.md)
        - Update account 
            - [Update complete account data - `PUT` request](user/3-update-put.md)
            - [Update parts of account data - `PATCH` request](user/4-update-patch.md)
     - Users `/users`
        - [Get users](users/1-get-users.md)
        - [Get user account by Id](users/2-get-user-by-id.md)
        - [Register account](users/3-register-account.md)
        - Update account 
            - [Update complete account data - `PUT` request](users/4-update-put.md) 
            - [Update parts of account data - `PATCH` request](users/5-update-patch.md)
        - [Terminate user account](users/6-terminate.md)
        - [Get User actions (Guest and System users included)](users/7-get-user-actions.md)
     - DataReception
        - [General Data Reception - `POST` request](dataReception/1-General.md) `/data-reception/{any}`
     - Application/System Statistics `/stats`
        - [Application Logs](statistics/1-application-logs.md)
        - [User actions](statistics/2-user-actions.md)
     - System `/system`
        - [Ping to system](system/1-ping.md)
        
End of document
