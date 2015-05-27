package ch.pistachios.wuerschapp.integration.user;

import android.os.AsyncTask;

import org.json.JSONException;
import org.json.JSONObject;

import ch.pistachios.wuerschapp.integration.GetRequest;
import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.GetResponse;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class RandomUserTask extends AsyncTask<String, Void, RandomUserTaskResponse> {

    private String userId;
    private String secret;

    public RandomUserTask(String userId, String secret) {
        this.userId = userId;
        this.secret = secret;
    }

    @Override
    protected RandomUserTaskResponse doInBackground(String... strings) {
        GetRequest getRequest = new GetRequest(WuerschURLs.getRandomUserPath(), true, userId, secret);
        GetResponse response = getRequest.getResponse();

        GetRequestStatus status = response.getGetRequestStatus();
        String statusMessage = response.getStatusMessage();

        String randomUserId = null;
        if (response.getGetRequestStatus().isOk()) {
            JSONObject data = response.getData();
            try {
                randomUserId = data.getString("id");
            } catch (JSONException e) {
                status = GetRequestStatus.FAIL;
                statusMessage = e.getMessage();
            }
        }
        return new RandomUserTaskResponse(status, statusMessage, randomUserId);
    }
}
