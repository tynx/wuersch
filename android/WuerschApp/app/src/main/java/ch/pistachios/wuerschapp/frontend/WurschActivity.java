package ch.pistachios.wuerschapp.frontend;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.Toast;

import ch.pistachios.wuerschapp.R;
import ch.pistachios.wuerschapp.integration.user.PictureTaskResponse;
import ch.pistachios.wuerschapp.integration_api.UserService;

public class WurschActivity extends Activity {

    private ImageView wuerschImage;
    private Button wuerschYes;
    private Button wuerschNo;
    private String randomUserId;
    private String userId;
    private String secret;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_wursch);

        wuerschImage = (ImageView) findViewById(R.id.wuerschImage);
        wuerschYes = (Button) findViewById(R.id.wuersch_yes);
        wuerschNo = (Button) findViewById(R.id.wuersch_no);

        wuerschYes.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                try {
                    showNextRandomUser();
                } catch (Exception e) {
                    Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + e.getMessage(), Toast.LENGTH_LONG).show();
                }
            }
        });

        wuerschNo.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                try {
                    showNextRandomUser();
                } catch (Exception e) {
                    Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + e.getMessage(), Toast.LENGTH_LONG).show();
                }
            }
        });

        try {
            SharedPreferences sharedPreferences = getSharedPreferences(LoginScreen.PREFS_NAME, 0);
            userId = sharedPreferences.getString("userId", null);
            secret = sharedPreferences.getString("secret", null);

            if (!isDeviceOnline()) {
                Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + "Gerät ist offline, bitte prüfe die Internet-Verbindung!", Toast.LENGTH_LONG).show();
            } else if (userId == null) {
                Intent i = new Intent(getApplicationContext(), LoginScreen.class);
                startActivity(i);
            } else {
                showNextRandomUser();
            }
        } catch (Exception e) {
            Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + e.getMessage(), Toast.LENGTH_LONG).show();
        }
    }

    public boolean isDeviceOnline() {
        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        return netInfo != null && netInfo.isConnectedOrConnecting();
    }

    private void showNextRandomUser() throws Exception {
        PictureTaskResponse randomImageResponse = UserService.get().getRandomImage(userId, secret);
        randomUserId = randomImageResponse.getRandomUserId();
        Bitmap image = randomImageResponse.getImage();
        wuerschImage.setImageBitmap(image);
        wuerschImage.invalidate();
    }
}
