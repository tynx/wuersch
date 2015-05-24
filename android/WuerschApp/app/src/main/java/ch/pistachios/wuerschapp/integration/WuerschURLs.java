package ch.pistachios.wuerschapp.integration;

import com.google.common.base.Joiner;

public class WuerschURLs {
    //URL Parts
    private static final String URL_DELIMITER = "/";
    private static final String PARAM_DELIMITER = "?";
    private static final String BASE_URL = "http://wuersch.pistachios.ch";
    private static final String USER = "user";
    private static final String REGISTER = "register";

    //Params
    private static final String SECRET = "secret=";

    public static String getRegisterURL(String secret) {
        String url = appendURL(BASE_URL, USER, REGISTER);
        return appendParams(url, SECRET + secret);
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
