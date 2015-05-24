package ch.pistachios.wuerschapp.integration;

public enum GetRequestStatus {
    OK, FAIL;

    public boolean isOk() {
        return this == OK;
    }
}
