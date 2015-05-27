package ch.pistachios.wuerschapp.integration.login;


import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.TaskResponse;

public class LoginTaskResponse extends TaskResponse {
    private final String id;
    private final String authenticationURL;

    public LoginTaskResponse(GetRequestStatus status, String statusMessage, String id, String authenticationURL) {
        super(status, statusMessage);
        this.id = id;
        this.authenticationURL = authenticationURL;
    }

    public String getId() {
        return id;
    }

    public String getAuthenticationURL() {
        return authenticationURL;
    }
}
