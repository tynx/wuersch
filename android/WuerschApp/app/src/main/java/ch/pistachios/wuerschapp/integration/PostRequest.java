package ch.pistachios.wuerschapp.integration;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.protocol.HTTP;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Date;

import ch.pistachios.wuerschapp.integration.util.CryptoHelper;

public class PostRequest {

    private static final String HMAC_SHA1_ALGORITHM = "HmacSHA1";

    private String path;
    private boolean requiresAuth;
    private String userId;
    private String secret;
    private String data;

    public PostRequest(String path, boolean requiresAuth, String userId, String secret) {
        this.path = path;
        this.requiresAuth = requiresAuth;
        this.userId = userId;
        this.secret = secret;
    }

    public GetResponse sendData() {
        String baseUrl = WuerschURLs.getBaseUrl();
        StringBuilder builder = new StringBuilder();
        HttpClient client = new DefaultHttpClient();
        HttpPost httpPost = new HttpPost(baseUrl + this.path);

        JSONObject settings = new JSONObject();
        try {
            settings.put("interestedInMale", true);
            settings.put("interestedInFemale", true);
        } catch (JSONException e) {
            e.printStackTrace();
        }
        data = settings.toString();

        if (requiresAuth) {
            addAuthFields(httpPost);
        }
        GetResponse getResponse;
        try {
            StringEntity se = new StringEntity(data);
            se.setContentType(new BasicHeader(HTTP.CONTENT_TYPE, "application/json"));
            httpPost.setEntity(se);

            HttpResponse response = client.execute(httpPost);
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

    private void addAuthFields(HttpPost httpPost) {
        try {
            String timestamp = "" + new Date().getTime();
            String md5 = CryptoHelper.md5(data);
            String hmac = CryptoHelper.calculateRFC2104HMAC(timestamp + "\n" + "post" + "\n" + this.path + "\n" + md5 + "\n", secret);
            httpPost.addHeader("timestamp", timestamp);
            httpPost.addHeader("hmac", userId + ":" + hmac);
        } catch (Exception e) {

        }
    }

}

