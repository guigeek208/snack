CREATE TABLE raduser (
    id int(11) unsigned not null auto_increment,
    username varchar(64) NOT NULL default '',
    admin boolean default '0',
    cert_path varchar(255),
    comment text,
    PRIMARY KEY (id),
    KEY username (username(32))
);
