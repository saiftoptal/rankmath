# Cool Kids Network Plugin API Documentation

## Overview

The Cool Kids Network plugin exposes a secure REST API endpoint to update user roles. This endpoint is intended for administrative use, allowing an external system or tool to update a user's role by identifying the user via their email address or by first and last name.

> **Important:**  
> This API uses a hardcoded authentication mechanism for simplicity. Every request must include the proper `Authorization` header as described below.

## Endpoint Details

- **URL:** `/wp-json/cool-kids-network/v1/role-assignment`
- **Method:** `POST`

> **To simplify:**  
> As the demo site is hosted at https://rankmath.saif.london, the full URL for the endpoint is: https://rankmath.saif.london/wp-json/cool-kids-network/v1/role-assignment 

## Authentication

Every request to this endpoint **must** include the following HTTP header:

```http
Authorization: Bearer SuperSecretAPIAuthenticationToken
```

If the header is missing or the token does not match, the API will return an unauthorized error.

## Request Parameters

The API accepts the following JSON parameters in the request body:

* `role` (string, required): 
The new role to assign to the user. Valid values are:
    - `cool_kid`
    - `cooler_kid`
    - `coolest_kid`

* `email` (string, optional):
The email address of the user whose role is to be updated.
    - `first_name` (string, optional):
  The first name of the user (used together with `last_name` if `email` is not provided).
  - `last_name` (string, optional):
    The last name of the user (used together with `first_name` if `email` is not provided).

  
**Note:**
Either an email or a combination of first_name and last_name is required to identify the user.

## Example Request

### Using cURL

```bash
curl -X POST https://rankmath.saif.london/wp-json/cool-kids-network/v1/role-assignment \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SuperSecretAPIAuthenticationToken" \
  -d '{
    "email": "user@example.com",
    "role": "coolest_kid"
  }'
```

### Using JavaScript (Fetch API)

```javascript
fetch('https://rankmath.saif.london/wp-json/cool-kids-network/v1/role-assignment', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer SuperSecretAPIAuthenticationToken'
  },
  body: JSON.stringify({
    email: 'user@example.com',
    role: 'coolest_kid'
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

## Response Format

On success, the API returns a JSON object with the following structure:

```json
{
  "success": true,
  "message": "User role has been updated.",
  "user_id": 123,
  "new_role": "coolest_kid"
}
```

On failure, an error response is returned. For example, if an invalid role is provided:

```json
{
  "code": "invalid_role",
  "message": "Role must be one of: cool_kid, cooler_kid, coolest_kid",
  "data": {
    "status": 400
  }
}
```

## Error Handling

The API returns appropriate HTTP status codes along with error messages in the response body. The following status codes are used:

- `200 OK`: The request was successful.
- `400 Bad Request`: The request was invalid or malformed.
- `401 Unauthorized`: The request lacks proper authentication.
- `403 Forbidden`: The request is not allowed due to insufficient permissions.
- `404 Not Found`: The requested resource was not found.

## Security Considerations

- **Authentication:** Always include the `Authorization` header with the correct token.
- **HTTPS:** Always use HTTPS to encrypt data transmitted between the client and server.
- **Token Security:** Keep the API token secure and do not expose it in client-side code.
- **Logging:** Log all API requests and responses for auditing and debugging purposes.

## Summary

The Cool Kids Network plugin provides a secure REST API endpoint for updating user roles. This endpoint requires proper authentication and provides error responses for invalid requests. By following the guidelines outlined in this documentation, you easily modify user roles for the Cool Kids Network.