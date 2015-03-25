# Wuersch - REST Backen

###### Features
 - HMAC-based Auth ([See wiki](http://en.wikipedia.org/wiki/Hash-based_message_authentication_code))
 - JSON-Based
 - Facebook-Integration

### URL Design
Example: http://localhost/wuersch/backend/user/random
- /wuersch/backend/
  - just the basic entry path
- user
  - the controller/category
- random
  - the action within the controller/category


# Methods
| Controller | Action       | Requires Auth | Method | GET-Args | POST-Args | Returns |
|------------|--------------|:-------------:|--------|----------|-----------|---------|
| auth       | register     | no            | GET    | secret   | None      | tbd     |
| auth       | fetch        | yes           | GET    | None     | None      | tbd     |
| user       | current      | yes           | GET    | None     | None      | tbd     |
| user       | random       | yes           | GET    | None     | None      | tbd     |
| user       | settings     | yes           | POST   | None     | Settings  | tbd     |
| picture    | None         | yes           | GET    | idUser   | None      | tbd     |
| would      | would        | yes           | GET    | idUser   | None      | tbd     |
| would      | wouldNot     | yes           | GET    | idUser   | None      | tbd     |

### Detailed
- auth/register
  - Registers a user. Make sure you generate a good secret (some real randomness). You will get instruction about what to do with the created user (e.g. open URL in browser/webview).
- auth/fetch
  - After a successful regristration and authentication against Facebook you can then download all the profile-pictures (these may be multiple one. Choose default via user/settings). Just authenticate and the API will do the rest.
- user/current
  - Probably just for debugging used: Returns the authenticated user
- user/random
  - This returns a random user of interesset (based on gender). This is not yet very well implemented, but it works and for the current use it's fine.
- user/settings
  - This allows to set the settings. Mainly: what the user is interested in (gender) and profile pictrue. This has to be fully implemented!
- picutre
  - This returns the image of a user based on given idUser.
- would
  - This will mark that the user would do the other user (provided by idUser)
- would/not
  - This will mark that the user would NOT do the other user (provided by idUser)
