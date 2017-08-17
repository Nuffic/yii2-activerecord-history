DROP TABLE IF EXISTS "history";
DROP TABLE IF EXISTS "users";
DROP TABLE IF EXISTS "cars";
DROP TABLE IF EXISTS "user_car";


CREATE TABLE history (
    id INTEGER,
    table_name varchar(255) NOT NULL,
    field_id varchar(255) NOT NULL,
    field_name varchar(255) NOT NULL,
    old_value text,
    event integer,
    action_uuid varchar(36) NOT NULL,
    created_at integer,
    PRIMARY KEY (id)
);

CREATE TABLE user (
    id INTEGER,
    name varchar(255) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE car (
    id INTEGER,
    name varchar(255) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE user_car (
    user_id INTEGER,
    car_id INTEGER,
    PRIMARY KEY (user_id, car_id)
);