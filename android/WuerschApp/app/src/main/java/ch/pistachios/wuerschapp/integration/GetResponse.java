package ch.pistachios.wuerschapp.integration;

import org.json.JSONObject;

public class GetResponse {
    private final GetRequestStatus getRequestStatus;
    private final String statusMessage;
    private final JSONObject data;

    public GetResponse(GetRequestStatus getRequestStatus, String statusMessage, JSONObject data) {
        this.getRequestStatus = getRequestStatus;
        this.statusMessage = statusMessage;
        this.data = data;
    }

    public GetRequestStatus getGetRequestStatus() {
        return getRequestStatus;
    }

    public String getStatusMessage() {
        return statusMessage;
    }

    public JSONObject getData() {
        return data;
    }
}
