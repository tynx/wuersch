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
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.Toast;

import ch.pistachios.wuerschapp.R;
import ch.pistachios.wuerschapp.integration.user.PictureTaskResponse;
import ch.pistachios.wuerschapp.integration.util.ExceptionHelper;
import ch.pistachios.wuerschapp.integration.util.WuerschConfigValues;
import ch.pistachios.wuerschapp.integration_api.UserService;

public class WurschActivity extends Activity {

    private ImageView wuerschImage;
    private ImageButton wuerschYes;
    private ImageButton wuerschNo;
    private String randomUserId;
    private String userId;
    private String secret;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        initView();

        try {
            SharedPreferences sharedPreferences = getSharedPreferences(WuerschConfigValues.PREFS_NAME, 0);
            userId = sharedPreferences.getString(WuerschConfigValues.USER_ID, null);
            secret = sharedPreferences.getString(WuerschConfigValues.SECRET, null);

            if (!isDeviceOnline()) {
                Toast.makeText(getApplicationContext(), getResources().getString(R.string.error) + getResources().getString(R.string.error_no_internet), Toast.LENGTH_LONG).show();
            } else if (userId == null) {
                Intent i = new Intent(getApplicationContext(), LoginScreen.class);
                startActivity(i);
            } else {
                showNextRandomUser();
            }
        } catch (Exception e) {
            ExceptionHelper.showExceptionToast(getApplicationContext(), getResources(), e);
        }
    }

    @Override
    public void onBackPressed() {
        //No back navigation needed
    }

    private boolean isDeviceOnline() {
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

    private void initView() {
        setContentView(R.layout.activity_wursch);

        wuerschImage = (ImageView) findViewById(R.id.wuerschImage);
        wuerschYes = (ImageButton) findViewById(R.id.wuersch_yes);
        wuerschNo = (ImageButton) findViewById(R.id.wuersch_no);

        wuerschYes.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                try {
                    //Send would to the server, but due to few test account we do not send this to the backend
                    //(You can only would/would_not a person once!)
                    showNextRandomUser();
                } catch (Exception e) {
                    ExceptionHelper.showExceptionToast(getApplicationContext(), getResources(), e);
                }
            }
        });

        wuerschNo.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                try {
                    //Send would_not to the server, but due to few test account we do not send this to the backend
                    //(You can only would/would_not a person once!)
                    showNextRandomUser();
                } catch (Exception e) {
                    ExceptionHelper.showExceptionToast(getApplicationContext(), getResources(), e);
                }
            }
        });
    }
}
