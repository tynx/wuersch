package ch.pistachios.wuerschapp.integration;

public class TaskResponse {
    private final GetRequestStatus status;
    private final String statusMessage;

    public TaskResponse(GetRequestStatus status, String statusMessage) {
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
