package ch.pistachios.wuerschapp.integration;

import android.graphics.Bitmap;

import org.json.JSONObject;

public class GetResponse {
    private final GetRequestStatus getRequestStatus;
    private final String statusMessage;
    private final JSONObject data;
    private final Bitmap image;

    public GetResponse(GetRequestStatus getRequestStatus, String statusMessage, JSONObject data, Bitmap image) {
        this.getRequestStatus = getRequestStatus;
        this.statusMessage = statusMessage;
        this.data = data;
        this.image = image;
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

    public Bitmap getImage() {
        return image;
    }
}
