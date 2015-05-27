package ch.pistachios.wuerschapp.frontend;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import java.util.Date;

import ch.pistachios.wuerschapp.R;
import ch.pistachios.wuerschapp.integration.util.CryptoHelper;
import ch.pistachios.wuerschapp.integration.util.ExceptionHelper;
import ch.pistachios.wuerschapp.integration.util.WuerschConfigValues;
import ch.pistachios.wuerschapp.integration_api.UserService;


public class LoginScreen extends FragmentActivity {


    private Button loginButtonDirect;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_screen);

        SharedPreferences sharedPreferences = getSharedPreferences(WuerschConfigValues.PREFS_NAME, 0);
        String userId = sharedPreferences.getString(WuerschConfigValues.USER_ID, null);

        loginButtonDirect = (Button) findViewById(R.id.login_button_direct);
        loginButtonDirect.setEnabled(false);
        loginButtonDirect.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(getApplicationContext(), FacebookLoginActivity.class);
                String secret = getNewSecret();
                i.putExtra(WuerschConfigValues.SECRET, secret);
                startActivityForResult(i, 1);
            }
        });

        //First check if internet works
        if (!isDeviceOnline()) {
            showNoInternetDialog();
        } else if (userId != null) {
            Intent i = new Intent(getApplicationContext(), WurschActivity.class);
            startActivity(i);
        } else {
            loginButtonDirect.setEnabled(true);
        }

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        try {
            SharedPreferences sharedPreferences = getSharedPreferences(WuerschConfigValues.PREFS_NAME, 0);
            String userId = sharedPreferences.getString(WuerschConfigValues.USER_ID, null);
            String secret = sharedPreferences.getString(WuerschConfigValues.SECRET, null);

            if (userId != null && secret != null) {
                UserService.get().initUser(userId, secret);
                Intent i = new Intent(getApplicationContext(), WurschActivity.class);
                startActivity(i);
            } else {
                Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + getResources().getString(R.string.error_login), Toast.LENGTH_LONG).show();
            }
        } catch (Exception e) {
            ExceptionHelper.showExceptionToast(getApplicationContext(), getResources(), e);
        }
    }

    private void showNoInternetDialog() {
        FragmentManager fm = getSupportFragmentManager();
        NoInternetDialog noInternetDialog = new NoInternetDialog();
        noInternetDialog.show(fm, WuerschConfigValues.FRAGMENT_NO_INTERNET);
    }

    public boolean isDeviceOnline() {
        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        return netInfo != null && netInfo.isConnectedOrConnecting();
    }

    public String getNewSecret() {
        try {
            return CryptoHelper.md5("" + new Date().getTime());
        } catch (Exception e) {
            e.printStackTrace();
        }

        return "" + new Date().getTime();
    }
}
