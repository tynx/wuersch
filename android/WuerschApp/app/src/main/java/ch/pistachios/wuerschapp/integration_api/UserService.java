package ch.pistachios.wuerschapp.integration_api;

import android.os.AsyncTask;

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

    private UserService() {
        //Singleton
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
            PictureTaskResponse pictureTaskResponse = new PictureTask(userId, secret, randomUserId).execute().get();
            if (!pictureTaskResponse.getStatus().isOk()) {
                throw new Exception(pictureTaskResponse.getStatusMessage());
            }

            return pictureTaskResponse;
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
