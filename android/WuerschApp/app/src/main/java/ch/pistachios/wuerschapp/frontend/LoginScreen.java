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

import ch.pistachios.wuerschapp.R;


public class LoginScreen extends FragmentActivity {

    public static final String PREFS_NAME = "wuersch_global_prefs";
    private Button loginButtonDirect;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_screen);

        loginButtonDirect = (Button) findViewById(R.id.login_button_direct);
        loginButtonDirect.setEnabled(false);
        loginButtonDirect.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(getApplicationContext(), FacebookLoginActivity.class);
                i.putExtra("secret", "secret");
                startActivityForResult(i, 1);
            }
        });

        SharedPreferences sharedPreferences = getSharedPreferences(LoginScreen.PREFS_NAME, 0);
        String userId = sharedPreferences.getString("userId", null);

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
}
