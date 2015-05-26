package ch.pistachios.wuerschapp.frontend;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.widget.Toast;

import ch.pistachios.wuerschapp.R;
import ch.pistachios.wuerschapp.integration.user.CurrentUserTask;
import ch.pistachios.wuerschapp.integration.user.CurrentUserTaskResponse;
import ch.pistachios.wuerschapp.integration.user.FetchAuthTask;
import ch.pistachios.wuerschapp.integration.user.SettingsUserTask;

public class WurschActivity extends Activity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_wursch);

        SharedPreferences sharedPreferences = getSharedPreferences(LoginScreen.PREFS_NAME, 0);
        String userId = sharedPreferences.getString("userId", null);
        String secret = sharedPreferences.getString("secret", null);

        if (!isDeviceOnline()) {
            Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + "Gerät ist offline, bitte prüfe die Internet-Verbindung!", Toast.LENGTH_LONG).show();
        } else if (userId == null) {
            Intent i = new Intent(getApplicationContext(), LoginScreen.class);
            startActivity(i);
        } else {

            new SettingsUserTask(userId).execute();
            AsyncTask<String, Void, CurrentUserTaskResponse> currentUserTask = new CurrentUserTask(userId, secret).execute();
            try {
                CurrentUserTaskResponse loginTaskResponse = currentUserTask.get();
                if (loginTaskResponse.getStatus().isOk()) {
                    new FetchAuthTask(userId, secret).execute();
                } else {
                    Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + loginTaskResponse.getStatusMessage(), Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    public boolean isDeviceOnline() {
        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        return netInfo != null && netInfo.isConnectedOrConnecting();
    }
}
