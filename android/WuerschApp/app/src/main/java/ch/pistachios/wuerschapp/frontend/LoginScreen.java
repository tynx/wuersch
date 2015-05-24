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
                startActivityForResult(i, 1);
            }
        });

        //First check if internet works
        if (!isDeviceOnline()) {
            showNoInternetDialog();
        } else {
            loginButtonDirect.setEnabled(true);
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        SharedPreferences sharedPreferences = getSharedPreferences(LoginScreen.PREFS_NAME, 0);
        String userId = sharedPreferences.getString("userId", null);
        Toast.makeText(getApplicationContext(), "Yes!!!: " + userId, Toast.LENGTH_LONG).show();
    }

    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);

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
