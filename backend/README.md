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

### Returns
The backend basically returns in following structure:
```
["status"]=>string(2) "OK"
["statusMessage"]=>string(9) "All good."
["responses"]=> array(1) { ... }
```
The status is either "OK" or "FAIL" indicating whetever the overall call was successful. The statusMessage may provide additional information about the request and can be useful in cases of bug-reports or debugging.

The responses are Objects of a type and can be mixed. As example: as responses you could have a user1 and user2 as well as user and his pictures.

### Domain
The **Registration** object:
```
["id"]=>string(32) "6512bd43d9caa6e02c990b0a82652dca"
["authenticationURL"]=>string(90) " ... "
```

The **User** object:
```
["id"]=>string(32) "c4ca4238a0b923820dcc509a6f75849b"
["name"]=>string(14) " ... "
["lastSeen"]=>int(1427387900)
["isMale"]=>bool(true)
["isFemale"]=>bool(false)
["interestedInMale"]=>bool(false)
["interestedInFemale"]=>bool(true)
```

The **Picture** object:
```
["id"]=>string(32) "eccbc87e4b5ce2fe28308fd9f2a7baf3"
["isDefault"]=>bool(false)
["time"]=>int(0)
```

The **Would** object:
```
["idUserWould"]=>string(32) "c4ca4238a0b923820dcc509a6f75849b"
["idUser"]=>string(32) "eccbc87e4b5ce2fe28308fd9f2a7baf3"
["would"]=>bool(true)
["time"]=>int(1427386098)
```

The **Match** object
```
["idUser1"]=>string(32) "c4ca4238a0b923820dcc509a6f75849b"
["idUser2"]=>string(32) "eccbc87e4b5ce2fe28308fd9f2a7baf3"
["time"]=>int(1427386098)
```



### Methods
| Controller | Action       | Requires Auth | Method | GET-Args  | POST-Args |
|------------|--------------|:-------------:|--------|-----------|-----------|
| auth       | fetch        | yes           | GET    | None      | None      |
| match      | is           | yes           | GET    | idUser    | None      |
| match      | get          | yes           | GET    | None      | None      |
| picture    | None         | yes           | GET    | idUser    | None      |
| picture    | get          | yes           | GET    | None      | None      |
| picture    | default      | yes           | GET    | idPicture | None      |
| user       | register     | no            | GET    | secret    | None      |
| user       | current      | yes           | GET    | None      | None      |
| user       | random       | yes           | GET    | None      | None      |
| user       | settings     | yes           | POST   | None      | Settings  |
| would      | None         | yes           | GET    | idUser    | None      |
| would      | not          | yes           | GET    | idUser    | None      |
| would      | get          | yes           | GET    | None      | None      |

### Description
- auth/fetch
  - After a successful regristration and authentication against Facebook you can then download all the profile-pictures (these may be multiple one. Choose default via user/settings). Just authenticate and the API will do the rest.
- match/is
  - Checks if provided user has a match with the current user.
- match/get
  - Returns all the matches of the current user
- picutre
  - This returns the image of a user based on given idUser.
- picture/get
  - Returns all the pictures of the current user, which have been downloaded previousely.
- picture/default
  - Sets a picture as the default
- user/register
  - Registers a user. Make sure you generate a good secret (some real randomness). You will get instruction about what to do with the created user (e.g. open URL in browser/webview). 
- user/current
  - Probably just for debugging used: Returns the authenticated user
- user/random
  - This returns a random user of interesset (based on gender). This is not yet very well implemented, but it works and for the current use it's fine.
- user/settings
  - This allows to set the settings. Mainly: what the user is interested in (gender) and profile pictrue. This has to be fully implemented!
- would
  - This will mark that the user would do the other user (provided by idUser)
- would/not
  - This will mark that the user would NOT do the other user (provided by idUser)

### Registration
The backend works with the PHP Facebook SDK. For authenticating a new user you have to create a unique and good random password (e.g 32 alphanumeric). With this newly created secret you can register in the backend and you will get a client-id. From that point on the secret should stay an absolute secret and never be sent or displayed in any form and stored locally in a safe manner.
Example:
```
HTTP GET backend/user/register?secret=CEcUthecHUpHutekequpUChehaDuFa5u
```
Returns:
```
["id"]=>string(32) "a87ff679a2f3e71d9181a67b7542122c"
["authenticationURL"]=>string(90) "http://localhost/wuersch/backend/auth/authenticate?idUser=a87ff679a2f3e71d9181a67b7542122c"
```
Then you open a cookie-based browser (probably webview on android) and authenticate the user agains FB and our Facebook-App. The url is provided by the registration call.
When a redirect happened to our backend and the response is empty (or just a "everything good" message) you have successfuly registere. Don't forget to call auth/fetch as without that call you won't have picture.

### Authentication
For the authentication use HMAC. You have to set two headers: timestamp and hmac.
- timestamp
  - unix timestamp which has to be the same used in the hmac-hash
- hmac
  - 4 fields: time, method, path, md5(content)

example-GET:
```
sha1_hmac($time . "\nget\nuser/current\n" . md5(null) . "\n") => hmac-header
```
example-POST
```
sha1_hmac($time . "\npost\nuser/settings\n" . md5($body) . "\n") => hmac-header
```

### Example
user/current
```
{
    "status": "OK",
    "statusMessage": "All good.",
    "responses": [
        {
            "type": "user",
            "data": {
                "id": "c4ca4238a0b923820dcc509a6f75849b",
                "name": " ... ",
                "lastSeen": 1427442497,
                "isMale": true,
                "isFemale": false,
                "interestedInMale": false,
                "interestedInFemale": true
            }
        },
        {
            "type": "picture",
            "data": {
                "id": "c4ca4238a0b923820dcc509a6f75849b",
                "isDefault": true,
                "time": 0
            }
        },
        {
            "type": "picture",
            "data": {
                "id": "c81e728d9d4c2f636f067f89cc14862c",
                "isDefault": false,
                "time": 0
            }
        },
        {
            "type": "picture",
            "data": {
                "id": "eccbc87e4b5ce2fe28308fd9f2a7baf3",
                "isDefault": false,
                "time": 0
            }
        },
        {
            "type": "picture",
            "data": {
                "id": "a87ff679a2f3e71d9181a67b7542122c",
                "isDefault": false,
                "time": 0
            }
        },
        {
            "type": "picture",
            "data": {
                "id": "e4da3b7fbbce2345d7772b0674a318d5",
                "isDefault": false,
                "time": 0
            }
        },
        {
            "type": "would",
            "data": {
                "idUserWould": "c4ca4238a0b923820dcc509a6f75849b",
                "idUser": "eccbc87e4b5ce2fe28308fd9f2a7baf3",
                "would": true,
                "time": 1427386098
            }
        },
        {
            "type": "match",
            "data": {
                "idUser1": "c4ca4238a0b923820dcc509a6f75849b",
                "idUser2": "eccbc87e4b5ce2fe28308fd9f2a7baf3",
                "time": 1427386098
            }
        }
    ]
}
```
