package ch.pistachios.wuerschapp.integration_api;

import android.os.AsyncTask;

import java.util.HashMap;
import java.util.Map;

import ch.pistachios.wuerschapp.integration.TaskResponse;
import ch.pistachios.wuerschapp.integration.login.LoginTask;
import ch.pistachios.wuerschapp.integration.login.LoginTaskResponse;
import ch.pistachios.wuerschapp.integration.user.FetchAuthTask;
import ch.pistachios.wuerschapp.integration.user.PictureTask;
import ch.pistachios.wuerschapp.integration.user.PictureTaskResponse;
import ch.pistachios.wuerschapp.integration.user.RandomUserTask;
import ch.pistachios.wuerschapp.integration.user.RandomUserTaskResponse;
import ch.pistachios.wuerschapp.integration.user.SettingsUserTask;

public class UserService {

    private static UserService userService;
    private Map<String, PictureTaskResponse> pictureCache;

    private UserService() {
        //Singleton
        pictureCache = new HashMap<>();
    }

    public static UserService get() {
        if (userService == null) {
            userService = new UserService();
        }

        return userService;
    }

    public void initUser(String userId, String secret) throws Exception {
        //Set interested in male to true;
        TaskResponse settingTaskResponse = new SettingsUserTask(userId, secret).execute().get();
        TaskResponse fetchTaskResponse = null;

        if (settingTaskResponse.getStatus().isOk()) {
            //Fetch the stuff
            fetchTaskResponse = new FetchAuthTask(userId, secret).execute().get();
            if (!fetchTaskResponse.getStatus().isOk()) {
                //Due to debugging reasons this is not active.
                //This call fails if it is called to often...
                //throw new Exception(fetchTaskResponse.getStatusMessage());
            }
        } else {
            throw new Exception(settingTaskResponse.getStatusMessage());
        }

    }

    public PictureTaskResponse getRandomImage(String userId, String secret) throws Exception {
        RandomUserTaskResponse randomUserTaskResponse = new RandomUserTask(userId, secret).execute().get();
        if (randomUserTaskResponse.getStatus().isOk()) {
            String randomUserId = randomUserTaskResponse.getRandomUserId();
            if(!pictureCache.containsKey(randomUserId)) {
                PictureTaskResponse pictureTaskResponse = new PictureTask(userId, secret, randomUserId).execute().get();
                if (!pictureTaskResponse.getStatus().isOk()) {
                    throw new Exception(pictureTaskResponse.getStatusMessage());
                }
                pictureCache.put(randomUserId, pictureTaskResponse);
                return pictureTaskResponse;
            } else {
                return pictureCache.get(randomUserId);
            }

        } else {
            throw new Exception(randomUserTaskResponse.getStatusMessage());
        }
    }

    public LoginTaskResponse getLoginURL(String newSecret) throws Exception {
        AsyncTask<String, Void, LoginTaskResponse> loginTask = new LoginTask(newSecret).execute();
        LoginTaskResponse loginTaskResponse = loginTask.get();
        if (!loginTaskResponse.getStatus().isOk()) {
            throw new Exception(loginTaskResponse.getStatusMessage());
        }
        return loginTaskResponse;
    }
}
