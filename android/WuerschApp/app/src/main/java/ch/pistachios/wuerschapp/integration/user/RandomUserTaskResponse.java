package ch.pistachios.wuerschapp.integration.user;

import ch.pistachios.wuerschapp.integration.GetRequestStatus;
import ch.pistachios.wuerschapp.integration.TaskResponse;

public class RandomUserTaskResponse extends TaskResponse {

    private String randomUserId;

    public RandomUserTaskResponse(GetRequestStatus status, String statusMessage, String randomUserId) {
        super(status, statusMessage);
        this.randomUserId = randomUserId;
    }

    public String getRandomUserId() {
        return randomUserId;
    }
}
