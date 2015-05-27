package ch.pistachios.wuerschapp.integration.util;

import android.widget.Toast;

import ch.pistachios.wuerschapp.R;

public class ExceptionHelper {

    public static void showExceptionToast(android.content.Context context, android.content.res.Resources resources, Exception e) {
        Toast.makeText(context, resources.getString(R.string.error) + e.getMessage(), Toast.LENGTH_LONG).show();
    }
}
