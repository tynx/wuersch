package ch.pistachios.wuerschapp;

import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import ch.pistachios.wuerschapp.integration.Login.LoginTask;
import ch.pistachios.wuerschapp.integration.Login.LoginTaskResponse;

public class LoginScreen extends FragmentActivity {

    private Button loginButtonDirect;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_screen);

        loginButtonDirect = (Button) findViewById(R.id.login_button_direct);
        loginButtonDirect.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

            }
        });

        //First check if internet works
        if (!isDeviceOnline()) {
            showNoInternetDialog();
            loginButtonDirect.setEnabled(false);
        } else {

            //Get login URL
            AsyncTask<String, Void, LoginTaskResponse> loginTask = new LoginTask("secret").execute();
            try {
                LoginTaskResponse loginTaskResponse = loginTask.get();
                if (loginTaskResponse.getStatus().isOk()) {

                } else {
                    Toast.makeText(getApplicationContext(), R.string.error + loginTaskResponse.getStatusMessage(), Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
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
