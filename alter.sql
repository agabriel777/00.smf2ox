ALTER TABLE smf_members ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_boards ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_categories ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_topics ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE smf_messages ADD `ow_id` MEDIUMINT(11) DEFAULT NULL;
ALTER TABLE ow_forum_read_topic ADD `postId` MEDIUMINT(11) DEFAULT NULL;

-- corectii

UPDATE smf_members 
SET email_address = CONCAT(member_name,"@vaspun.eu")
WHERE email_address = '' OR member_name = '3D'


UPDATE smf_topics
SET id_member_started = (SELECT id_member
FROM smf_messages m 
WHERE m.id_topic=id_topic
ORDER BY id_msg ASC
LIMIT 1)
WHERE id_member_started=0


-- dupa import




