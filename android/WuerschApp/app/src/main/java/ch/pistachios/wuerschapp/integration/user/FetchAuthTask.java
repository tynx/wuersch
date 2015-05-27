package ch.pistachios.wuerschapp.integration.user;

import android.os.AsyncTask;

import ch.pistachios.wuerschapp.integration.GetRequest;
import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.GetResponse;
import ch.pistachios.wuerschapp.integration.TaskResponse;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class FetchAuthTask extends AsyncTask<String, Void, TaskResponse> {

    private String userId;
    private String secret;

    public FetchAuthTask(String userId, String secret) {
        this.userId = userId;
        this.secret = secret;
    }

    @Override
    protected TaskResponse doInBackground(String... strings) {
        GetRequest getRequest = new GetRequest(WuerschURLs.getFetchAuthUserPath(), true, userId, secret);
        GetResponse response = getRequest.getResponse();

        GetRequestStatus status = response.getGetRequestStatus();
        String statusMessage = response.getStatusMessage();

        return new TaskResponse(status, statusMessage);
    }
}
