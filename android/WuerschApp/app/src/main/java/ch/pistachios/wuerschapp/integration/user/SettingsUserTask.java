package ch.pistachios.wuerschapp.integration.user;

import android.os.AsyncTask;

import ch.pistachios.wuerschapp.integration.GetResponse;
import ch.pistachios.wuerschapp.integration.PostRequest;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class SettingsUserTask extends AsyncTask<String, Void, CurrentUserTaskResponse> {
    private String userId;
    private String secret;

    public SettingsUserTask(String userId, String secret) {
        this.userId = userId;
        this.secret = secret;
    }

    @Override
    protected CurrentUserTaskResponse doInBackground(String... strings) {
        PostRequest postRequest = new PostRequest(WuerschURLs.getSettingsUserPath(), true, userId, secret);
        GetResponse response = postRequest.sendData();
        return null;
    }

}
