CREATE TABLE user (
       id_user	INTEGER AUTOINC NOT NULL,
       email		VARCHAR(255) NOT NULL,
       name	VARCHAR(255) NOT NULL,
       regdate	DATE,
       pwd		VARCHAR(32),
       PRIMARY KEY(id_user)
);

CREATE TABLE groups (
       id_user              INTEGER NOT NULL,
       group_name      VARCHAR(255),
       id_group             INTEGER AUTOINC NOT NULL,
       FOREIGN KEY(id_user) REFERENCES user(id_user) ON DELETE RESTRICT,
       PRIMARY KEY (id_user, id_group)
);

CREATE TABLE link (
       id_link	INTEGER AUTOINC NOT NULL,
       id_user	INTEGER NOT NULL,
       id_group	INTEGER,
       name	VARCHAR(255),
       url		VARCHAR(255) NOT NULL,
       link_date	DATE,
       FOREIGN KEY(id_user) REFERENCES user(id_user) ON DELETE RESTRICT,
       FOREIGN KEY(id_group) REFERENCES groups(id_group) ON DELETE SET NULL,
       PRIMARY KEY(id_link)
);

CREATE TABLE tags (
       id_tag	INTEGER AUTOINC NOT NULL,
       tag		VARCHAR(255),
       PRIMARY KEY(id_tag)
);

CREATE TABLE link_tag (
       id_link	INTEGER NOT NULL,
       id_tag	INTEGER NOT NULL,
       id_user	INTEGER NOT NULL,
       FOREIGN KEY(id_link) REFERENCES link(id_link) ON DELETE RESTRICT,
       FOREIGN KEY(id_user) REFERENCES link(id_user) ON DELETE RESTRICT,
       FOREIGN KEY(id_tag) REFERENCES tags(id_tag) ON DELETE RESTRICT,
       PRIMARY KEY(id_link, id_tag, id_user)
);

-- CREATE UNIQUE INDEX XPKgroups ON groups (        id_user,        id_group );