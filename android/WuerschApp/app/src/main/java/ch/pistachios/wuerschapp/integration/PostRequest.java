package ch.pistachios.wuerschapp.integration;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.security.MessageDigest;
import java.util.Date;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

public class PostRequest {

    private static final String HMAC_SHA1_ALGORITHM = "HmacSHA1";

    private String path;
    private boolean requiresAuth;
    private String userId;
    private String data;

    public PostRequest(String path, boolean requiresAuth, String userId, String data) {
        this.path = path;
        this.requiresAuth = requiresAuth;
        this.userId = userId;
        this.data = data;
    }

    //Copied from https://gist.github.com/tistaharahap/1202974
    public static String calculateRFC2104HMAC(String data, String keyString) throws Exception {
        SecretKeySpec key = new SecretKeySpec(keyString.getBytes(), HMAC_SHA1_ALGORITHM);
        Mac mac = Mac.getInstance(HMAC_SHA1_ALGORITHM);
        mac.init(key);

        byte[] bytes = mac.doFinal(data.getBytes());

        //return new String( Base64.encodeToString(bytes, Base64.NO_WRAP) );

        //converting byte array to Hexadecimal String
        StringBuilder sb = new StringBuilder(2 * bytes.length);
        for (byte b : bytes) {
            sb.append(String.format("%02x", b & 0xff));
        }

        return sb.toString();
    }

    //Copied from http://stackoverflow.com/questions/19234734/how-can-i-get-md5-hash-in-java-wich-whould-look-like-afj5fs5h4sd5hb4g8d6s5sb4g
    public static String md5(String message) throws Exception {
        String digest = null;
        //Life's hard...
        if (message == null) {
            return "d41d8cd98f00b204e9800998ecf8427e";
        }
        MessageDigest md = MessageDigest.getInstance("MD5");
        byte[] hash = md.digest(message.getBytes("UTF-8"));

        //converting byte array to Hexadecimal String
        StringBuilder sb = new StringBuilder(2 * hash.length);
        for (byte b : hash) {
            sb.append(String.format("%02x", b & 0xff));
        }

        return sb.toString();
    }

    public GetResponse sendData() {
        String baseUrl = WuerschURLs.getBaseUrl();
        StringBuilder builder = new StringBuilder();
        HttpClient client = new DefaultHttpClient();
        HttpPost httpPost = new HttpPost(baseUrl + this.path);
        if (requiresAuth) {
            addAuthFields(httpPost);
        }
        GetResponse getResponse;
        try {
            JSONObject settings = new JSONObject();
            settings.put("interestedInMale", true);
            settings.put("interestedInFemale", true);
            StringEntity se = new StringEntity(settings.toString());
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
            String md5 = md5(data);
            String hmac = calculateRFC2104HMAC(timestamp + "\n" + "post" + "\n" + this.path + "\n" + md5 + "\n", "secret2");
            httpPost.addHeader("timestamp", timestamp);
            httpPost.addHeader("hmac", userId + ":" + hmac);
        } catch (Exception e) {

        }
    }

}

