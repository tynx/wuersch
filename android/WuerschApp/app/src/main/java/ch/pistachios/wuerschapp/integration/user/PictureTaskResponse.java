package ch.pistachios.wuerschapp.integration.user;

import android.graphics.Bitmap;

import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.TaskResponse;

public class PictureTaskResponse extends TaskResponse {

    private String randomUserId;
    private Bitmap image;

    public PictureTaskResponse(GetRequestStatus status, String statusMessage, String randomUserId, Bitmap image) {
        super(status, statusMessage);
        this.randomUserId = randomUserId;
        this.image = image;
    }

    public Bitmap getImage() {
        return image;
    }

    public String getRandomUserId() {
        return randomUserId;
    }
}
