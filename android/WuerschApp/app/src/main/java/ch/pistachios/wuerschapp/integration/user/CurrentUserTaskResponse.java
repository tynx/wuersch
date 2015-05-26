package ch.pistachios.wuerschapp.integration.user;

import ch.pistachios.wuerschapp.integration.GetRequestStatus;

public class CurrentUserTaskResponse {
    private final GetRequestStatus status;
    private final String statusMessage;

    public CurrentUserTaskResponse(GetRequestStatus status, String statusMessage) {
        this.status = status;
        this.statusMessage = statusMessage;
    }

    public GetRequestStatus getStatus() {
        return status;
    }

    public String getStatusMessage() {
        return statusMessage;
    }
}
