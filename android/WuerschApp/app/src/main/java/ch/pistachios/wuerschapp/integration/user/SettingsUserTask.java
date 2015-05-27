package ch.pistachios.wuerschapp.integration.user;

import android.os.AsyncTask;

import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.GetResponse;
import ch.pistachios.wuerschapp.integration.PostRequest;
import ch.pistachios.wuerschapp.integration.TaskResponse;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class SettingsUserTask extends AsyncTask<String, Void, TaskResponse> {
    private String userId;
    private String secret;

    public SettingsUserTask(String userId, String secret) {
        this.userId = userId;
        this.secret = secret;
    }

    @Override
    protected TaskResponse doInBackground(String... strings) {
        PostRequest postRequest = new PostRequest(WuerschURLs.getSettingsUserPath(), true, userId, secret);
        GetResponse response = postRequest.sendData();

        GetRequestStatus status = response.getGetRequestStatus();
        String statusMessage = response.getStatusMessage();

        return new TaskResponse(status, statusMessage);
    }

}
