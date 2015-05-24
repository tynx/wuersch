package ch.pistachios.wuerschapp.integration.Login;


import ch.pistachios.wuerschapp.integration.GetRequestStatus;

public class LoginTaskResponse {
    private final GetRequestStatus status;
    private final String statusMessage;
    private final String id;
    private final String authenticationURL;

    public LoginTaskResponse(GetRequestStatus status, String statusMessage, String id, String authenticationURL) {
        this.status = status;
        this.statusMessage = statusMessage;
        this.id = id;
        this.authenticationURL = authenticationURL;
    }

    public GetRequestStatus getStatus() {
        return status;
    }

    public String getStatusMessage() {
        return statusMessage;
    }

    public String getId() {
        return id;
    }

    public String getAuthenticationURL() {
        return authenticationURL;
    }
}
