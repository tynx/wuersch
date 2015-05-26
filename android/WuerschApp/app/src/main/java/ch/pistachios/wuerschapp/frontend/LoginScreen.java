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
import ch.pistachios.wuerschapp.integration.user.FetchAuthTask;
import ch.pistachios.wuerschapp.integration.user.SettingsUserTask;
import ch.pistachios.wuerschapp.integration.util.CryptoHelper;


public class LoginScreen extends FragmentActivity {

    public static final String PREFS_NAME = "wuersch_global_prefs";
    private Button loginButtonDirect;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_screen);

        SharedPreferences sharedPreferences = getSharedPreferences(LoginScreen.PREFS_NAME, 0);
        String userId = sharedPreferences.getString("userId", null);

        loginButtonDirect = (Button) findViewById(R.id.login_button_direct);
        loginButtonDirect.setEnabled(false);
        loginButtonDirect.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(getApplicationContext(), FacebookLoginActivity.class);
                String secret = getNewSecret();
                i.putExtra("secret", secret);
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

        SharedPreferences sharedPreferences = getSharedPreferences(LoginScreen.PREFS_NAME, 0);
        String userId = sharedPreferences.getString("userId", null);
        String secret = sharedPreferences.getString("secret", null);

        //Set interested in male and female to true;
        new SettingsUserTask(userId, secret).execute();
        //Fetch the stuff
        new FetchAuthTask(userId, secret).execute();

        if (userId != null && secret != null) {
            Intent i = new Intent(getApplicationContext(), WurschActivity.class);
            startActivity(i);
        } else {
            Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + "Login fehlgeschlagen, bitte versuche es erneut!", Toast.LENGTH_LONG).show();
        }
    }

    private void showNoInternetDialog() {
        FragmentManager fm = getSupportFragmentManager();
        NoInternetDialog noInternetDialog = new NoInternetDialog();
        noInternetDialog.show(fm, "fragment_no_internet");
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
