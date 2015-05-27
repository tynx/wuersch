package ch.pistachios.wuerschapp.integration.util;

import java.security.MessageDigest;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

public class CryptoHelper {

    private static final String HMAC_SHA1_ALGORITHM = "HmacSHA1";

    //Copied from https://gist.github.com/tistaharahap/1202974
    public static String calculateRFC2104HMAC(String data, String keyString) throws Exception {
        SecretKeySpec key = new SecretKeySpec(keyString.getBytes(), HMAC_SHA1_ALGORITHM);
        Mac mac = Mac.getInstance(HMAC_SHA1_ALGORITHM);
        mac.init(key);

        byte[] bytes = mac.doFinal(data.getBytes());

        //return new String( Base64.encodeToString(bytes, Base64.NO_WRAP) );

        //converting byte array to Hexadecimal String
        StringBuilder sb = new StringBuilder(2 * bytes.length);
        for (byte b : bytes) {
            sb.append(String.format("%02x", b & 0xff));
        }

        return sb.toString();
    }

    //Copied from http://stackoverflow.com/questions/19234734/how-can-i-get-md5-hash-in-java-wich-whould-look-like-afj5fs5h4sd5hb4g8d6s5sb4g
    public static String md5(String message) throws Exception {
        //Life's hard...
        if (message == null) {
            return "d41d8cd98f00b204e9800998ecf8427e";
        }
        MessageDigest md = MessageDigest.getInstance("MD5");
        byte[] hash = md.digest(message.getBytes("UTF-8"));

        //converting byte array to Hexadecimal String
        StringBuilder sb = new StringBuilder(2 * hash.length);
        for (byte b : hash) {
            sb.append(String.format("%02x", b & 0xff));
        }

        return sb.toString();
    }
}
