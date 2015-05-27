package ch.pistachios.wuerschapp.frontend;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import ch.pistachios.wuerschapp.R;
import ch.pistachios.wuerschapp.integration.WuerschURLs;
import ch.pistachios.wuerschapp.integration.login.LoginTaskResponse;
import ch.pistachios.wuerschapp.integration.util.ExceptionHelper;
import ch.pistachios.wuerschapp.integration.util.WuerschConfigValues;
import ch.pistachios.wuerschapp.integration_api.UserService;

public class FacebookLoginActivity extends Activity {

    WebView webView;
    private String userId;
    private String secret;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        initView();

        try {
            String newSecret = getIntent().getExtras().getString(WuerschConfigValues.SECRET);
            LoginTaskResponse loginTaskResponse = UserService.get().getLoginURL(newSecret);
            userId = loginTaskResponse.getId();
            secret = newSecret;
            String authenticationURL = loginTaskResponse.getAuthenticationURL();
            checkIfLoginWasDone(authenticationURL);
            webView.loadUrl(authenticationURL);

        } catch (Exception e) {
            ExceptionHelper.showExceptionToast(getApplicationContext(), getResources(), e);
        }
    }

    private void checkIfLoginWasDone(String url) {
        if (url.startsWith(WuerschURLs.getBaseUrl())) {
            SharedPreferences sharedPreferences = getSharedPreferences(WuerschConfigValues.PREFS_NAME, 0);
            SharedPreferences.Editor editor = sharedPreferences.edit();
            if (userId != null && secret != null) {
                editor.putString(WuerschConfigValues.USER_ID, userId);
                editor.putString(WuerschConfigValues.SECRET, secret);
            }
            editor.apply();
            Intent returnIntent = new Intent();
            setResult(RESULT_OK, returnIntent);
            finish();
        }
    }

    private void initView() {
        setContentView(R.layout.activity_facebook_login);
        webView = (WebView) findViewById(R.id.webView);
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                checkIfLoginWasDone(url);
            }
        });
    }
}
