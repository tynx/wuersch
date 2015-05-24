package ch.pistachios.wuerschapp.integration;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;

public class GetRequest {

    private String url;

    public GetRequest(String url) {
        this.url = url;
    }

    public GetResponse getResponse() {
        StringBuilder builder = new StringBuilder();
        HttpClient client = new DefaultHttpClient();
        HttpGet httpGet = new HttpGet(this.url);
        GetResponse getResponse;
        try {
            HttpResponse response = client.execute(httpGet);
            StatusLine statusLine = response.getStatusLine();
            int statusCode = statusLine.getStatusCode();
            if (statusCode == 200) {
                HttpEntity entity = response.getEntity();
                InputStream content = entity.getContent();
                BufferedReader reader = new BufferedReader(new InputStreamReader(content));
                String line;
                while ((line = reader.readLine()) != null) {
                    builder.append(line);
                }
                JSONObject responseObject = new JSONObject(builder.toString());
                GetRequestStatus getRequestStatus = GetRequestStatus.valueOf(responseObject.getString("status"));
                String statusMessage = responseObject.getString("statusMessage");
                JSONArray responses = responseObject.getJSONArray("responses");
                JSONObject data = ((JSONObject) responses.get(0)).getJSONObject("data");

                getResponse = new GetResponse(getRequestStatus, statusMessage, data);

            } else {
                getResponse = new GetResponse(GetRequestStatus.FAIL, statusLine.getReasonPhrase(), null);
            }
        } catch (Exception e) {
            getResponse = new GetResponse(GetRequestStatus.FAIL, e.getMessage(), null);
        }
        return getResponse;
    }
}
