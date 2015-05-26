package ch.pistachios.wuerschapp.integration.user;

import android.os.AsyncTask;

import org.json.JSONObject;

import ch.pistachios.wuerschapp.integration.GetRequest;
import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.GetResponse;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class RandomUserTask extends AsyncTask<String, Void, CurrentUserTaskResponse> {

    private String userId;
    private String secret;

    public RandomUserTask(String userId, String secret) {
        this.userId = userId;
        this.secret = secret;
    }

    @Override
    protected CurrentUserTaskResponse doInBackground(String... strings) {
        GetRequest getRequest = new GetRequest(WuerschURLs.getRandomUserPath(), true, userId, secret);
        GetResponse response = getRequest.getResponse();

        GetRequestStatus status = response.getGetRequestStatus();
        String statusMessage = response.getStatusMessage();
        //String id = null;
        //String authenticationURL = null;

        if (response.getGetRequestStatus().isOk()) {
            JSONObject data = response.getData();
            //try {
            //id = data.getString("id");
            //authenticationURL = data.getString("authenticationURL");
            //} catch (JSONException e) {
            //    status = GetRequestStatus.FAIL;
            //    statusMessage = e.getMessage();
            //}
        }
        return new CurrentUserTaskResponse(status, statusMessage);
    }
}
