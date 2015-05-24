package ch.pistachios.wuerschapp.frontend;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import ch.pistachios.wuerschapp.R;
import ch.pistachios.wuerschapp.integration.Login.LoginTask;
import ch.pistachios.wuerschapp.integration.Login.LoginTaskResponse;
import ch.pistachios.wuerschapp.integration.WuerschURLs;

public class FacebookLoginActivity extends Activity {

    WebView webView;
    private String userId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_facebook_login);
        webView = (WebView) findViewById(R.id.webView);
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                checkIfLoginWasDone(url);
            }
        });

        //Get login URL
        AsyncTask<String, Void, LoginTaskResponse> loginTask = new LoginTask("secret").execute();
        try {
            LoginTaskResponse loginTaskResponse = loginTask.get();
            if (loginTaskResponse.getStatus().isOk()) {
                userId = loginTaskResponse.getId();
                String authenticationURL = loginTaskResponse.getAuthenticationURL();
                checkIfLoginWasDone(authenticationURL);
                webView.loadUrl(authenticationURL);

            } else {
                Toast.makeText(getApplicationContext(), R.string.error + loginTaskResponse.getStatusMessage(), Toast.LENGTH_LONG).show();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void checkIfLoginWasDone(String url) {
        if (url.startsWith(WuerschURLs.getBaseUrl())) {
            SharedPreferences sharedPreferences = getSharedPreferences(LoginScreen.PREFS_NAME, 0);
            SharedPreferences.Editor editor = sharedPreferences.edit();
            editor.putString("userId", userId);
            editor.apply();
            Intent returnIntent = new Intent();
            setResult(RESULT_OK, returnIntent);
            finish();
        }
    }
}
