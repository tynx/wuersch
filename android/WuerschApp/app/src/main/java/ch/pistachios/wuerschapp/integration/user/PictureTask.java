package ch.pistachios.wuerschapp.integration.user;

import android.graphics.Bitmap;
import android.os.AsyncTask;

import ch.pistachios.wuerschapp.integration.GetRequest;
import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.GetResponse;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class PictureTask extends AsyncTask<String, Void, PictureTaskResponse> {

    private String userId;
    private String secret;
    private String randomUserId;

    public PictureTask(String userId, String secret, String randomUserId) {
        this.userId = userId;
        this.secret = secret;
        this.randomUserId = randomUserId;
    }

    @Override
    protected PictureTaskResponse doInBackground(String... strings) {
        GetRequest getRequest = new GetRequest(WuerschURLs.getPicturePath(randomUserId), true, userId, secret);
        GetResponse response = getRequest.getResponse();

        GetRequestStatus status = response.getGetRequestStatus();
        String statusMessage = response.getStatusMessage();

        Bitmap image = null;

        if (response.getGetRequestStatus().isOk()) {
            image = response.getImage();
        }
        return new PictureTaskResponse(status, statusMessage, randomUserId, image);
    }
}
