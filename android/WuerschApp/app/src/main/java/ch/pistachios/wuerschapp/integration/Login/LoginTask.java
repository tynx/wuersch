package ch.pistachios.wuerschapp.integration.login;

import android.os.AsyncTask;

import org.json.JSONException;
import org.json.JSONObject;

import ch.pistachios.wuerschapp.integration.GetRequest;
import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.GetResponse;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class LoginTask extends AsyncTask<String, Void, LoginTaskResponse> {

    private String secret;

    public LoginTask(String secret) {
        this.secret = secret;
    }

    @Override
    protected LoginTaskResponse doInBackground(String... args) {
        GetRequest getRequest = new GetRequest(WuerschURLs.getRegisterPath(secret), false, null, secret);
        GetResponse response = getRequest.getResponse();

        GetRequestStatus status = response.getGetRequestStatus();
        String statusMessage = response.getStatusMessage();
        String id = null;
        String authenticationURL = null;

        if (response.getGetRequestStatus().isOk()) {
            JSONObject data = response.getData();
            try {
                id = data.getString("id");
                authenticationURL = data.getString("authenticationURL");
            } catch (JSONException e) {
                status = GetRequestStatus.FAIL;
                statusMessage = e.getMessage();
            }
        }

        return new LoginTaskResponse(status, statusMessage, id, authenticationURL);
    }
}
