package ch.pistachios.wuerschapp.integration;

import com.google.common.base.Joiner;

public class WuerschURLs {
    //URL Parts
    private static final String URL_DELIMITER = "/";
    private static final String PARAM_DELIMITER = "?";
    private static final String BASE_URL = "http://wuersch.pistachios.ch/";

    private static final String USER = "user";
    private static final String REGISTER = "register";
    private static final String CURRENT = "current";
    private static final String SETTINGS = "settings";
    private static final String RANDOM = "random";

    private static final String AUTH = "auth";
    private static final String FETCH = "fetch";


    //Params
    private static final String SECRET = "secret=";

    public static String getBaseUrl() {
        return BASE_URL;
    }

    public static String getRegisterPath(String secret) {
        String path = appendURL(USER, REGISTER);
        return appendParams(path, SECRET + secret);
    }

    public static String getCurrentUserPath() {
        return appendURL(USER, CURRENT);
    }

    public static String getSettingsUserPath() {
        return appendURL(USER, SETTINGS);
    }

    public static String getFetchAuthUserPath() {
        return appendURL(AUTH, FETCH);
    }

    public static String getRandomUserPath() {
        return appendURL(USER, RANDOM);
    }

    private static String appendURL(String... args) {
        Joiner joiner = Joiner.on(URL_DELIMITER).skipNulls();
        return joiner.join(args);
    }

    private static String appendParams(String... args) {
        Joiner joiner = Joiner.on(PARAM_DELIMITER).skipNulls();
        return joiner.join(args);
    }
}
