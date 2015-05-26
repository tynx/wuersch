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
import java.util.Date;

import ch.pistachios.wuerschapp.integration.util.CryptoHelper;

public class GetRequest {

    private String path;
    private boolean requiresAuth;
    private String userId;
    private String secret;

    public GetRequest(String path, boolean requiresAuth, String userId, String secret) {
        this.path = path;
        this.requiresAuth = requiresAuth;
        this.userId = userId;
        this.secret = secret;
    }

    public GetResponse getResponse() {
        String baseUrl = WuerschURLs.getBaseUrl();
        StringBuilder builder = new StringBuilder();
        HttpClient client = new DefaultHttpClient();
        HttpGet httpGet = new HttpGet(baseUrl + this.path);
        if (requiresAuth) {
            addAuthFields(httpGet);
        }
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
                JSONObject data = null;
                if (responses.length() > 0) {
                    data = ((JSONObject) responses.get(0)).getJSONObject("data");
                }

                getResponse = new GetResponse(getRequestStatus, statusMessage, data);

            } else {
                getResponse = new GetResponse(GetRequestStatus.FAIL, statusLine.getReasonPhrase(), null);
            }
        } catch (Exception e) {
            getResponse = new GetResponse(GetRequestStatus.FAIL, e.getMessage(), null);
        }
        return getResponse;
    }

    private void addAuthFields(HttpGet httpGet) {
        try {
            String timestamp = "" + new Date().getTime();
            String md5 = CryptoHelper.md5(null);
            String hmac = CryptoHelper.calculateRFC2104HMAC(timestamp + "\n" + "get" + "\n" + this.path + "\n" + md5 + "\n", secret);
            httpGet.addHeader("timestamp", timestamp);
            httpGet.addHeader("hmac", userId + ":" + hmac);
        } catch (Exception e) {

        }
    }

}

