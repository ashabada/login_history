CREATE TABLE login_logs (
    username VARCHAR(255) NOT NULL,
    remoteip VARCHAR(40) NOT NULL,
    remotedns VARCHAR(255) NOT NULL,
    logintime timestamp NOT NULL
);

